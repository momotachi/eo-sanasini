import { notFound } from "next/navigation";
import { prisma } from "@/lib/prisma";
import {
  statusConfig,
  typeConfig,
  formatDateRange,
} from "@/lib/event";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { DivisionList } from "@/components/event/division-list";
import { ScheduleTimeline } from "@/components/event/schedule-timeline";
import { MedalTable, type ContingentStanding } from "@/components/event/medal-table";
import {
  Calendar,
  MapPin,
  Phone,
  Mail,
  Users,
  Trophy,
  Clock,
  Target,
} from "lucide-react";

// hitung klasemen kontingen dari medali yang sudah ada
async function getStandings(eventId: string): Promise<ContingentStanding[]> {
  const medals = await prisma.medal.findMany({
    where: { eventId, contingentId: { not: null } },
    include: { contingent: true },
  });

  // kelompokkan per kontingen
  const map = new Map<string, ContingentStanding>();
  for (const m of medals) {
    if (!m.contingentId || !m.contingent) continue;
    const existing = map.get(m.contingentId);
    if (!existing) {
      map.set(m.contingentId, {
        contingent: { id: m.contingent.id, name: m.contingent.name, logoUrl: m.contingent.logoUrl },
        gold: 0,
        silver: 0,
        bronze: 0,
        total: 0,
        rank: 0,
      });
    }
    const entry = map.get(m.contingentId)!;
    if (m.type === "GOLD") entry.gold++;
    if (m.type === "SILVER") entry.silver++;
    if (m.type === "BRONZE") entry.bronze++;
    entry.total++;
  }

  // ranking: Gold > Silver > Bronze > Total
  const arr = Array.from(map.values()).sort(
    (a, b) =>
      b.gold - a.gold ||
      b.silver - a.silver ||
      b.bronze - a.bronze ||
      b.total - a.total
  );
  arr.forEach((s, i) => (s.rank = i + 1));
  return arr;
}

