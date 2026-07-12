<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Medals\Schemas\MedalForm;
use App\Filament\Resources\Medals\Tables\MedalsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MedalsRelationManager extends RelationManager
{
    protected static string $relationship = 'medals';

    public function form(Schema $schema): Schema
    {
        return MedalForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return MedalsTable::configure($table);
    }
}
