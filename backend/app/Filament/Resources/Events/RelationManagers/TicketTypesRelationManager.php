<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\TicketTypes\Schemas\TicketTypeForm;
use App\Filament\Resources\TicketTypes\Tables\TicketTypesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TicketTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketTypes';

    public function form(Schema $schema): Schema { return TicketTypeForm::configure($schema); }
    public function table(Table $table): Table { return TicketTypesTable::configure($table); }
}
