<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')->badge()->color(fn(string $s) => match ($s) {
                    'SUPER_ADMIN' => 'danger', 'ADMIN' => 'primary', 'STAF' => 'gray',
                })->formatStateUsing(fn(string $s) => match ($s) {
                    'SUPER_ADMIN' => 'Super Admin', 'ADMIN' => 'Admin', 'STAF' => 'Staf',
                }),
                IconColumn::make('is_active')->boolean()->label('Aktif'),
                TextColumn::make('last_login_at')->dateTime('d M Y, H:i')->label('Login Terakhir')->toggleable(),
                TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')->options([
                    'SUPER_ADMIN' => 'Super Admin', 'ADMIN' => 'Admin', 'STAF' => 'Staf',
                ]),
            ])
            ->actions([\Filament\Tables\Actions\EditAction::make()])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([\Filament\Tables\Actions\DeleteBulkAction::make()])]);
    }
}
