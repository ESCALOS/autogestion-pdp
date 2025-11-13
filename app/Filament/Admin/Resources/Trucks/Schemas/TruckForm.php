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
                Select::make('status')
                    ->label('Estado')
                    ->required()
                    ->options(EntityStatusEnum::class),
                TextInput::make('nationality'),
                Toggle::make('is_internal')
                    ->label('Interno')
                    ->required(),
                TextInput::make('truck_type'),
                Toggle::make('has_bonus')
                    ->label('Tiene Bono')
                    ->required(),
                TextInput::make('tare')
                    ->label('Tara (kg)')
                    ->numeric(),
            ]);
    }
}
