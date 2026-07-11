<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'SPORT' => 'primary',
                        'FESTIVAL' => 'warning',
                        'MICE' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'SPORT' => '🏆 Sport',
                        'FESTIVAL' => '🎪 Festival',
                        'MICE' => '💼 MICE',
                        'OTHER' => '🎒 Other',
                        default => $state,
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'REGISTRATION_OPEN' => 'warning',
                        'ONGOING' => 'success',
                        'COMPLETED' => 'gray',
                        'CANCELLED' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => str_replace('_', ' ', ucfirst(strtolower($state)))),
                TextColumn::make('start_date')->dateTime('d M Y')->sortable()->label('Mulai'),
                TextColumn::make('venue')->limit(20)->toggleable(),
                IconColumn::make('is_public')->boolean()->label('Publik'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'SPORT' => '🏆 Sport',
                        'FESTIVAL' => '🎪 Festival',
                        'MICE' => '💼 MICE',
                        'OTHER' => '🎒 Other',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'DRAFT' => 'Draft',
                        'REGISTRATION_OPEN' => 'Pendaftaran Dibuka',
                        'UPCOMING' => 'Segera Hadir',
                        'ONGOING' => 'Sedang Berlangsung',
                        'COMPLETED' => 'Selesai',
                        'CANCELLED' => 'Dibatalkan',
                    ]),
                SelectFilter::make('organization')->relationship('organization', 'name'),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
