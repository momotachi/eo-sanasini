<?php

namespace App\Filament\Resources\TicketTypes\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->weight('bold'),
            TextColumn::make('price')->money('IDR')->sortable(),
            TextColumn::make('quota')->label('Kuota')->toggleable(),
            TextColumn::make('sold_count')->label('Terjual')->color(fn($r) => $r->quota && $r->sold_count >= $r->quota ? 'danger' : 'success'),
            TextColumn::make('event.name')->label('Event')->limit(25),
        ])->filters([SelectFilter::make('event')->relationship('event', 'name')])
            ->actions([\Filament\Actions\EditAction::make()])
            ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }
}
