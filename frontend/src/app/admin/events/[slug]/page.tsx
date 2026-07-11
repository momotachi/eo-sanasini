import Link from "next/link";
import { notFound } from "next/navigation";
import { prisma } from "@/lib/prisma";
import { statusConfig, typeConfig } from "@/lib/event";
import { Badge } from "@/components/ui/badge";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { Users, Trophy, CalendarClock, Settings, ArrowRight, ChevronLeft } from "lucide-react";

export default async function AdminEventPage({
  params,
}: {
  params: { slug: string };
}) {
  const event = await prisma.event.findUnique({
    where: { slug: params.slug },
    include: { config: true },
  });
  if (!event) notFound();

  const [participantCount, pendingCount, matchesCount, contingentsCount] = await Promise.all([
    prisma.participant.count({ where: { division: { eventId: event.id } } }),
    prisma.participant.count({
      where: { division: { eventId: event.id }, status: "PENDING" },
    }),
    prisma.match.count({ where: { division: { eventId: event.id } } }),
    prisma.contingent.count({ where: { eventId: event.id } }),
  ]);

  const status = statusConfig[event.status];
  const type = typeConfig[event.type];

  const modules = [
    {
      href: `/admin/events/${event.slug}/participants`,
      icon: Users,
      title: "Peserta",
      desc: "Verifikasi & kelola pendaftaran atlet",
      stat: `${pendingCount} menunggu / ${participantCount} total`,
      highlight: pendingCount > 0,
    },
    {
      href: `/admin/events/${event.slug}/matches`,
      icon: Trophy,
      title: "Pertandingan & Bracket",
      desc: "Generate bracket, input skor, tentukan pemenang",
      stat: `${matchesCount} pertandingan`,
    },
    {
      href: `/admin/events/${event.slug}/schedule`,
      icon: CalendarClock,
      title: "Jadwal",
      desc: "Atur timeline pertandingan per venue",
      stat: "Kelola jadwal",
    },
    {
      href: `/admin/events/${event.slug}/settings`,
      icon: Settings,
      title: "Pengaturan Event",
      desc: "Status, info, klasemen medali",
      stat: `${contingentsCount} kontingen`,
    },
  ];

  return (
    <div className="space-y-6">
      <Link
        href="/admin"
        className="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
      >
        <ChevronLeft className="h-4 w-4" />
        Dashboard
      </Link>

      <div>
        <div className="flex flex-wrap items-center gap-2">
          <Badge variant="secondary">{type.label}</Badge>
          <Badge variant={status.variant}>{status.label}</Badge>
        </div>
        <h1 className="mt-2 font-serif text-2xl font-semibold tracking-tight md:text-3xl">
          {event.name}
        </h1>
        <p className="text-sm text-muted-foreground">
          {event.venue} · {contingentsCount} kontingen
        </p>
      </div>

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {[
          { label: "Peserta", value: participantCount },
          { label: "Menunggu", value: pendingCount },
          { label: "Pertandingan", value: matchesCount },
          { label: "Kontingen", value: contingentsCount },
        ].map((s) => (
          <Card key={s.label}>
            <CardContent className="py-4">
              <div className="text-xs uppercase tracking-wider text-muted-foreground">
                {s.label}
              </div>
              <div className="mt-1 font-serif text-2xl font-semibold">{s.value}</div>
            </CardContent>
          </Card>
        ))}
      </div>

      <div className="grid gap-4 sm:grid-cols-2">
        {modules.map((m) => (
          <Link
            key={m.href}
            href={m.href}
            className={`group rounded-lg border bg-card p-5 transition-all hover:-translate-y-0.5 hover:shadow-md ${
              m.highlight ? "border-warning" : ""
            }`}
          >
            <div className="flex items-start gap-4">
              <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                <m.icon className="h-5 w-5" />
              </div>
              <div className="min-w-0 flex-1">
                <div className="flex items-center justify-between">
                  <h3 className="font-serif text-lg font-semibold">{m.title}</h3>
                  <ArrowRight className="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-1" />
                </div>
                <p className="mt-1 text-sm text-muted-foreground">{m.desc}</p>
                <p className="mt-2 text-xs font-medium text-primary">{m.stat}</p>
              </div>
            </div>
          </Link>
        ))}
      </div>
    </div>
  );
}
