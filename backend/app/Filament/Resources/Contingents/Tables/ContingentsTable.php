<?php

namespace App\Filament\Resources\Contingents\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContingentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('event.name')->label('Event')->limit(30)->sortable(),
                TextColumn::make('type')->badge()->color(fn(string $s) => match ($s) {
                    'CLUB' => 'primary', 'PROVINCE' => 'warning', 'COUNTRY' => 'success', default => 'gray',
                })->formatStateUsing(fn(string $s) => ucfirst(strtolower($s))),
                TextColumn::make('participants_count')->counts('participants')->label('Peserta')->sortable(),
                TextColumn::make('medals_count')->counts('medals')->label('Medali')->sortable(),
            ])
            ->filters([
                SelectFilter::make('event')->relationship('event', 'name'),
                SelectFilter::make('type')->options([
                    'CLUB' => 'Perguruan', 'PROVINCE' => 'Provinsi', 'COUNTRY' => 'Negara', 'OTHER' => 'Lainnya',
                ]),
            ])
            ->actions([\Filament\Tables\Actions\EditAction::make()])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
