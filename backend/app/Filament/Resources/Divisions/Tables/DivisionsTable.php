<?php

namespace App\Filament\Resources\Divisions\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DivisionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('discipline')
            ->columns([
                TextColumn::make('discipline')->label('Cabang')->searchable()->sortable()->weight('bold'),
                TextColumn::make('age_category')->label('Kategori')->sortable(),
                TextColumn::make('gender')->badge()->formatStateUsing(fn(string $s) => ucfirst(strtolower($s))),
                TextColumn::make('class_name')->label('Kelas')->searchable(),
                TextColumn::make('format')->badge()->color(fn(string $s) => match ($s) {
                    'GROUP_KNOCKOUT' => 'primary',
                    'FULL_KNOCKOUT' => 'warning',
                    'SCORING' => 'success',
                    'ROUND_ROBIN' => 'gray',
                    default => 'gray',
                })->formatStateUsing(fn(string $s) => str_replace('_', ' ', ucfirst(strtolower($s)))),
                TextColumn::make('event.name')->label('Event')->limit(25)->toggleable(),
                TextColumn::make('participants_count')->counts('participants')->label('Peserta')->sortable(),
            ])
            ->filters([
                SelectFilter::make('event')->relationship('event', 'name'),
                SelectFilter::make('discipline'),
                SelectFilter::make('format')->options([
                    'FULL_KNOCKOUT' => 'Full Knockout', 'GROUP_KNOCKOUT' => 'Grup → Knockout',
                    'ROUND_ROBIN' => 'Liga', 'SCORING' => 'Penilaian', 'NON_COMPETITIVE' => 'Non-Kompetitif',
                ]),
            ])
            ->actions([
                \App\Filament\Resources\Divisions\Tables\Actions\GenerateBracketAction::make()
                    ->visible(fn($record) => in_array($record->format, ['FULL_KNOCKOUT', 'GROUP_KNOCKOUT', 'ROUND_ROBIN'])),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
