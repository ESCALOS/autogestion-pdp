<?php

namespace App\Filament\Admin\Resources\Drivers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                TextInput::make('document_type')
                    ->required()
                    ->numeric(),
                TextInput::make('document_number')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('lastname')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(1),
                DateTimePicker::make('appeal_token_expires_at'),
                TextInput::make('license_number')
                    ->required(),
            ]);
    }
}
