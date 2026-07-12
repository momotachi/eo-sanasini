<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->description('Slug akan terisi otomatis dari nama event')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                // auto-generate slug dari name (hanya kalau slug masih kosong)
                                if (!$get('slug')) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Otomatis dari nama. Bisa edit manual.')
                            ->suffixAction(Action::make('regenerateSlug')
                                ->icon('heroicon-o-arrow-path')
                                ->tooltip('Buat ulang dari nama')
                                ->action(fn(Get $get, Set $set) => $set('slug', Str::slug($get('name') ?: '')))),
                        Select::make('organization_id')
                            ->relationship('organization', 'name')
                            ->required()
                            ->default(fn() => \App\Models\Organization::value('id')),
                        Select::make('category')
                            ->label('Kategori Event')
                            ->options([
                                'SPORT' => '🏆 Sport / Kompetisi',
                                'FESTIVAL' => '🎪 Festival / Pameran',
                                'MICE' => '💼 Konferensi / MICE',
                                'OTHER' => '🎒 Travel / Gathering',
                            ])
                            ->required()
                            ->live()
                            ->default('SPORT')
                            ->helperText('Menentukan tabs/modul yang muncul. Type akan auto-set sesuai kategori.'),
                        // type auto-set via Model::booted() — disembunyikan supaya tidak bingung
                        Hidden::make('type')->default('CHAMPIONSHIP'),
                        Toggle::make('is_public')->label('Publik (tampil di website)')->default(true),
                    ])->columns(2),

                Section::make('Waktu & Status')
                    ->schema([
                        DateTimePicker::make('start_date')->label('Mulai')->required()->live(),
                        DateTimePicker::make('end_date')->label('Selesai')->required()->live(),
                        Select::make('status')
                            ->options([
                                'DRAFT' => '📋 Draft',
                                'REGISTRATION_OPEN' => '🔔 Pendaftaran Dibuka',
                                'UPCOMING' => '⏰ Segera Hadir',
                                'ONGOING' => '🔴 Sedang Berlangsung',
                                'COMPLETED' => '✅ Selesai',
                                'CANCELLED' => '❌ Dibatalkan',
                            ])
                            ->required()
                            ->default('DRAFT')
                            ->helperText(function (Get $get): string {
                                $start = $get('start_date');
                                $end = $get('end_date');
                                if (!$start || !$end) return 'Status akan auto-sync berdasarkan tanggal saat disimpan.';
                                $now = now();
                                $s = \Illuminate\Support\Carbon::parse($start);
                                $e = \Illuminate\Support\Carbon::parse($end);
                                $auto = $now < $s ? 'UPCOMING (otomatis)' : ($now > $e ? 'COMPLETED (otomatis)' : 'ONGOING (otomatis)');
                                return "Berdasarkan tanggal, seharusnya: $auto. Pilih manual untuk override.";
                            }),
                    ])->columns(2),

                Section::make('Lokasi & Map')
                    ->description('Klik peta untuk set titik lokasi, atau isi manual.')
                    ->schema([
                        TextInput::make('venue')->label('Nama Venue')->placeholder('cth: Istora Senayan'),
                        TextInput::make('address')->label('Alamat Lengkap')->columnSpanFull(),
                        // Map picker: Leaflet inline (tanpa package tambahan, pakai view custom)
                        \Filament\Forms\Components\Field::make('map_picker')
                            ->label('Titik Lokasi (klik peta untuk pilih)')
                            ->view('filament.forms.components.map-picker')
                            ->columnSpanFull(),
                        Hidden::make('latitude'),
                        Hidden::make('longitude'),
                        Hidden::make('map_zoom')->default(13),
                        TextInput::make('map_url')->label('Link Google Maps (opsional)')
                            ->placeholder('https://maps.google.com/...')
                            ->helperText('Diisi otomatis dari titik di peta, atau tempel manual.')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Konten')
                    ->schema([
                        Textarea::make('description')->label('Deskripsi')->rows(4)->columnSpanFull(),
                        TextInput::make('poster_url')->label('URL Poster')->columnSpanFull(),
                    ]),

                Section::make('Kontak Panitia')
                    ->schema([
                        TextInput::make('contact_name')->label('Nama Kontak'),
                        TextInput::make('contact_phone')->label('Telepon'),
                        TextInput::make('contact_email')->label('Email'),
                    ])->columns(3),

                Section::make('Modul Tambahan')
                    ->description('Aktifkan modul yang muncul di halaman publik event')
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
