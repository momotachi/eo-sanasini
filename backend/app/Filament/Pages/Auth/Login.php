<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Custom Login Filament — pre-fill default superadmin credentials.
 * Sementara aktif untuk semua env (dev). Untuk production, ubah condition env().
 */
class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        $isDev = true; // sementara: aktif semua env

        return $schema
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->default($isDev ? 'superadmin@sanasini.id' : null)
                    ->autocomplete('email'),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->default($isDev ? 'SuperAdmin2026!' : null)
                    ->autocomplete('current-password'),
            ])
            ->statePath('data');
    }

    public function getSubHeading(): ?string
    {
        return 'DEV: kredensial superadmin sudah terisi. Klik "Masuk" langsung.';
    }
}
