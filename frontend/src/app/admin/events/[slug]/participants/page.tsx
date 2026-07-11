import Link from "next/link";
import { notFound } from "next/navigation";
import { prisma } from "@/lib/prisma";
import { Badge } from "@/components/ui/badge";
import { ParticipantActions } from "@/components/admin/participant-actions";
import { ChevronLeft, Users } from "lucide-react";

const statusBadge: Record<string, { label: string; variant: "default" | "secondary" | "success" | "warning" | "destructive" | "outline" }> = {
  PENDING: { label: "Menunggu", variant: "warning" },
  APPROVED: { label: "Disetujui", variant: "success" },
  REJECTED: { label: "Ditolak", variant: "destructive" },
  WITHDRAWN: { label: "Mundur", variant: "outline" },
};

export default async function ParticipantsPage({
  params,
  searchParams,
}: {
  params: { slug: string };
  searchParams: { status?: string };
}) {
  const event = await prisma.event.findUnique({ where: { slug: params.slug } });
  if (!event) notFound();

  const statusFilter = (searchParams.status as typeof statusBadge[keyof typeof statusBadge] extends { variant: string } ? string : never) || "ALL";
  const statuses = ["PENDING", "APPROVED", "REJECTED", "WITHDRAWN"];

  const where =
    statusFilter !== "ALL"
      ? { division: { eventId: event.id }, status: statusFilter as never }
      : { division: { eventId: event.id } };

  const participants = await prisma.participant.findMany({
    where,
    include: {
      division: true,
      contingent: true,
    },
    orderBy: [{ status: "asc" }, { createdAt: "desc" }],
  });

  return (
    <div className="space-y-6">
      <Link
        href={`/admin/events/${params.slug}`}
        className="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
      >
        <ChevronLeft className="h-4 w-4" />
        {event.name}
      </Link>

      <div className="flex items-center justify-between">
        <div>
          <h1 className="font-serif text-2xl font-semibold tracking-tight">
            Peserta
          </h1>
          <p className="text-sm text-muted-foreground">
            {participants.length} peserta
          </p>
        </div>
        <Users className="h-6 w-6 text-muted-foreground" />
      </div>

      {/* Filter */}
      <div className="flex flex-wrap gap-2">
        <FilterChip href={`/admin/events/${params.slug}/participants`} active={statusFilter === "ALL"} label="Semua" />
        {statuses.map((s) => (
          <FilterChip
            key={s}
            href={`/admin/events/${params.slug}/participants?status=${s}`}
            active={statusFilter === s}
            label={statusBadge[s].label}
          />
        ))}
      </div>

      {/* Table */}
      {participants.length === 0 ? (
        <div className="rounded-lg border border-dashed p-12 text-center">
          <p className="text-sm text-muted-foreground">Belum ada peserta.</p>
        </div>
      ) : (
        <div className="overflow-x-auto rounded-lg border">
          <table className="w-full text-sm">
            <thead className="bg-secondary/50">
              <tr className="text-left">
                <th className="px-4 py-3 font-semibold">Nama</th>
                <th className="px-4 py-3 font-semibold">Kontingen</th>
                <th className="px-4 py-3 font-semibold">Kelas</th>
                <th className="px-4 py-3 font-semibold">Status</th>
                <th className="px-4 py-3 text-right font-semibold">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y">
              {participants.map((p) => (
                <tr key={p.id} className="hover:bg-secondary/30">
                  <td className="px-4 py-3">
                    <div className="font-medium">{p.name}</div>
                    <div className="text-xs text-muted-foreground">
                      {p.gender} · {p.birthDate ? new Date(p.birthDate).toLocaleDateString("id-ID") : "-"}
                    </div>
                  </td>
                  <td className="px-4 py-3">{p.contingent?.name || <span className="text-muted-foreground">—</span>}</td>
                  <td className="px-4 py-3">
                    <div>{p.division.discipline}</div>
                    <div className="text-xs text-muted-foreground">
                      {p.division.ageCategory} {p.division.gender} {p.division.className}
                    </div>
                  </td>
                  <td className="px-4 py-3">
                    <Badge variant={statusBadge[p.status].variant}>{statusBadge[p.status].label}</Badge>
                  </td>
                  <td className="px-4 py-3 text-right">
                    <ParticipantActions
                      eventSlug={params.slug}
                      participantId={p.id}
                      currentStatus={p.status}
                    />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

function FilterChip({ href, active, label }: { href: string; active: boolean; label: string }) {
  return (
    <a
      href={href}
      className={`rounded-full border px-3 py-1 text-xs font-medium transition-colors ${
        active
          ? "border-primary bg-primary text-primary-foreground"
          : "bg-card text-muted-foreground hover:border-primary hover:text-foreground"
      }`}
    >
      {label}
    </a>
  );
}
