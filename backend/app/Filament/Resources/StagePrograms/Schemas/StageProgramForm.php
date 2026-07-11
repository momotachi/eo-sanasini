<?php

namespace App\Filament\Resources\StagePrograms\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StageProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
            DateTimePicker::make('time')->required()->label('Waktu'),
            TextInput::make('title')->required()->label('Judul/Performa'),
            TextInput::make('performer')->label('Pembawa'),
            TextInput::make('stage')->label('Panggung')->placeholder('cth: Panggung Utama'),
            Textarea::make('description')->rows(2)->columnSpanFull(),
        ]);
    }
}
