<?php

namespace App\Filament\Resources\StagePrograms\Pages;

use App\Filament\Resources\StagePrograms\StageProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStageProgram extends EditRecord
{
    protected static string $resource = StageProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
