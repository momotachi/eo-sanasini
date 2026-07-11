<?php

namespace App\Filament\Resources\StagePrograms;

use App\Filament\Resources\StagePrograms\Pages\CreateStageProgram;
use App\Filament\Resources\StagePrograms\Pages\EditStageProgram;
use App\Filament\Resources\StagePrograms\Pages\ListStagePrograms;
use App\Filament\Resources\StagePrograms\Schemas\StageProgramForm;
use App\Filament\Resources\StagePrograms\Tables\StageProgramsTable;
use App\Models\StageProgram;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StageProgramResource extends Resource
{
    protected static ?string $model = StageProgram::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StageProgramForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StageProgramsTable::configure($table);
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
            'index' => ListStagePrograms::route('/'),
            'create' => CreateStageProgram::route('/create'),
            'edit' => EditStageProgram::route('/{record}/edit'),
        ];
    }
}
