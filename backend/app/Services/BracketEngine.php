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

            // P1-2: Generate placeholder match untuk round berikutnya (SEMIFINAL/FINAL/THIRD_PLACE).
            // Match kosong, akan diisi otomatis saat round sebelumnya selesai (lihat MatchModelObserver).
            $firstRoundSize = $this->nextPowerOfTwo(count($participants));
            $count = $this->generateNextRounds($division->id, $firstRoundSize, $count);

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

    /**
     * Generate placeholder match kosong untuk round berikutnya sampai FINAL + THIRD_PLACE.
     * Match-nya kosong (participant_a/b = null), akan diisi oleh MatchModelObserver
     * saat round sebelumnya selesai.
     */
    protected function generateNextRounds(int $divisionId, int $firstRoundSize, int $count): int
    {
        $roundsChain = [
            16 => ['ROUND_OF_16', 'QUARTERFINAL', 'SEMIFINAL', 'FINAL'],
            8  => ['QUARTERFINAL', 'SEMIFINAL', 'FINAL'],
            4  => ['SEMIFINAL', 'FINAL'],
            2  => ['FINAL'],
        ];

        $chain = $roundsChain[$firstRoundSize] ?? ['FINAL'];
        // skip index 0 (round pertama sudah dibuat di generateKnockoutSlots)
        for ($i = 1; $i < count($chain); $i++) {
            $round = $chain[$i];
            $matchCountInRound = intdiv($firstRoundSize, pow(2, $i));
            for ($pos = 0; $pos < $matchCountInRound; $pos++) {
                MatchModel::create([
                    'division_id' => $divisionId,
                    'round' => $round,
                    'bracket_position' => $pos,
                    'participant_a_id' => null,
                    'participant_b_id' => null,
                    'status' => 'SCHEDULED',
                ]);
                $count++;
            }
        }

        // THIRD_PLACE match (dari 2 loser SEMIFINAL) — kalau ada babak semifinal
        if (in_array('SEMIFINAL', $chain)) {
            MatchModel::create([
                'division_id' => $divisionId,
                'round' => 'THIRD_PLACE',
                'bracket_position' => 0,
                'participant_a_id' => null,
                'participant_b_id' => null,
                'status' => 'SCHEDULED',
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Advance winner ke match round berikutnya.
     * Dipanggil dari MatchModelObserver saat sebuah match selesai.
     *
     * Logika: cari match di round berikutnya dengan bracket_position yang sesuai,
     * isi peserta (A atau B slot-nya). Mapping posisi:
     *   round 1 match 0,1 -> round 2 match 0 (slot A, B)
     *   round 1 match 2,3 -> round 2 match 1 (slot A, B)
     *   dst.
     */
    public function advanceWinner(MatchModel $completedMatch): void
    {
        if (!$completedMatch->winner_id) return;
        if ($completedMatch->round === 'FINAL' || $completedMatch->round === 'THIRD_PLACE') return;

        $nextRound = $this->nextRoundName($completedMatch->round);
        if (!$nextRound) return;

        // cari match selanjutnya di round + bracket_position yang sesuai
        $nextPos = intdiv($completedMatch->bracket_position, 2);
        $nextMatch = MatchModel::where('division_id', $completedMatch->division_id)
            ->where('round', $nextRound)
            ->where('bracket_position', $nextPos)
            ->first();

        if (!$nextMatch) return;

        // isi slot A dulu, kalau sudah terisi -> slot B
        if ($completedMatch->bracket_position % 2 === 0) {
            $nextMatch->participant_a_id = $completedMatch->winner_id;
        } else {
            $nextMatch->participant_b_id = $completedMatch->winner_id;
        }
        $nextMatch->save();

        // Handle THIRD_PLACE: 2 loser SEMIFINAL masuk ke match THIRD_PLACE
        if ($completedMatch->round === 'SEMIFINAL') {
            $loser = $this->getMatchLoser($completedMatch);
            if ($loser) {
                $thirdPlace = MatchModel::where('division_id', $completedMatch->division_id)
                    ->where('round', 'THIRD_PLACE')
                    ->where('bracket_position', 0)
                    ->first();
                if ($thirdPlace) {
                    if ($completedMatch->bracket_position % 2 === 0) {
                        $thirdPlace->participant_a_id = $loser;
                    } else {
                        $thirdPlace->participant_b_id = $loser;
                    }
                    $thirdPlace->save();
                }
            }
        }
    }

    protected function nextRoundName(string $round): ?string
    {
        $chain = ['ROUND_OF_16', 'QUARTERFINAL', 'SEMIFINAL', 'FINAL'];
        $idx = array_search($round, $chain);
        if ($idx === false || $idx >= count($chain) - 1) return null;
        return $chain[$idx + 1];
    }

    protected function getMatchLoser(MatchModel $match): ?string
    {
        if (!$match->winner_id) return null;
        if ($match->participant_a_id && $match->participant_a_id !== $match->winner_id) {
            return $match->participant_a_id;
        }
        if ($match->participant_b_id && $match->participant_b_id !== $match->winner_id) {
            return $match->participant_b_id;
        }
        return null;
    }

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
