<?php

namespace App\Filament\Resources\Contingents;

use App\Filament\Resources\Contingents\Pages\CreateContingent;
use App\Filament\Resources\Contingents\Pages\EditContingent;
use App\Filament\Resources\Contingents\Pages\ListContingents;
use App\Filament\Resources\Contingents\Schemas\ContingentForm;
use App\Filament\Resources\Contingents\Tables\ContingentsTable;
use App\Models\Contingent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContingentResource extends Resource
{
    protected static ?string $model = Contingent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;


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
        return ContingentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContingentsTable::configure($table);
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
            'index' => ListContingents::route('/'),
            'create' => CreateContingent::route('/create'),
            'edit' => EditContingent::route('/{record}/edit'),
        ];
    }
}
