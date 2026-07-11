"use server";

import { prisma } from "@/lib/prisma";
import { generateKnockoutSlots, generateRoundRobinSlots, splitIntoGroups } from "@/lib/bracket";
import { revalidatePath } from "next/cache";

// ===== GENERATE BRACKET =====

export async function generateBracket(
  eventSlug: string,
  divisionId: string
): Promise<{ success: boolean; message: string; matchCount?: number }> {
  const division = await prisma.division.findUnique({
    where: { id: divisionId },
    include: {
      participants: {
        where: { status: "APPROVED" },
      },
    },
  });

  if (!division) return { success: false, message: "Divisi tidak ditemukan" };
  if (division.participants.length < 2) {
    return { success: false, message: "Minimal 2 peserta approved untuk generate bracket" };
  }

  // hapus match lama di divisi ini (regenerate)
  await prisma.match.deleteMany({ where: { divisionId } });

  const participantIds = division.participants.map((p) => p.id);

  if (division.format === "FULL_KNOCKOUT" || division.format === "GROUP_KNOCKOUT") {
    if (division.format === "GROUP_KNOCKOUT" && participantIds.length >= 8) {
      // babak grup dulu (4 grup @ minimal 2), lalu knockout untuk pemenang
      const groupCount = Math.min(4, Math.floor(participantIds.length / 2));
      const groups = splitIntoGroups(participantIds, groupCount);
      let matchCount = 0;

      for (let gi = 0; gi < groups.length; gi++) {
        const label = String.fromCharCode(65 + gi); // A, B, C, D
        const slots = generateRoundRobinSlots(groups[gi], label);
        for (const slot of slots) {
          await prisma.match.create({
            data: {
              divisionId,
              round: slot.round,
              bracketPosition: matchCount,
              groupLabel: slot.groupLabel,
              participantAId: slot.participantAId,
              participantBId: slot.participantBId,
              status: "SCHEDULED",
            },
          });
          matchCount++;
        }
      }
      revalidatePath(`/admin/events/${eventSlug}/matches`);
      revalidatePath(`/events/${eventSlug}`);
      return {
        success: true,
        message: `Bracket Grup→Knockout dibuat: ${matchCount} pertandingan babak grup (${groupCount} grup). Knockout dibuat setelah babak grup selesai.`,
        matchCount,
      };
    }

    // single elimination knockout langsung
    const slots = generateKnockoutSlots(participantIds);
    for (const slot of slots) {
      await prisma.match.create({
        data: {
          divisionId,
          round: slot.round,
          bracketPosition: slot.bracketPosition,
          participantAId: slot.participantAId,
          participantBId: slot.participantBId,
          status: slot.status,
        },
      });
    }
    revalidatePath(`/admin/events/${eventSlug}/matches`);
    revalidatePath(`/events/${eventSlug}`);
    return {
      success: true,
      message: `${slots.length} pertandingan knockout dibuat (${slots.filter((s) => s.status === "BYE").length} BYE)`,
      matchCount: slots.length,
    };
  }

  if (division.format === "ROUND_ROBIN") {
    const slots = generateRoundRobinSlots(participantIds, "A");
    for (const slot of slots) {
      await prisma.match.create({
        data: {
          divisionId,
          round: slot.round,
          bracketPosition: 0,
          groupLabel: "A",
          participantAId: slot.participantAId,
          participantBId: slot.participantBId,
          status: "SCHEDULED",
        },
      });
    }
    revalidatePath(`/admin/events/${eventSlug}/matches`);
    return { success: true, message: `${slots.length} pertandingan round-robin dibuat`, matchCount: slots.length };
  }

  return { success: false, message: `Format ${division.format} belum didukung untuk auto-generate` };
}

// ===== INPUT SKOR =====

export async function setMatchResult(
  eventSlug: string,
  matchId: string,
  winnerId: string,
  scoreA?: string,
  scoreB?: string
): Promise<{ success: boolean; message: string }> {
  const match = await prisma.match.findUnique({
    where: { id: matchId },
    include: { division: true },
  });
  if (!match) return { success: false, message: "Pertandingan tidak ditemukan" };

  const validWinner =
    winnerId === match.participantAId || winnerId === match.participantBId;
  if (!validWinner) return { success: false, message: "Pemenang harus salah satu peserta" };

  await prisma.match.update({
    where: { id: matchId },
    data: {
      winnerId,
      scoreA: scoreA ? { value: scoreA } : match.scoreA,
      scoreB: scoreB ? { value: scoreB } : match.scoreB,
      status: "COMPLETED",
    },
  });

  revalidatePath(`/admin/events/${eventSlug}/matches`);
  revalidatePath(`/events/${eventSlug}`);
  return { success: true, message: "Hasil tersimpan" };
}
