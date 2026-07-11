<?php

namespace App\Filament\Resources\Medals\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MedalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
            Select::make('division_id')->relationship('division', 'class_name')->required()->searchable(),
            Select::make('participant_id')->relationship('participant', 'name')->required()->searchable(),
            Select::make('contingent_id')->relationship('contingent', 'name')->searchable(),
            Select::make('type')->options([
                'GOLD' => '🥇 Emas', 'SILVER' => '🥈 Perak', 'BRONZE' => '🥉 Perunggu',
            ])->required(),
            TextInput::make('discipline')->label('Cabang')->required(),
        ]);
    }
}
