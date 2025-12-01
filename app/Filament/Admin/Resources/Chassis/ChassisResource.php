<?php

namespace App\Filament\Admin\Resources\Chassis;

use App\Filament\Admin\Resources\Chassis\Pages\CreateChassis;
use App\Filament\Admin\Resources\Chassis\Pages\EditChassis;
use App\Filament\Admin\Resources\Chassis\Pages\ListChassis;
use App\Filament\Admin\Resources\Chassis\Pages\ViewChassis;
use App\Filament\Admin\Resources\Chassis\Schemas\ChassisForm;
use App\Filament\Admin\Resources\Chassis\Schemas\ChassisInfolist;
use App\Filament\Admin\Resources\Chassis\Tables\ChassisTable;
use App\Models\Chassis;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ChassisResource extends Resource
{
    protected static ?string $model = Chassis::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'license_plate';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión de Transportes';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'carreta';

    public static function form(Schema $schema): Schema
    {
        return ChassisForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChassisInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChassisTable::configure($table);
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
            'index' => ListChassis::route('/'),
            // 'create' => CreateChassis::route('/create'),
            'view' => ViewChassis::route('/{record}'),
            'edit' => EditChassis::route('/{record}/edit'),
        ];
    }
}
