<?php

namespace App\Filament\Resources\MatchModels\Pages;

use App\Filament\Resources\MatchModels\MatchModelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMatchModels extends ListRecords
{
    protected static string $resource = MatchModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
