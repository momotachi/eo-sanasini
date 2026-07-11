<?php

namespace App\Filament\Resources\MatchModels\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MatchModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Match')->schema([
                Select::make('division_id')->relationship('division', 'class_name')->required()->searchable(),
                Select::make('round')->options([
                    'GROUP_STAGE' => 'Babak Grup', 'ROUND_OF_16' => '16 Besar',
                    'QUARTERFINAL' => 'Perempat Final', 'SEMIFINAL' => 'Semi Final',
                    'FINAL' => 'Final', 'THIRD_PLACE' => 'Perebutan ke-3',
                ])->required(),
                TextInput::make('bracket_position')->numeric()->label('Posisi Bracket'),
                TextInput::make('group_label')->label('Label Grup (cth: A, B, C)'),
            ])->columns(2),

            Section::make('Peserta')->schema([
                Select::make('participant_a_id')->relationship('participantA', 'name')->label('Peserta A')->searchable(),
                Select::make('participant_b_id')->relationship('participantB', 'name')->label('Peserta B')->searchable(),
            ])->columns(2),

            Section::make('Hasil')->schema([
                Select::make('winner_id')->relationship('winner', 'name')->label('Pemenang')->searchable(),
                Select::make('status')->options([
                    'SCHEDULED' => 'Terjadwal', 'ONGOING' => 'Berlangsung',
                    'COMPLETED' => 'Selesai', 'BYE' => 'BYE (walkover)',
                ])->default('SCHEDULED')->required(),
            ])->columns(2),

            Section::make('Jadwal & Venue')->schema([
                DateTimePicker::make('scheduled_at')->label('Waktu Pertandingan'),
                Select::make('venue_id')->relationship('venue', 'name')->label('Venue')->searchable(),
                TextInput::make('notes')->label('Catatan'),
            ])->columns(2),
        ]);
    }
}
