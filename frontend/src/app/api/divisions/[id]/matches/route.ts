import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

export async function GET(
  _req: Request,
  { params }: { params: { id: string } }
) {
  const matches = await prisma.match.findMany({
    where: { divisionId: params.id },
    include: {
      participantA: { select: { id: true, name: true } },
      participantB: { select: { id: true, name: true } },
      winner: { select: { id: true, name: true } },
    },
    orderBy: [{ round: "asc" }, { bracketPosition: "asc" }],
  });

  return NextResponse.json({ matches });
}
