<?php

namespace App\Filament\Resources\ItineraryItems\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ItineraryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
            TextInput::make('day')->numeric()->required()->label('Hari ke-'),
            DateTimePicker::make('time')->required()->label('Waktu'),
            TextInput::make('title')->required()->label('Aktivitas'),
            TextInput::make('location')->label('Lokasi'),
            Select::make('transport_mode')->options([
                'Bus' => 'Bus', 'Pesawat' => 'Pesawat', 'Kereta' => 'Kereta',
                'Kapal' => 'Kapal', 'Jalan kaki' => 'Jalan kaki', 'Lainnya' => 'Lainnya',
            ])->label('Transportasi'),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }
}
