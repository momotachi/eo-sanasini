<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\StagePrograms\Schemas\StageProgramForm;
use App\Filament\Resources\StagePrograms\Tables\StageProgramsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class StageProgramsRelationManager extends RelationManager
{
    protected static string $relationship = 'stagePrograms';

    public function form(Schema $schema): Schema { return StageProgramForm::configure($schema); }
    public function table(Table $table): Table { return StageProgramsTable::configure($table); }
}
