<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\ItineraryItems\Schemas\ItineraryItemForm;
use App\Filament\Resources\ItineraryItems\Tables\ItineraryItemsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ItineraryRelationManager extends RelationManager
{
    protected static string $relationship = 'itinerary';

    public function form(Schema $schema): Schema { return ItineraryItemForm::configure($schema); }
    public function table(Table $table): Table { return ItineraryItemsTable::configure($table); }
}
