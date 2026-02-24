<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MachineryRequirements;

use App\Filament\Admin\Resources\MachineryRequirements\Pages\CreateMachineryRequirement;
use App\Filament\Admin\Resources\MachineryRequirements\Pages\EditMachineryRequirement;
use App\Filament\Admin\Resources\MachineryRequirements\Pages\ListMachineryRequirements;
use App\Filament\Admin\Resources\MachineryRequirements\Pages\ViewMachineryRequirement;
use App\Filament\Admin\Resources\MachineryRequirements\Schemas\MachineryRequirementForm;
use App\Filament\Admin\Resources\MachineryRequirements\Schemas\MachineryRequirementInfolist;
use App\Filament\Admin\Resources\MachineryRequirements\Tables\MachineryRequirementsTable;
use App\Models\MachineryRequirement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class MachineryRequirementResource extends Resource
{
    protected static ?string $model = MachineryRequirement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $recordTitleAttribute = 'vessel_name';

    protected static string|UnitEnum|null $navigationGroup = 'Maquinaria';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Requerimiento de Maquinaria';

    protected static ?string $pluralModelLabel = 'Requerimientos de Maquinaria';

    public static function form(Schema $schema): Schema
    {
        return MachineryRequirementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MachineryRequirementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MachineryRequirementsTable::configure($table);
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
            'index' => ListMachineryRequirements::route('/'),
            'create' => CreateMachineryRequirement::route('/create'),
            'view' => ViewMachineryRequirement::route('/{record}'),
            'edit' => EditMachineryRequirement::route('/{record}/edit'),
        ];
    }
}
