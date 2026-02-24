<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SupplierMachineries;

use App\Filament\Admin\Resources\SupplierMachineries\Pages\EditSupplierMachinery;
use App\Filament\Admin\Resources\SupplierMachineries\Pages\ListSupplierMachineries;
use App\Filament\Admin\Resources\SupplierMachineries\Pages\ViewSupplierMachinery;
use App\Filament\Admin\Resources\SupplierMachineries\Schemas\SupplierMachineryForm;
use App\Filament\Admin\Resources\SupplierMachineries\Schemas\SupplierMachineryInfolist;
use App\Filament\Admin\Resources\SupplierMachineries\Tables\SupplierMachineriesTable;
use App\Models\SupplierMachinery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class SupplierMachineryResource extends Resource
{
    protected static ?string $model = SupplierMachinery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'license_plate';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión de Proveedores';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'maquinaria';

    protected static ?string $pluralLabel = 'maquinaria';

    public static function form(Schema $schema): Schema
    {
        return SupplierMachineryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SupplierMachineryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupplierMachineriesTable::configure($table);
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
            'index' => ListSupplierMachineries::route('/'),
            'view' => ViewSupplierMachinery::route('/{record}'),
            'edit' => EditSupplierMachinery::route('/{record}/edit'),
        ];
    }
}
