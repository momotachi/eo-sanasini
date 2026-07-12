<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Participants\Schemas\ParticipantForm;
use App\Filament\Resources\Participants\Tables\ParticipantsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    public function form(Schema $schema): Schema
    {
        return ParticipantForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ParticipantsTable::configure($table);
    }
}
