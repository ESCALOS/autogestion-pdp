<?php

namespace App\Filament\Admin\Resources\Trucks\Schemas;

use App\Enums\EntityStatusEnum;
use App\Models\Company;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TruckForm
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
                    Select::make('nationality')
                    ->label('Nacionalidad')
                    ->options([
                        'Peruana' => 'Peruana',
                        'Extranjera' => 'Extranjera',
                    ]),
                Select::make('truck_type')
                    ->label('Tipo de Camión')
                    ->options(\App\Enums\TruckTypeEnum::class)
                    ->searchable()
                    ->preload()
                    ->native(false),
                TextInput::make('tare')
                    ->label('Peso Neto')
                    ->numeric()
                    ->required()
                    ->step(0.001)
                    ->minValue(0)
                    ->maxValue(99.999)
                    ->suffix('Toneladas'),
                Select::make('status')
                    ->label('Estado')
                    ->required()
                    ->options(EntityStatusEnum::class),                                    
                Toggle::make('is_internal')
                    ->label('Interno')
                    ->required(),
                Toggle::make('has_bonus')
                    ->label('Tiene Bono')
                    ->required(),
            ]);
    }
}
