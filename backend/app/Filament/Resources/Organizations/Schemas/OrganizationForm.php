<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profil Organisasi')->schema([
                TextInput::make('name')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                TextInput::make('tagline')->columnSpanFull(),
                Textarea::make('about')->rows(4)->columnSpanFull(),
            ])->columns(2),
            Section::make('Branding & Kontak')->schema([
                TextInput::make('logo_url')->label('Logo URL')->columnSpanFull(),
                TextInput::make('website'),
                TextInput::make('instagram'),
                TextInput::make('email')->email(),
                TextInput::make('phone'),
                TextInput::make('address')->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
