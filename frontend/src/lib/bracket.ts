// Bracket generator — Group → Knockout logic
// Pure functions, reusable, tested via matches creation

import type { MatchRound } from "@prisma/client";

/**
 * Generate single-elimination knockout bracket dari daftar peserta.
 * Mengisi BYE untuk peserta ganjil.
 *
 * @param participantIds daftar ID peserta (sudah di-seed)
 * @returns array of match specs untuk round pertama
 */
export interface KnockoutSlot {
  bracketPosition: number;
  participantAId: string | null;
  participantBId: string | null;
  round: MatchRound;
  status: "SCHEDULED" | "BYE";
}

export function generateKnockoutSlots(
  participantIds: string[]
): KnockoutSlot[] {
  if (participantIds.length < 2) return [];

  // padding ke power of 2
  const size = nextPowerOfTwo(participantIds.length);
  const padded = [...participantIds];
  while (padded.length < size) padded.push(""); // empty = BYE

  // seeding: standar bracket seeding (1v8, 4v5, 3v6, 2v7 untuk 8)
  const seeded = seedOrder(padded);

  // tentukan round name dari size
  const round = roundFromSize(size);

  const slots: KnockoutSlot[] = [];
  for (let i = 0; i < seeded.length; i += 2) {
    const a = seeded[i] || null;
    const b = seeded[i + 1] || null;
    const hasBye = !a || !b;
    slots.push({
      bracketPosition: i / 2,
      participantAId: a || null,
      participantBId: b || null,
      round,
      status: hasBye ? "BYE" : "SCHEDULED",
    });
  }
  return slots;
}

/**
 * Generate round-robin group stage — semua lawan semua dalam grup.
 */
export interface GroupMatchSlot {
  groupLabel: string;
  participantAId: string;
  participantBId: string;
  round: MatchRound;
}

export function generateRoundRobinSlots(
  participantIds: string[],
  groupLabel = "A"
): GroupMatchSlot[] {
  const slots: GroupMatchSlot[] = [];
  for (let i = 0; i < participantIds.length; i++) {
    for (let j = i + 1; j < participantIds.length; j++) {
      slots.push({
        groupLabel,
        participantAId: participantIds[i],
        participantBId: participantIds[j],
        round: "GROUP_STAGE",
      });
    }
  }
  return slots;
}

/**
 * Bagi peserta ke grup (mis. 4 grup @ 4 peserta).
 */
export function splitIntoGroups(
  participantIds: string[],
  groupCount: number
): string[][] {
  const groups: string[][] = Array.from({ length: groupCount }, () => []);
  participantIds.forEach((id, i) => {
    groups[i % groupCount].push(id);
  });
  return groups;
}

// ===== HELPERS =====

function nextPowerOfTwo(n: number): number {
  let p = 2;
  while (p < n) p *= 2;
  return p;
}

function roundFromSize(size: number): MatchRound {
  switch (size) {
    case 2:
      return "FINAL";
    case 4:
      return "SEMIFINAL";
    case 8:
      return "QUARTERFINAL";
    case 16:
      return "ROUND_OF_16";
    default:
      return "ROUND_OF_16";
  }
}

// standard seeding order untuk single elimination
// agar seed 1 & 2 baru ketemu di final
function seedOrder(slots: (string | null)[]): (string | null)[] {
  if (slots.length <= 2) return slots.filter((s) => s !== undefined);
  const result: (string | null)[] = new Array(slots.length).fill(null);
  const seeds = slots.map((s, i) => ({ id: s, seed: i + 1 }));

  // bracket position untuk seeding: gunakan precomputed pattern
  const positions = seedPositions(slots.length);
  positions.forEach((pos, seedIdx) => {
    if (seeds[seedIdx]) {
      result[pos] = seeds[seedIdx].id;
    }
  });
  return result;
}

// generate posisi bracket untuk seeding standar
function seedPositions(size: number): number[] {
  let rounds = Math.log2(size);
  let positions = [0, 1];
  for (let r = 1; r < rounds; r++) {
    const next: number[] = [];
    const newSize = positions.length * 2;
    for (const p of positions) {
      next.push(p);
      next.push(newSize - 1 - p);
    }
    positions = next;
  }
  return positions;
}
