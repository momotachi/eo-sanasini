<?php

namespace App\Filament\Resources\MatchModels\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MatchModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_at')
            ->columns([
                TextColumn::make('division.discipline')->label('Cabang')->toggleable(),
                TextColumn::make('round')->badge()->formatStateUsing(fn(string $s) => str_replace('_', ' ', ucfirst(strtolower($s)))),
                TextColumn::make('participantA.name')->label('A')->limit(15),
                TextColumn::make('participantB.name')->label('B')->limit(15),
                TextColumn::make('winner.name')->label('Pemenang')->limit(15)->color('success')->weight('bold'),
                TextColumn::make('status')->badge()->color(fn(string $s) => match ($s) {
                    'COMPLETED' => 'success', 'ONGOING' => 'warning',
                    'BYE' => 'gray', default => 'primary',
                })->formatStateUsing(fn(string $s) => ucfirst(strtolower($s))),
                TextColumn::make('scheduled_at')->dateTime('d M, H:i')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('division')->relationship('division', 'class_name'),
                SelectFilter::make('status')->options([
                    'SCHEDULED' => 'Terjadwal', 'ONGOING' => 'Berlangsung',
                    'COMPLETED' => 'Selesai', 'BYE' => 'BYE',
                ]),
            ])
            ->actions([
                \App\Filament\Resources\MatchModels\Tables\Actions\SetWinnerAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }
}
