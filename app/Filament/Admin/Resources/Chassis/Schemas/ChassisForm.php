<?php

namespace App\Filament\Admin\Resources\Chassis\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Company;
use App\Enums\EntityStatusEnum;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChassisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->label('Empresa')
                    ->options(Company::query()->pluck('business_name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('license_plate')
                    ->label('Placa')
                    ->required(),
                Select::make('status')
                    ->required()
                    ->label('Estado')
                    ->options(EntityStatusEnum::class),
                TextInput::make('vehicle_type')
                    ->label('Tipo de Vehículo'),
                TextInput::make('axle_count')
                    ->label('Número de Ejes')
                    ->numeric(),
                    TextInput::make('tare')
                    ->label('Tara')
                    ->numeric(),
                    TextInput::make('safe_weight')
                    ->label('Peso Seguro')
                    ->numeric(),
                    TextInput::make('height')
                    ->label('Alto')
                    ->numeric(),
                    TextInput::make('length')
                    ->label('Largo')
                    ->numeric(),
                    TextInput::make('width')
                    ->label('Ancho')
                    ->numeric(),
                    TextInput::make('material')
                    ->label('Material'),
                Toggle::make('is_insulated')
                    ->label('¿Es Aislado?')
                    ->required(),
                    Toggle::make('accepts_20ft')
                    ->label('¿Acepta 20\'?')
                    ->required(),
                Toggle::make('accepts_40ft')
                    ->label('¿Acepta 40\'?')
                    ->required(),
                Toggle::make('has_bonus')
                    ->label('¿Tiene Bono?')
                    ->required()
            ]);
    }
}
