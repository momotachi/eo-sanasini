<?php

namespace App\Filament\Resources\ItineraryItems\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItineraryItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort(['day', 'time'])
            ->columns([
                TextColumn::make('day')->label('Hari')->sortable(),
                TextColumn::make('time')->dateTime('d M, H:i')->sortable()->weight('bold'),
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('location')->toggleable(),
                TextColumn::make('transport_mode')->badge()->toggleable(),
                TextColumn::make('event.name')->label('Event')->limit(25),
            ])->filters([SelectFilter::make('event')->relationship('event', 'name')])
            ->actions([\Filament\Tables\Actions\EditAction::make()])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([\Filament\Tables\Actions\DeleteBulkAction::make()])]);
    }
}
