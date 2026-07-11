<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')->relationship('event', 'name')->required()->searchable(),
            TextInput::make('name')->required()->label('Nama Tenant/Vendor'),
            TextInput::make('category')->label('Kategori')->placeholder('Kuliner, UMKM, Fashion, Teknologi'),
            TextInput::make('booth_number')->label('Nomor Stan'),
            Textarea::make('description')->rows(3)->columnSpanFull(),
            TextInput::make('logo_url')->label('Logo URL'),
            TextInput::make('contact_name')->label('Kontak'),
            TextInput::make('contact_phone')->label('Telepon'),
        ]);
    }
}
