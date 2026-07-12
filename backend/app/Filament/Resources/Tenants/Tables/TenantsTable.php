<?php

namespace App\Filament\Resources\Tenants\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->weight('bold'),
            TextColumn::make('category')->badge()->color('warning'),
            TextColumn::make('booth_number')->label('Stan')->toggleable(),
            TextColumn::make('event.name')->label('Event')->limit(25),
        ])->filters([
            SelectFilter::make('event')->relationship('event', 'name'),
        ])->actions([\Filament\Actions\EditAction::make()])
          ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }
}
