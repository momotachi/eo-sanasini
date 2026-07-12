<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Tenants\Schemas\TenantForm;
use App\Filament\Resources\Tenants\Tables\TenantsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TenantsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenants';

    public function form(Schema $schema): Schema { return TenantForm::configure($schema); }
    public function table(Table $table): Table { return TenantsTable::configure($table); }
}
