<?php

namespace App\Filament\Admin\Resources\Drivers\Schemas;

use App\Enums\DriverDocumentTypeEnum;
use App\Enums\EntityStatusEnum;
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
                    ->required()
                    ->relationship('company', 'business_name')
                    ->columnSpanFull(),
                Select::make('document_type')
                    ->required()
                    ->options(DriverDocumentTypeEnum::class),
                TextInput::make('document_number')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('lastname')
                    ->required(),
                Select::make('status')
                    ->required()
                    ->options(EntityStatusEnum::class),
                TextInput::make('license_number')
                    ->required(),
            ]);
    }
}
