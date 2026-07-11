<?php

namespace App\Filament\Resources\TicketTypes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TicketTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tiket')->schema([
                Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
                TextInput::make('name')->required()->label('Nama Tiket')->placeholder('Early Bird, Regular, VIP'),
                TextInput::make('price')->numeric()->prefix('Rp')->required()->label('Harga'),
                Select::make('currency')->options(['IDR' => 'IDR (Rp)', 'USD' => 'USD ($)'])->default('IDR'),
                TextInput::make('quota')->numeric()->label('Kuota'),
                TextInput::make('description')->label('Deskripsi')->columnSpanFull(),
                DateTimePicker::make('sale_start')->label('Penjualan Mulai'),
                DateTimePicker::make('sale_end')->label('Penjualan Selesai'),
            ])->columns(2),
        ]);
    }
}
