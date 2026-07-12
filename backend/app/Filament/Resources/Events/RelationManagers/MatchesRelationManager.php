<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\MatchModels\Schemas\MatchModelForm;
use App\Filament\Resources\MatchModels\Tables\MatchModelsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'matches'; // relasi di Event model: matches()

    public function form(Schema $schema): Schema
    {
        return MatchModelForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return MatchModelsTable::configure($table);
    }
}
