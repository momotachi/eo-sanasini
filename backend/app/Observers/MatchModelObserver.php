<?php

namespace App\Observers;

use App\Models\MatchModel;
use App\Services\MedalService;

class MatchModelObserver
{
    /**
     * Saat match di-update (mis. set winner), recompute medali untuk divisi.
     */
    public function updated(MatchModel $match): void
    {
        // hanya kalau status jadi COMPLETED atau winner berubah
        if (
            $match->wasChanged('status') && $match->status === 'COMPLETED'
            || $match->wasChanged('winner_id')
        ) {
            $division = $match->division;
            if ($division) {
                app(MedalService::class)->recomputeForDivision($division);
            }
        }
    }

    public function created(MatchModel $match): void
    {
        // tidak ada action saat create (match belum selesai)
    }

    public function deleted(MatchModel $match): void
    {
        // recompute kalau match selesai dihapus
        if ($match->status === 'COMPLETED') {
            $division = $match->division;
            if ($division) {
                app(MedalService::class)->recomputeForDivision($division);
            }
        }
    }
}
