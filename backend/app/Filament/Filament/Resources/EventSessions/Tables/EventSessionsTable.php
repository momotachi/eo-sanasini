<?php

namespace App\Filament\Resources\EventSessions\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('start_time')
            ->columns([
                TextColumn::make('start_time')->dateTime('d M, H:i')->sortable()->weight('bold'),
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('speaker.name')->label('Pembicara')->limit(20),
                TextColumn::make('room')->badge()->toggleable(),
                TextColumn::make('track')->badge()->color('primary')->toggleable(),
                TextColumn::make('event.name')->label('Event')->limit(25),
            ])->filters([
                SelectFilter::make('event')->relationship('event', 'name'),
                SelectFilter::make('speaker')->relationship('speaker', 'name'),
            ])->actions([\Filament\Tables\Actions\EditAction::make()])
              ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([\Filament\Tables\Actions\DeleteBulkAction::make()])]);
    }
}
