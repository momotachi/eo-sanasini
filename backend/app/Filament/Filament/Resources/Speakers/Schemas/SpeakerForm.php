<?php

namespace App\Filament\Resources\Speakers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SpeakerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
            TextInput::make('name')->required(),
            TextInput::make('title')->label('Jabatan/Gelar')->placeholder('CEO PT X, Prof. Dr.'),
            Textarea::make('bio')->rows(3)->columnSpanFull(),
            TextInput::make('photo_url')->label('URL Foto'),
            TextInput::make('email')->email(),
        ]);
    }
}
