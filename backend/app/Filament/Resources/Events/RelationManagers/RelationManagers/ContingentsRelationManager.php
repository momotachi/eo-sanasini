<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Contingents\Schemas\ContingentForm;
use App\Filament\Resources\Contingents\Tables\ContingentsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ContingentsRelationManager extends RelationManager
{
    protected static string $relationship = 'contingents';

    public function form(Schema $schema): Schema
    {
        return ContingentForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ContingentsTable::configure($table);
    }
}
