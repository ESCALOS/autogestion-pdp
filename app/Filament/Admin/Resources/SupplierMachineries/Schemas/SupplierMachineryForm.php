<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SupplierMachineries\Schemas;

use App\Enums\EntityStatusEnum;
use App\Models\Supplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class SupplierMachineryForm
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
                TextInput::make('license_plate')
                    ->label('Placa')
                    ->required()
                    ->maxLength(20),
                TextInput::make('brand')
                    ->label('Marca')
                    ->maxLength(100),
                TextInput::make('model')
                    ->label('Modelo')
                    ->maxLength(100),
                TextInput::make('year')
                    ->label('Año')
                    ->numeric(),
                Select::make('status')
                    ->label('Estado')
                    ->required()
                    ->options(EntityStatusEnum::class),
            ]);
    }
}
