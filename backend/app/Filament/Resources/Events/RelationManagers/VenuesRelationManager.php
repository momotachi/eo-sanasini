<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Venues\Schemas\VenueForm;
use App\Filament\Resources\Venues\Tables\VenuesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VenuesRelationManager extends RelationManager
{
    protected static string $relationship = 'venues';

    public function form(Schema $schema): Schema
    {
        return VenueForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return VenuesTable::configure($table);
    }
}
