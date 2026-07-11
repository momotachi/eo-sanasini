<?php

namespace App\Filament\Resources\Venues\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VenueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
            TextInput::make('name')->required()->label('Nama Venue/Arena')->placeholder('cth: Mat A, Arena Utama'),
            TextInput::make('area')->label('Area')->placeholder('cth: Gedung A'),
        ]);
    }
}
