<?php

namespace App\Filament\Resources\Venues\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VenuesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->weight('bold'),
            TextColumn::make('area')->toggleable(),
            TextColumn::make('event.name')->limit(30),
        ])->filters([
            SelectFilter::make('event')->relationship('event', 'name'),
        ])->actions([\Filament\Actions\EditAction::make()])
          ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }
}
