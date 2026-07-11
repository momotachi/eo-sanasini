<?php

namespace App\Filament\Resources\Speakers\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SpeakersTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->weight('bold'),
            TextColumn::make('title')->limit(30)->toggleable(),
            TextColumn::make('email')->toggleable(),
            TextColumn::make('sessions_count')->counts('sessions')->label('Sesi')->sortable(),
            TextColumn::make('event.name')->label('Event')->limit(25),
        ])->filters([SelectFilter::make('event')->relationship('event', 'name')])
            ->actions([\Filament\Tables\Actions\EditAction::make()])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([\Filament\Tables\Actions\DeleteBulkAction::make()])]);
    }
}
