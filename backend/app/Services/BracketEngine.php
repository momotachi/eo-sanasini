<?php

namespace App\Services;

use App\Models\Division;
use App\Models\MatchModel;
use Illuminate\Support\Collection;

/**
 * Bracket Engine — generate match slots untuk berbagai format kompetisi.
 *
 * Mendukung:
 *  - Single elimination (Full Knockout): auto-bye untuk peserta ganjil
 *  - Group → Knockout: bagi peserta ke grup, round-robin tiap grup
 *  - Full Round Robin (Liga)
 *
 * Pure logic class — tidak sentuh DB. generateBracket() di service terpisah
 * yang pakai class ini lalu persist ke tabel matches.
 */
class BracketEngine
{
    /**
     * Generate single-elimination knockout slots.
     * Peserta di-seed supaya seed 1 & 2 baru ketemu di final.
     *
     * @return array<int, array{bracket_position: int, participant_a_id: ?string, participant_b_id: ?string, round: string, status: string}>
     */
    public function generateKnockoutSlots(array $participantIds): array
    {
        if (count($participantIds) < 2) {
            return [];
        }

        $size = $this->nextPowerOfTwo(count($participantIds));
        // padding dengan null (BYE)
        $padded = array_values($participantIds);
        while (count($padded) < $size) {
            $padded[] = null;
        }

        $seeded = $this->seedOrder($padded);
        $round = $this->roundFromSize($size);

        $slots = [];
        for ($i = 0; $i < count($seeded); $i += 2) {
            $a = $seeded[$i] ?? null;
            $b = $seeded[$i + 1] ?? null;
            $hasBye = $a === null || $b === null;

            $slots[] = [
                'bracket_position' => intdiv($i, 2),
                'participant_a_id' => $a,
                'participant_b_id' => $b,
                'round' => $round,
                'status' => $hasBye ? 'BYE' : 'SCHEDULED',
            ];
        }
        return $slots;
    }

