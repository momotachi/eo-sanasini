<?php

namespace App\Filament\Resources\Contingents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContingentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kontingen')->schema([
                Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
                TextInput::make('name')->required(),
                Select::make('type')->options([
                    'CLUB' => 'Perguruan',
                    'PROVINCE' => 'Provinsi',
                    'COUNTRY' => 'Negara',
                    'OTHER' => 'Lainnya',
                ])->default('CLUB'),
                TextInput::make('logo_url')->label('Logo URL'),
                TextInput::make('contact_name')->label('Nama Kontak'),
                TextInput::make('contact_phone')->label('Telepon'),
            ])->columns(2),
        ]);
    }
}
