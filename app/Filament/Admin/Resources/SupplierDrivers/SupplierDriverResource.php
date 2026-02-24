<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SupplierDrivers;

use App\Filament\Admin\Resources\SupplierDrivers\Pages\EditSupplierDriver;
use App\Filament\Admin\Resources\SupplierDrivers\Pages\ListSupplierDrivers;
use App\Filament\Admin\Resources\SupplierDrivers\Pages\ViewSupplierDriver;
use App\Filament\Admin\Resources\SupplierDrivers\Schemas\SupplierDriverForm;
use App\Filament\Admin\Resources\SupplierDrivers\Schemas\SupplierDriverInfolist;
use App\Filament\Admin\Resources\SupplierDrivers\Tables\SupplierDriversTable;
use App\Models\SupplierDriver;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class SupplierDriverResource extends Resource
{
    protected static ?string $model = SupplierDriver::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión de Proveedores';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'conductor';

    protected static ?string $pluralLabel = 'conductores';

    public static function form(Schema $schema): Schema
    {
        return SupplierDriverForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SupplierDriverInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupplierDriversTable::configure($table);
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
            'index' => ListSupplierDrivers::route('/'),
            'view' => ViewSupplierDriver::route('/{record}'),
            'edit' => EditSupplierDriver::route('/{record}/edit'),
        ];
    }
}
