<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\EventSessions\Schemas\EventSessionForm;
use App\Filament\Resources\EventSessions\Tables\EventSessionsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';

    public function form(Schema $schema): Schema { return EventSessionForm::configure($schema); }
    public function table(Table $table): Table { return EventSessionsTable::configure($table); }
}
