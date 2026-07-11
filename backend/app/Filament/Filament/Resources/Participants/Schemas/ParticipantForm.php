<?php

namespace App\Filament\Resources\Participants\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Data Peserta')->schema([
                Select::make('event_id')->relationship('event', 'name')->required()->searchable()->live(),
                Select::make('division_id')
                    ->relationship('division', 'class_name')
                    ->label('Kelas Pertandingan (Sport)')
                    ->helperText('Wajib untuk event Sport. Kosongkan untuk event lain.')
                    ->searchable()
                    ->preload(),
                Select::make('contingent_id')
                    ->relationship('contingent', 'name')
                    ->label('Kontingen')
                    ->searchable()->preload(),
                TextInput::make('name')->required(),
                Select::make('gender')->options([
                    'PUTRA' => 'Putra', 'PUTRI' => 'Putri', 'MIXED' => 'Campuran',
                ])->default('PUTRA')->required(),
                DatePicker::make('birth_date')->label('Tanggal Lahir'),
            ])->columns(2),

            Section::make('Kontak')->schema([
                TextInput::make('email')->email(),
                TextInput::make('phone')->label('Telepon'),
            ])->columns(2),

            Section::make('Khusus MICE / Travel (opsional)')
                ->schema([
                    TextInput::make('job_title')->label('Jabatan/Instansi (MICE)'),
                    TextInput::make('id_doc_number')->label('No. KTP/Passport (Travel)'),
                    TextInput::make('emergency_contact')->label('Kontak Darurat (Travel)'),
                ])->columns(3),

            Section::make('Status')->schema([
                Select::make('status')->options([
                    'PENDING' => 'Menunggu Verifikasi',
                    'APPROVED' => 'Disetujui',
                    'REJECTED' => 'Ditolak',
                    'WITHDRAWN' => 'Mundur',
                ])->default('PENDING')->required(),
                TextInput::make('seed')->label('Seed Bracket')->numeric()->helperText('Untuk seeding knockout bracket.'),
            ])->columns(2),
        ]);
    }
}
