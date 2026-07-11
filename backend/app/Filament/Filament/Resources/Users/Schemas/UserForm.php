<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User')->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')->password()->revealable()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->helperText('Kosongkan jika tidak ingin mengubah password.'),
                Select::make('role')->options([
                    'SUPER_ADMIN' => 'Super Admin (Programmer)',
                    'ADMIN' => 'Admin (EO Sanasini)',
                    'STAF' => 'Staf (Panitia per-event)',
                ])->required(),
                Select::make('is_active')->label('Status')->options([
                    1 => 'Aktif', 0 => 'Nonaktif',
                ])->default(1),
            ])->columns(2),
        ]);
    }
}
