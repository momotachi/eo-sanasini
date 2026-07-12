<?php

namespace App\Filament\Resources\ScheduleItems;

use App\Filament\Resources\ScheduleItems\Pages\CreateScheduleItem;
use App\Filament\Resources\ScheduleItems\Pages\EditScheduleItem;
use App\Filament\Resources\ScheduleItems\Pages\ListScheduleItems;
use App\Filament\Resources\ScheduleItems\Schemas\ScheduleItemForm;
use App\Filament\Resources\ScheduleItems\Tables\ScheduleItemsTable;
use App\Models\ScheduleItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScheduleItemResource extends Resource
{
    protected static ?string $model = ScheduleItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calendar;


    public static function getNavigationGroup(): ?string
    {
        return 'Sport Module';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    public static function form(Schema $schema): Schema
    {
        return ScheduleItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScheduleItemsTable::configure($table);
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
            'index' => ListScheduleItems::route('/'),
            'create' => CreateScheduleItem::route('/create'),
            'edit' => EditScheduleItem::route('/{record}/edit'),
        ];
    }
}
