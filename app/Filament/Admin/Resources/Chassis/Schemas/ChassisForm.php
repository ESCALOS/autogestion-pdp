<?php

namespace App\Filament\Admin\Resources\Chassis\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Company;
use App\Enums\EntityStatusEnum;
use Dom\Text;
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
                Select::make('vehicle_type')
                    ->label('Tipo de Vehículo')
                    ->options(\App\Enums\VehicleTypeEnum::class)
                    ->searchable()
                    ->required()
                    ->preload()
                    ->native(false),                    
                TextInput::make('axle_count')
                    ->label('Número de Ejes')
                    ->required()
                    ->numeric(),
                TextInput::make('tare')
                    ->label('Peso Neto')
                    ->numeric()
                    ->required()
                    ->step(0.001)
                    ->minValue(0)
                    ->maxValue(99.999)
                    ->suffix('Toneladas'),             
                TextInput::make('safe_weight')
                    ->label('Peso Bruto')
                    ->numeric()
                    ->required()
                    ->step(0.001)
                    ->minValue(0)
                    ->maxValue(99.999)
                    ->suffix('Toneladas'),
                TextInput::make('length')
                    ->label('Largo')
                    ->numeric()
                    ->step(0.01)
                    ->required()
                    ->minValue(0)
                    ->maxValue(99.99)
                    ->suffix('Metros'),
                TextInput::make('width')
                    ->label('Ancho')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->required()
                    ->maxValue(99.99)
                    ->suffix('Metros'),
                TextInput::make('height')
                    ->label('Alto')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->required()
                    ->maxValue(99.99)
                    ->suffix('Metros'),
                Select::make('material')
                    ->label('Material')
                    ->required()
                    ->options([
                        'Acero' => 'Acero',
                        'Aluminio' => 'Aluminio',
                        'Fibra de Vidrio' => 'Fibra de Vidrio',
                        'Madera' => 'Madera',
                        'Otro' => 'Otro',
                    ]),
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
                    ->required(),
                    Select::make('status')
                        ->required()
                        ->label('Estado')
                        ->options(EntityStatusEnum::class),
            ]);
    }
}
