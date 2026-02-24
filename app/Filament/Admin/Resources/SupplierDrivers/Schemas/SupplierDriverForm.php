<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SupplierDrivers\Schemas;

use App\Enums\DriverDocumentTypeEnum;
use App\Enums\EntityStatusEnum;
use App\Models\Supplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class SupplierDriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('supplier_id')
                    ->label('Proveedor')
                    ->options(Supplier::query()->pluck('business_name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('document_type')
                    ->label('Tipo de Documento')
                    ->required()
                    ->options(DriverDocumentTypeEnum::class),
                TextInput::make('document_number')
                    ->label('Número de Documento')
                    ->required(),
                TextInput::make('name')
                    ->label('Nombres')
                    ->required(),
                TextInput::make('lastname')
                    ->label('Apellidos')
                    ->required(),
                TextInput::make('license_number')
                    ->label('Número de Licencia')
                    ->required(),
                Select::make('status')
                    ->label('Estado')
                    ->required()
                    ->options(EntityStatusEnum::class),
            ]);
    }
}
