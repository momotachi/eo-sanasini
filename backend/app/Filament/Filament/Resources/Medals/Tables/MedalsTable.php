<?php

namespace App\Filament\Resources\Medals\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MedalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('type')
            ->columns([
                TextColumn::make('type')->badge()->color(fn(string $s) => match ($s) {
                    'GOLD' => 'warning', 'SILVER' => 'gray', 'BRONZE' => 'primary',
                })->formatStateUsing(fn(string $s) => match ($s) {
                    'GOLD' => '🥇 Emas', 'SILVER' => '🥈 Perak', 'BRONZE' => '🥉 Perunggu',
                }),
                TextColumn::make('participant.name')->label('Peserta')->weight('bold')->searchable(),
                TextColumn::make('contingent.name')->label('Kontingen')->searchable(),
                TextColumn::make('discipline')->label('Cabang'),
                TextColumn::make('division.class_name')->label('Kelas')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')->options([
                    'GOLD' => '🥇 Emas', 'SILVER' => '🥈 Perak', 'BRONZE' => '🥉 Perunggu',
                ]),
                SelectFilter::make('event')->relationship('event', 'name'),
            ])
            ->actions([\Filament\Tables\Actions\EditAction::make()])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([\Filament\Tables\Actions\DeleteBulkAction::make()])]);
    }
}
