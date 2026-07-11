<?php

namespace App\Filament\Resources\EventSessions;

use App\Filament\Resources\EventSessions\Pages\CreateEventSession;
use App\Filament\Resources\EventSessions\Pages\EditEventSession;
use App\Filament\Resources\EventSessions\Pages\ListEventSessions;
use App\Filament\Resources\EventSessions\Schemas\EventSessionForm;
use App\Filament\Resources\EventSessions\Tables\EventSessionsTable;
use App\Models\EventSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EventSessionResource extends Resource
{
    protected static ?string $model = EventSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EventSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventSessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventSessions::route('/'),
            'create' => CreateEventSession::route('/create'),
            'edit' => EditEventSession::route('/{record}/edit'),
        ];
    }
}
