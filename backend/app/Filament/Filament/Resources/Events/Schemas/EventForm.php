<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                            ->columnSpanFull(),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        Select::make('organization_id')
                            ->relationship('organization', 'name')
                            ->required(),
                        Select::make('type')
                            ->options([
                                'CHAMPIONSHIP' => 'Kejuaraan',
                                'LEAGUE' => 'Liga',
                                'FESTIVAL' => 'Festival',
                                'MICE' => 'Konferensi',
                                'OTHER' => 'Lainnya',
                            ])
                            ->required(),
                        Select::make('category')
                            ->options([
                                'SPORT' => '🏆 Sport / Kompetisi',
                                'FESTIVAL' => '🎪 Festival / Pameran',
                                'MICE' => '💼 Konferensi / MICE',
                                'OTHER' => '🎒 Travel / Gathering',
                            ])
                            ->required()
                            ->helperText('Menentukan modul yang relevan (Division, Tenant, Speaker, dll).'),
                        Select::make('status')
                            ->options([
                                'DRAFT' => 'Draft',
                                'REGISTRATION_OPEN' => 'Pendaftaran Dibuka',
                                'UPCOMING' => 'Segera Hadir',
                                'ONGOING' => 'Sedang Berlangsung',
                                'COMPLETED' => 'Selesai',
                                'CANCELLED' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('DRAFT'),
                        Toggle::make('is_public')->label('Publik (tampil di website)')->default(true),
                    ])->columns(2),

                Section::make('Waktu & Lokasi')
                    ->schema([
                        DateTimePicker::make('start_date')->label('Mulai')->required(),
                        DateTimePicker::make('end_date')->label('Selesai')->required(),
                        TextInput::make('venue')->label('Nama Venue'),
                        TextInput::make('address')->label('Alamat')->columnSpanFull(),
                        TextInput::make('map_url')->label('Link Google Maps')->columnSpanFull(),
                    ])->columns(2),

                Section::make('Konten')
                    ->schema([
                        Textarea::make('description')->label('Deskripsi')->rows(4)->columnSpanFull(),
                        TextInput::make('poster_url')->label('URL Poster')->columnSpanFull(),
                    ])->columns(1),

                Section::make('Kontak Panitia')
                    ->schema([
                        TextInput::make('contact_name')->label('Nama Kontak'),
                        TextInput::make('contact_phone')->label('Telepon'),
                        TextInput::make('contact_email')->label('Email'),
                    ])->columns(3),

                Section::make('Modul Tambahan')
                    ->description('Aktifkan modul tambahan yang muncul di halaman event')
                    ->schema([
                        Toggle::make('modules.registration')->label('Pendaftaran Online')->default(true),
                        Toggle::make('modules.schedule')->label('Jadwal Publik')->default(true),
                        Toggle::make('modules.gallery')->label('Galeri Foto')->default(false),
                        Toggle::make('modules.certificate')->label('Sertifikat Digital')->default(false),
                        Toggle::make('modules.livestream')->label('Link Livestream')->default(false),
                        Toggle::make('modules.merch')->label('Merchandise')->default(false),
                    ])->columns(3),
            ]);
    }
}
