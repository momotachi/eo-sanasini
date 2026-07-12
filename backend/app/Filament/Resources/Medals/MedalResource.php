<?php

namespace App\Filament\Resources\Medals;

use App\Filament\Resources\Medals\Pages\CreateMedal;
use App\Filament\Resources\Medals\Pages\EditMedal;
use App\Filament\Resources\Medals\Pages\ListMedals;
use App\Filament\Resources\Medals\Schemas\MedalForm;
use App\Filament\Resources\Medals\Tables\MedalsTable;
use App\Models\Medal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MedalResource extends Resource
{
    protected static ?string $model = Medal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Star;


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
        return MedalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedalsTable::configure($table);
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
            'index' => ListMedals::route('/'),
            'create' => CreateMedal::route('/create'),
            'edit' => EditMedal::route('/{record}/edit'),
        ];
    }
}
