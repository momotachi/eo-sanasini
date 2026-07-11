import Link from "next/link";
import { notFound } from "next/navigation";
import { prisma } from "@/lib/prisma";
import { ChevronLeft, Trophy } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { BracketManager } from "@/components/admin/bracket-manager";

export default async function MatchesPage({
  params,
}: {
  params: { slug: string };
}) {
  const event = await prisma.event.findUnique({ where: { slug: params.slug } });
  if (!event) notFound();

  const divisions = await prisma.division.findMany({
    where: { eventId: event.id },
    orderBy: [{ discipline: "asc" }, { ageCategory: "asc" }, { gender: "asc" }],
    include: {
      _count: { select: { participants: true, matches: true } },
    },
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
            Pertandingan &amp; Bracket
          </h1>
          <p className="text-sm text-muted-foreground">
            Generate bracket dan input hasil pertandingan
          </p>
        </div>
        <Trophy className="h-6 w-6 text-muted-foreground" />
      </div>

      <div className="space-y-3">
        {divisions.map((d) => (
          <BracketManager
            key={d.id}
            eventSlug={params.slug}
            division={{
              id: d.id,
              discipline: d.discipline,
              ageCategory: d.ageCategory,
              gender: d.gender,
              className: d.className,
              format: d.format,
              participantCount: d._count.participants,
              matchCount: d._count.matches,
            }}
          />
        ))}
      </div>
    </div>
  );
}
