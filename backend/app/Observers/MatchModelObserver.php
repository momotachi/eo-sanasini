<?php

namespace App\Observers;

use App\Models\MatchModel;
use App\Services\BracketEngine;
use App\Services\MedalService;

class MatchModelObserver
{
    /**
     * Saat match di-update (mis. set winner):
     *  1. Advance winner ke match round berikutnya (P1-2)
     *  2. Recompute medali untuk divisi (auto-medal)
     */
    public function updated(MatchModel $match): void
    {
        $isJustCompleted = ($match->wasChanged('status') && $match->status === 'COMPLETED')
            || $match->wasChanged('winner_id');

        if (!$isJustCompleted) return;

        $division = $match->division;
        if (!$division) return;

        // P1-2: advance winner ke match round berikutnya (FULL_KNOCKOUT saja)
        if (in_array($division->format, ['FULL_KNOCKOUT', 'GROUP_KNOCKOUT'])) {
            app(BracketEngine::class)->advanceWinner($match);
        }

        // Recompute medali (auto GOLD/SILVER/BRONZE dari FINAL/SEMIFINAL/THIRD_PLACE)
        app(MedalService::class)->recomputeForDivision($division);
    }

    public function created(MatchModel $match): void
    {
        // tidak ada action saat create
    }

    public function deleted(MatchModel $match): void
    {
        if ($match->status === 'COMPLETED') {
            $division = $match->division;
            if ($division) {
                app(MedalService::class)->recomputeForDivision($division);
            }
        }
    }
}

