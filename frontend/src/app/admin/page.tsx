import Link from "next/link";
import { prisma } from "@/lib/prisma";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { Users, Trophy, CalendarClock, ClipboardList, ArrowRight } from "lucide-react";

export const metadata = { title: "Admin Dashboard" };

export default async function AdminDashboard() {
  const [events, participantCount, pendingCount, matchCount] = await Promise.all([
    prisma.event.findMany({
      select: { id: true, name: true, slug: true, status: true, type: true },
      orderBy: { startDate: "desc" },
    }),
    prisma.participant.count(),
    prisma.participant.count({ where: { status: "PENDING" } }),
    prisma.match.count(),
  ]);

  const stats = [
    { label: "Total Event", value: events.length, icon: Trophy },
    { label: "Total Peserta", value: participantCount, icon: Users },
    { label: "Menunggu Verifikasi", value: pendingCount, icon: ClipboardList, highlight: true },
    { label: "Total Pertandingan", value: matchCount, icon: CalendarClock },
  ];

  return (
    <div className="space-y-8">
      <div>
        <h1 className="font-serif text-2xl font-semibold tracking-tight md:text-3xl">
          Dashboard
        </h1>
        <p className="text-sm text-muted-foreground">
          Ringkasan operasional event EO Sanasini.
        </p>
      </div>

      {/* Stats */}
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {stats.map((s) => (
          <Card key={s.label} className={s.highlight && pendingCount > 0 ? "border-warning" : ""}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                {s.label}
              </CardTitle>
              <s.icon className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="font-serif text-2xl font-semibold">{s.value}</div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Event list */}
      <div>
        <h2 className="mb-4 font-serif text-xl font-semibold">Event</h2>
        <div className="space-y-2">
          {events.map((event) => (
            <Link
              key={event.id}
              href={`/admin/events/${event.slug}`}
              className="flex items-center justify-between rounded-lg border bg-card p-4 transition-colors hover:border-primary"
            >
              <div>
                <div className="font-medium">{event.name}</div>
                <div className="text-xs text-muted-foreground">
                  {event.type} · {event.status}
                </div>
              </div>
              <ArrowRight className="h-4 w-4 text-muted-foreground" />
            </Link>
          ))}
        </div>
      </div>
    </div>
  );
}