export default async function EventDetailPage({
  params,
}: {
  params: { slug: string };
}) {
  const event = await prisma.event.findUnique({
    where: { slug: params.slug, isPublic: true },
    include: {
      config: true,
      divisions: { orderBy: [{ discipline: "asc" }, { ageCategory: "asc" }] },
      schedule: { orderBy: { time: "asc" } },
      contingents: true,
    },
  });

  if (!event) notFound();

  const [standings, participantCount, matchCount] = await Promise.all([
    getStandings(event.id),
    prisma.participant.count({
      where: { division: { eventId: event.id }, status: "APPROVED" },
    }),
    prisma.match.count({ where: { division: { eventId: event.id } } }),
  ]);

  const status = statusConfig[event.status];
  const type = typeConfig[event.type];
  const config = event.config;

  // tab navigasi anchor
  const tabs = [
    { id: "overview", label: "Overview", icon: Target },
    { id: "divisions", label: "Kelas Pertandingan", icon: Trophy },
    { id: "schedule", label: "Jadwal", icon: Clock },
    { id: "standings", label: "Klasemen", icon: Users },
  ];

  return (
    <div className="min-h-screen">
      {/* === HERO / POSTER === */}
      <div className="relative border-b">
        <div className="container py-12 md:py-16">
          <div className="flex flex-wrap items-center gap-2">
            <Badge variant="secondary">{type.label}</Badge>
            <Badge variant={status.variant}>{status.label}</Badge>
          </div>
          <h1 className="mt-4 max-w-3xl font-serif text-3xl font-semibold leading-tight tracking-tight md:text-5xl">
            {event.name}
          </h1>
          {event.description && (
            <p className="mt-4 max-w-2xl text-muted-foreground md:text-lg">
              {event.description}
            </p>
          )}

          {/* meta info */}
          <div className="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div className="flex items-center gap-3 rounded-md border bg-card px-4 py-3">
              <Calendar className="h-5 w-5 shrink-0 text-primary" />
              <div className="text-sm">
                <div className="font-medium">Tanggal</div>
                <div className="text-muted-foreground">
                  {formatDateRange(event.startDate, event.endDate)}
                </div>
              </div>
            </div>
            {event.venue && (
              <div className="flex items-center gap-3 rounded-md border bg-card px-4 py-3">
                <MapPin className="h-5 w-5 shrink-0 text-primary" />
                <div className="text-sm">
                  <div className="font-medium">Lokasi</div>
                  <div className="text-muted-foreground">{event.venue}</div>
                </div>
              </div>
            )}
            {config && (
              <div className="flex items-center gap-3 rounded-md border bg-card px-4 py-3">
                <Users className="h-5 w-5 shrink-0 text-primary" />
                <div className="text-sm">
                  <div className="font-medium">Registrasi</div>
                  <div className="text-muted-foreground">
                    {config.registrationType === "HYBRID"
                      ? "Individu + Kontingen"
                      : config.registrationType === "TEAM"
                      ? "Per Kontingen"
                      : "Individu"}
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* === STATS BAR === */}
      <div className="border-b bg-secondary/30">
        <div className="container grid grid-cols-3 divide-x divide-border">
          <StatBox value={participantCount} label="Peserta" />
          <StatBox value={event.contingents.length} label="Kontingen" />
          <StatBox value={matchCount} label="Pertandingan" />
        </div>
      </div>

      {/* === CONTENT === */}
      <div className="container space-y-16 py-14 md:py-20">
        {/* OVERVIEW */}
        <section id="overview" className="scroll-mt-20">
          <SectionTitle
            icon={Target}
            title="Overview"
            subtitle="Informasi umum kejuaraan"
          />
          <div className="grid gap-6 md:grid-cols-3">
            <InfoCard title="Format Kompetisi">
              {event.divisions.some((d) => d.format === "GROUP_KNOCKOUT") &&
                "Grup → Knockout"}
              {event.divisions.some((d) => d.format === "SCORING") &&
                " • Penilaian Juri"}
            </InfoCard>
            {config?.disciplines && (
              <InfoCard title="Cabang">
                {(config.disciplines as string[]).join(", ")}
              </InfoCard>
            )}
            {config?.ageCategories && (
              <InfoCard title="Kategori Umur">
                {(config.ageCategories as string[]).join(", ")}
              </InfoCard>
            )}
          </div>

          {/* kontak */}
          {(event.contactName || event.contactPhone || event.contactEmail) && (
            <div className="mt-6 rounded-lg border bg-card p-6">
              <h3 className="font-serif text-lg font-semibold">Kontak Panitia</h3>
              <div className="mt-3 grid gap-3 sm:grid-cols-3">
                {event.contactName && (
                  <div className="flex items-center gap-2 text-sm">
                    <Users className="h-4 w-4 text-muted-foreground" />
                    {event.contactName}
                  </div>
                )}
                {event.contactPhone && (
                  <a href={`tel:${event.contactPhone}`} className="flex items-center gap-2 text-sm hover:text-primary">
                    <Phone className="h-4 w-4 text-muted-foreground" />
                    {event.contactPhone}
                  </a>
                )}
                {event.contactEmail && (
                  <a href={`mailto:${event.contactEmail}`} className="flex items-center gap-2 text-sm hover:text-primary">
                    <Mail className="h-4 w-4 text-muted-foreground" />
                    {event.contactEmail}
                  </a>
                )}
              </div>
            </div>
          )}
        </section>

        {/* DIVISIONS */}
        <section id="divisions" className="scroll-mt-20">
          <SectionTitle
            icon={Trophy}
            title="Kelas Pertandingan"
            subtitle="Daftar kelas dan kategori yang dipertandingkan"
          />
          <DivisionList divisions={event.divisions} />
        </section>

        {/* SCHEDULE */}
        <section id="schedule" className="scroll-mt-20">
          <SectionTitle
            icon={Clock}
            title="Jadwal"
            subtitle="Timeline pertandingan per hari"
          />
          <ScheduleTimeline items={event.schedule} />
        </section>

        {/* STANDINGS */}
        <section id="standings" className="scroll-mt-20">
          <SectionTitle
            icon={Users}
            title="Klasemen Kontingen"
            subtitle="Perolehan medali akumulasi — Overall Champion"
          />
          <MedalTable standings={standings} />
        </section>
      </div>
    </div>
  );
}

function StatBox({ value, label }: { value: number; label: string }) {
  return (
    <div className="px-4 py-8 text-center">
      <div className="font-serif text-2xl font-semibold text-primary md:text-3xl">
        {value.toLocaleString("id-ID")}
      </div>
      <div className="mt-1 text-xs uppercase tracking-wider text-muted-foreground">
        {label}
      </div>
    </div>
  );
}

function SectionTitle({
  icon: Icon,
  title,
  subtitle,
}: {
  icon: typeof Target;
  title: string;
  subtitle: string;
}) {
  return (
    <div className="mb-6">
      <div className="flex items-center gap-2">
        <div className="flex h-8 w-8 items-center justify-center rounded-md bg-primary/10 text-primary">
          <Icon className="h-4 w-4" />
        </div>
        <h2 className="font-serif text-2xl font-semibold tracking-tight md:text-3xl">
          {title}
        </h2>
      </div>
      <p className="mt-1.5 pl-10 text-sm text-muted-foreground">{subtitle}</p>
    </div>
  );
}

function InfoCard({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="rounded-lg border bg-card p-5">
      <div className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
        {title}
      </div>
      <div className="mt-1.5 font-medium">{children || "—"}</div>
    </div>
  );
}
