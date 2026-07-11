<?php

namespace App\Filament\Resources\ScheduleItems\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScheduleItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('time')
            ->columns([
                TextColumn::make('time')->dateTime('d M Y, H:i')->sortable()->weight('bold'),
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('venue.name')->label('Venue')->badge()->toggleable(),
                TextColumn::make('event.name')->label('Event')->limit(25)->toggleable(),
            ])
            ->filters([
                SelectFilter::make('event')->relationship('event', 'name'),
                SelectFilter::make('venue')->relationship('venue', 'name'),
            ])
            ->actions([\Filament\Tables\Actions\EditAction::make()])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([\Filament\Tables\Actions\DeleteBulkAction::make()])]);
    }
}
