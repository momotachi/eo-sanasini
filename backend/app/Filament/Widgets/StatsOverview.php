<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\MatchModel;
use App\Models\Participant;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Event', Event::count())
                ->description(Event::where('status', 'ONGOING')->count() . ' sedang berlangsung')
                ->icon(Heroicon::OutlinedRectangleStack)
                ->color('primary'),

            Stat::make('Peserta Terdaftar', Participant::count())
                ->description(Participant::where('status', 'APPROVED')->count() . ' approved · '
                    . Participant::where('status', 'PENDING')->count() . ' menunggu')
                ->icon(Heroicon::OutlinedUsers)
                ->color('success'),

            Stat::make('Pertandingan', MatchModel::count())
                ->description(MatchModel::where('status', 'COMPLETED')->count() . ' selesai')
                ->icon(Heroicon::OutlinedTrophy)
                ->color('warning'),
        ];
    }

    protected static ?int $sort = 1;
}
