<?php

namespace App\Filament\Resources\MatchModels;

use App\Filament\Resources\MatchModels\Pages\CreateMatchModel;
use App\Filament\Resources\MatchModels\Pages\EditMatchModel;
use App\Filament\Resources\MatchModels\Pages\ListMatchModels;
use App\Filament\Resources\MatchModels\Schemas\MatchModelForm;
use App\Filament\Resources\MatchModels\Tables\MatchModelsTable;
use App\Models\MatchModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MatchModelResource extends Resource
{
    protected static ?string $model = MatchModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::SquaresPlus;


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
        return MatchModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MatchModelsTable::configure($table);
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
            'index' => ListMatchModels::route('/'),
            'create' => CreateMatchModel::route('/create'),
            'edit' => EditMatchModel::route('/{record}/edit'),
        ];
    }
}
