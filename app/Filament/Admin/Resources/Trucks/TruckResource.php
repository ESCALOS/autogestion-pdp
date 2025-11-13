<?php

namespace App\Filament\Admin\Resources\Trucks;

use App\Filament\Admin\Resources\Trucks\Pages\CreateTruck;
use App\Filament\Admin\Resources\Trucks\Pages\EditTruck;
use App\Filament\Admin\Resources\Trucks\Pages\ListTrucks;
use App\Filament\Admin\Resources\Trucks\Pages\ViewTruck;
use App\Filament\Admin\Resources\Trucks\Schemas\TruckForm;
use App\Filament\Admin\Resources\Trucks\Schemas\TruckInfolist;
use App\Filament\Admin\Resources\Trucks\Tables\TrucksTable;
use App\Models\Truck;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TruckResource extends Resource
{
    protected static ?string $model = Truck::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'license_plate';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión de Transportes';

    protected static ?string $modelLabel = 'tracto';

    public static function form(Schema $schema): Schema
    {
        return TruckForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TruckInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrucksTable::configure($table);
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
            'index' => ListTrucks::route('/'),
            // 'create' => CreateTruck::route('/create'),
            'view' => ViewTruck::route('/{record}'),
            'edit' => EditTruck::route('/{record}/edit'),
        ];
    }
}
