<?php

namespace App\Filament\Resources\MatchModels\Pages;

use App\Filament\Resources\MatchModels\MatchModelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMatchModel extends EditRecord
{
    protected static string $resource = MatchModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
