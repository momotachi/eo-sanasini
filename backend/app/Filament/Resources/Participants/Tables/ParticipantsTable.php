<?php

namespace App\Filament\Resources\Participants\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('event.name')->label('Event')->limit(25)->sortable()->toggleable(),
                TextColumn::make('division.discipline')->label('Cabang')->toggleable(),
                TextColumn::make('division.class_name')->label('Kelas')->limit(20)->toggleable(),
                TextColumn::make('contingent.name')->label('Kontingen')->limit(20)->toggleable(),
                TextColumn::make('gender')->badge()->formatStateUsing(fn(string $s) => ucfirst(strtolower($s))),
                TextColumn::make('status')->badge()->color(fn(string $s) => match ($s) {
                    'APPROVED' => 'success',
                    'PENDING' => 'warning',
                    'REJECTED' => 'danger',
                    'WITHDRAWN' => 'gray',
                })->formatStateUsing(fn(string $s) => match ($s) {
                    'PENDING' => 'Menunggu', 'APPROVED' => 'Disetujui',
                    'REJECTED' => 'Ditolak', 'WITHDRAWN' => 'Mundur',
                }),
                TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('event')->relationship('event', 'name'),
                SelectFilter::make('status')->options([
                    'PENDING' => 'Menunggu', 'APPROVED' => 'Disetujui',
                    'REJECTED' => 'Ditolak', 'WITHDRAWN' => 'Mundur',
                ]),
                SelectFilter::make('contingent')->relationship('contingent', 'name'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => $record->status !== 'APPROVED')
                    ->action(fn($record) => $record->update(['status' => 'APPROVED']))
                    ->requiresConfirmation(),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn($record) => $record->status !== 'REJECTED')
                    ->action(fn($record) => $record->update(['status' => 'REJECTED']))
                    ->requiresConfirmation(),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    BulkAction::make('approveAll')
                        ->label('Setujui semua')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn(Collection $records) => $records->each->update(['status' => 'APPROVED']))
                        ->requiresConfirmation(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
