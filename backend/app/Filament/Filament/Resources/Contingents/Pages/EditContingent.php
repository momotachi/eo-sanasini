<?php

namespace App\Filament\Resources\Contingents\Pages;

use App\Filament\Resources\Contingents\ContingentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContingent extends EditRecord
{
    protected static string $resource = ContingentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
