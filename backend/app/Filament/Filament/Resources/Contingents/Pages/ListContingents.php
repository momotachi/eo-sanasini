<?php

namespace App\Filament\Resources\Contingents\Pages;

use App\Filament\Resources\Contingents\ContingentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContingents extends ListRecords
{
    protected static string $resource = ContingentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
