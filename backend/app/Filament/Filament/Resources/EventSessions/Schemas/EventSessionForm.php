<?php

namespace App\Filament\Resources\EventSessions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EventSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
            TextInput::make('title')->required()->label('Judul Sesi'),
            Textarea::make('description')->rows(3)->columnSpanFull(),
            DateTimePicker::make('start_time')->required()->label('Mulai'),
            DateTimePicker::make('end_time')->required()->label('Selesai'),
            TextInput::make('room')->label('Ruang'),
            Select::make('speaker_id')->relationship('speaker', 'name')->label('Pembicara')->searchable(),
            TextInput::make('track')->label('Track')->placeholder('Keynote, Workshop, Track A'),
            TextInput::make('capacity')->numeric()->label('Kapasitas'),
        ]);
    }
}
