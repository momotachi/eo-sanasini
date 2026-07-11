<?php

namespace App\Services;

use App\Models\MatchModel;
use App\Models\Medal;
use App\Models\Participant;
use App\Models\Division;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Auto-assign medali ketika match dengan round FINAL / SEMIFINAL / THIRD_PLACE selesai.
 *
 * Aturan Taekwondo WT (default bronzePerDivision = 2):
 *  - FINAL         -> pemenang GOLD, kalahhan SILVER
 *  - SEMIFINAL     -> kedua kalahhan BRONZE (2 peserta)
 *  - THIRD_PLACE   -> pemenang BRONZE (kalau ada perebutan juara 3)
 *
 * Kalau bronzePerDivision = 1, hanya pemenang THIRD_PLACE yang dapat BRONZE.
 */
class MedalService
{
    /**
     * Recompute semua medali untuk sebuah division.
     * Dipanggil setiap kali ada match COMPLETED di divisi tsb.
     */
    public function recomputeForDivision(Division $division): void
    {
        // ambil config event
        $config = $division->event?->config;
        $bronzePerDivision = $config?->bronze_per_division ?? 2;

        // hapus medali lama di divisi ini
        Medal::where('division_id', $division->id)->delete();

        $matches = MatchModel::where('division_id', $division->id)
            ->where('status', 'COMPLETED')
            ->whereNotNull('winner_id')
            ->with(['participantA', 'participantB', 'winner'])
            ->get();

        foreach ($matches as $match) {
            // FINAL: winner = GOLD, loser = SILVER
            if ($match->round === 'FINAL') {
                $this->createMedal($match->winner_id, $division, 'GOLD');
                $loser = $this->getLoser($match);
                if ($loser) $this->createMedal($loser, $division, 'SILVER');
            }

            // THIRD_PLACE: winner = BRONZE
            if ($match->round === 'THIRD_PLACE') {
                $this->createMedal($match->winner_id, $division, 'BRONZE');
            }

            // SEMIFINAL (kalau bronzePerDivision = 2, dua loser dapat BRONZE)
            if ($match->round === 'SEMIFINAL' && $bronzePerDivision === 2) {
                $loser = $this->getLoser($match);
                if ($loser) $this->createMedal($loser, $division, 'BRONZE');
            }
        }
    }

    protected function createMedal(string $participantId, Division $division, string $type): void
    {
        $participant = Participant::find($participantId);
        if (!$participant) return;

        Medal::firstOrCreate(
            [
                'division_id' => $division->id,
                'participant_id' => $participantId,
                'type' => $type,
            ],
            [
                'event_id' => $division->event_id,
                'contingent_id' => $participant->contingent_id,
                'discipline' => $division->discipline,
            ]
        );
    }

    protected function getLoser(MatchModel $match): ?string
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
}
