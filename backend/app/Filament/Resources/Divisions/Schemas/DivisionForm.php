<?php

namespace App\Filament\Resources\Divisions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DivisionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kelas Pertandingan')->schema([
                Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
                TextInput::make('discipline')->label('Cabang')->required()->placeholder('cth: Kyorugi, Poomsae, Futsal'),
                TextInput::make('age_category')->label('Kategori Umur')->required()->placeholder('cth: Cadet, Junior, Senior'),
                Select::make('gender')->options([
                    'PUTRA' => 'Putra', 'PUTRI' => 'Putri', 'MIXED' => 'Campuran',
                ])->required()->default('PUTRA'),
                TextInput::make('class_name')->label('Kelas')->required()->placeholder('cth: -45kg, Individual'),
                Select::make('format')->options([
                    'FULL_KNOCKOUT' => 'Full Knockout',
                    'GROUP_KNOCKOUT' => 'Grup → Knockout',
                    'ROUND_ROBIN' => 'Liga (Round Robin)',
                    'SCORING' => 'Penilaian Juri',
                    'NON_COMPETITIVE' => 'Non-Kompetitif',
                ])->required()->default('GROUP_KNOCKOUT'),
            ])->columns(2),
        ]);
    }
}
