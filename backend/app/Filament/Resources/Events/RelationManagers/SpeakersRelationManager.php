<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Speakers\Schemas\SpeakerForm;
use App\Filament\Resources\Speakers\Tables\SpeakersTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SpeakersRelationManager extends RelationManager
{
    protected static string $relationship = 'speakers';

    public function form(Schema $schema): Schema { return SpeakerForm::configure($schema); }
    public function table(Table $table): Table { return SpeakersTable::configure($table); }
}
