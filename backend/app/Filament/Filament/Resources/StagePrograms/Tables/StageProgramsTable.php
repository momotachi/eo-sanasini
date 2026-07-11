<?php

namespace App\Filament\Resources\StagePrograms\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StageProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('time')
            ->columns([
                TextColumn::make('time')->dateTime('d M, H:i')->sortable()->weight('bold'),
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('performer')->toggleable(),
                TextColumn::make('stage')->badge()->toggleable(),
                TextColumn::make('event.name')->label('Event')->limit(25),
            ])->filters([SelectFilter::make('event')->relationship('event', 'name')])
            ->actions([\Filament\Tables\Actions\EditAction::make()])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([\Filament\Tables\Actions\DeleteBulkAction::make()])]);
    }
}
