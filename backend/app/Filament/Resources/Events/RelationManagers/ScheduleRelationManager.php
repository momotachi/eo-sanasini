<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\ScheduleItems\Schemas\ScheduleItemForm;
use App\Filament\Resources\ScheduleItems\Tables\ScheduleItemsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ScheduleRelationManager extends RelationManager
{
    protected static string $relationship = 'schedule';

    public function form(Schema $schema): Schema
    {
        return ScheduleItemForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ScheduleItemsTable::configure($table);
    }
}
