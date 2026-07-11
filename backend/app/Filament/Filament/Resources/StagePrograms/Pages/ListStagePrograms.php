<?php

namespace App\Filament\Resources\StagePrograms\Pages;

use App\Filament\Resources\StagePrograms\StageProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStagePrograms extends ListRecords
{
    protected static string $resource = StageProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
