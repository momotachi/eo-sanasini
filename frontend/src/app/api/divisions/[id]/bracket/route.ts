import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

// Bracket data untuk public display — group by round + group label
export async function GET(
  _req: Request,
  { params }: { params: { id: string } }
) {
  const matches = await prisma.match.findMany({
    where: { divisionId: params.id },
    include: {
      participantA: {
        select: {
          id: true,
          name: true,
          contingent: { select: { name: true } },
        },
      },
      participantB: {
        select: {
          id: true,
          name: true,
          contingent: { select: { name: true } },
        },
      },
      winner: { select: { id: true } },
    },
    orderBy: [{ round: "asc" }, { groupLabel: "asc" }, { bracketPosition: "asc" }],
  });

  // group by stage
  const byStage: Record<string, typeof matches> = {};
  for (const m of matches) {
    const key = m.groupLabel && m.round === "GROUP_STAGE"
      ? `Grup ${m.groupLabel}`
      : m.round.replace(/_/g, " ");
    (byStage[key] = byStage[key] || []).push(m);
  }

  return NextResponse.json({ stages: byStage });
}
