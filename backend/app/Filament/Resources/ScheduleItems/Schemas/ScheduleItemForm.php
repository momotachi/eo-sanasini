<?php

namespace App\Filament\Resources\ScheduleItems\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ScheduleItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
            DateTimePicker::make('time')->required()->label('Waktu'),
            TextInput::make('title')->required()->label('Judul')->columnSpanFull(),
            Select::make('venue_id')->relationship('venue', 'name')->label('Venue')->searchable(),
            TextInput::make('division')->label('Divisi (opsional)'),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }
}