    /**
     * Generate round-robin slots (semua lawan semua) untuk satu grup.
     *
     * @return array<int, array{participant_a_id: string, participant_b_id: string, round: string, group_label: string}>
     */
    public function generateRoundRobinSlots(array $participantIds, string $groupLabel = 'A'): array
    {
        $slots = [];
        $count = count($participantIds);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $slots[] = [
                    'participant_a_id' => $participantIds[$i],
                    'participant_b_id' => $participantIds[$j],
                    'round' => 'GROUP_STAGE',
                    'group_label' => $groupLabel,
                ];
            }
        }
        return $slots;
    }

    /**
     * Bagi peserta ke N grup (distribusi round-robin supaya seimbang).
     *
     * @return array<int, array<int, string>>
     */
    public function splitIntoGroups(array $participantIds, int $groupCount): array
    {
        $groups = array_fill(0, $groupCount, []);
        foreach ($participantIds as $i => $id) {
            $groups[$i % $groupCount][] = $id;
        }
        return $groups;
    }

    /**
     * Entry point: generate full bracket untuk sebuah Division.
     * Logika menyesuaikan format divisi:
     *   - FULL_KNOCKOUT: langsung knockout
     *   - GROUP_KNOCKOUT: kalau peserta >= 8, bagi grup dulu
     *   - ROUND_ROBIN: full liga
     *   - SCORING / NON_COMPETITIVE: tidak generate match
     *
     * @return array{matches_count: int, message: string}
     */
    public function generateForDivision(Division $division): array
    {
        $participants = $division->participants()
            ->where('status', 'APPROVED')
            ->orderBy('seed')
            ->orderBy('name')
            ->pluck('id')
            ->all();

        if (count($participants) < 2) {
            return ['matches_count' => 0, 'message' => 'Minimal 2 peserta approved untuk generate bracket.'];
        }

        // hapus match lama di divisi ini
        MatchModel::where('division_id', $division->id)->delete();

        $format = $division->format;
        $count = 0;

        if ($format === 'FULL_KNOCKOUT') {
            $slots = $this->generateKnockoutSlots($participants);
            foreach ($slots as $slot) {
                MatchModel::create(array_merge($slot, [
                    'division_id' => $division->id,
                    'bracket_position' => $slot['bracket_position'] ?? 0,
                ]));
                $count++;
            }
            $byeCount = count(array_filter($slots, fn($s) => $s['status'] === 'BYE'));
            return ['matches_count' => $count, 'message' => "{$count} pertandingan knockout dibuat ({$byeCount} BYE)."];
        }

        if ($format === 'GROUP_KNOCKOUT' && count($participants) >= 8) {
            // Babak grup dulu (4 grup), knockout dibuat setelah grup selesai
            $groupCount = min(4, intdiv(count($participants), 2));
            $groups = $this->splitIntoGroups($participants, $groupCount);
            foreach ($groups as $gi => $group) {
                $label = chr(65 + $gi); // A, B, C, D
                $slots = $this->generateRoundRobinSlots($group, $label);
                foreach ($slots as $slot) {
                    MatchModel::create(array_merge($slot, [
                        'division_id' => $division->id,
                        'bracket_position' => $count,
                    ]));
                    $count++;
                }
            }
            return [
                'matches_count' => $count,
                'message' => "{$count} pertandingan babak grup ({$groupCount} grup). Knockout dibuat setelah grup selesai.",
            ];
        }

        if ($format === 'GROUP_KNOCKOUT') {
            // peserta < 8, fallback ke knockout langsung
            $slots = $this->generateKnockoutSlots($participants);
            foreach ($slots as $slot) {
                MatchModel::create(array_merge($slot, [
                    'division_id' => $division->id,
                    'bracket_position' => $slot['bracket_position'] ?? 0,
                ]));
                $count++;
            }
            return ['matches_count' => $count, 'message' => "{$count} pertandingan knockout dibuat (peserta < 8, grup skip)."];
        }

        if ($format === 'ROUND_ROBIN') {
            $slots = $this->generateRoundRobinSlots($participants, 'A');
            foreach ($slots as $slot) {
                MatchModel::create(array_merge($slot, [
                    'division_id' => $division->id,
                    'bracket_position' => 0,
                ]));
                $count++;
            }
            return ['matches_count' => $count, 'message' => "{$count} pertandingan round-robin dibuat."];
        }

        return ['matches_count' => 0, 'message' => "Format {$format} tidak didukung auto-generate."];
    }

    // ===== HELPERS =====

    protected function nextPowerOfTwo(int $n): int
    {
        $p = 2;
        while ($p < $n) {
            $p *= 2;
        }
        return $p;
    }

    protected function roundFromSize(int $size): string
    {
        return match ($size) {
            2 => 'FINAL',
            4 => 'SEMIFINAL',
            8 => 'QUARTERFINAL',
            16 => 'ROUND_OF_16',
            default => 'ROUND_OF_16',
        };
    }

    /**
     * Standard seeding supaya seed 1 & 2 baru ketemu di final.
     * Pattern: posisi bracket dibangun rekursif.
     */
    protected function seedOrder(array $slots): array
    {
        $size = count($slots);
        if ($size <= 2) {
            return $slots;
        }
        $result = array_fill(0, $size, null);
        $positions = $this->seedPositions($size);
        foreach ($positions as $seedIdx => $pos) {
            if (isset($slots[$seedIdx])) {
                $result[$pos] = $slots[$seedIdx];
            }
        }
        return $result;
    }

    /**
     * Generate urutan posisi bracket untuk seeding standar.
     * Mis. size=8 -> [0,7,3,4,2,5,6,1] supaya 1v8,4v5,3v6,2v7.
     */
    protected function seedPositions(int $size): array
    {
        $rounds = (int) log($size, 2);
        $positions = [0, 1];
        for ($r = 1; $r < $rounds; $r++) {
            $next = [];
            $newSize = count($positions) * 2;
            foreach ($positions as $p) {
                $next[] = $p;
                $next[] = $newSize - 1 - $p;
            }
            $positions = $next;
        }
        return $positions;
    }
}
