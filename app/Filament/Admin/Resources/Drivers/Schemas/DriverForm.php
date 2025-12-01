<?php

namespace App\Filament\Admin\Resources\Drivers\Schemas;

use App\Enums\DriverDocumentTypeEnum;
use App\Enums\EntityStatusEnum;
use App\Models\Company;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DriverForm
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
