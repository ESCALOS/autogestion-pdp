<?php

namespace App\Filament\Admin\Resources\Companies\Schemas;

use App\Enums\CompanyStatusEnum;
use App\Enums\CompanyTypeEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ruc')
                    ->label('RUC')
                    ->required()
                    ->maxLength(11),

                TextInput::make('business_name')
                    ->label('Razón Social')
                    ->required()
                    ->maxLength(255),

                Select::make('type')
                    ->label('Tipo de Empresa')
                    ->options(CompanyTypeEnum::class),

                Select::make('status')
                    ->label('Estado')
                    ->options(CompanyStatusEnum::class)
                    ->default(CompanyStatusEnum::PENDIENTE),

                Toggle::make('is_active')
                    ->label('¿Está Activa?')
                    ->default(true),
            ]);
    }
}
