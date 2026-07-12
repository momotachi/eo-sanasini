<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Divisions\Schemas\DivisionForm;
use App\Filament\Resources\Divisions\Tables\DivisionsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DivisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'divisions';

    public function form(Schema $schema): Schema
    {
        return DivisionForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DivisionsTable::configure($table);
    }
}
