<?php

namespace App\Filament\Admin\Resources\Chassis\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChassisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                TextInput::make('license_plate')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('1'),
                TextInput::make('vehicle_type'),
                TextInput::make('axle_count')
                    ->numeric(),
                Toggle::make('has_bonus')
                    ->required(),
                TextInput::make('tare')
                    ->numeric(),
                TextInput::make('safe_weight')
                    ->numeric(),
                TextInput::make('height')
                    ->numeric(),
                TextInput::make('length')
                    ->numeric(),
                TextInput::make('width')
                    ->numeric(),
                Toggle::make('is_insulated')
                    ->required(),
                TextInput::make('material'),
                Toggle::make('accepts_20ft')
                    ->required(),
                Toggle::make('accepts_40ft')
                    ->required(),
                DateTimePicker::make('appeal_token_expires_at'),
            ]);
    }
}
