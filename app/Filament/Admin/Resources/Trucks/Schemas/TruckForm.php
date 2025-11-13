<?php

namespace App\Filament\Admin\Resources\Trucks\Schemas;

use App\Enums\EntityStatusEnum;
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
                TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                TextInput::make('license_plate')
                    ->required(),
                Select::make('status')
                    ->required()
                    ->options(EntityStatusEnum::class),
                TextInput::make('nationality'),
                Toggle::make('is_internal')
                    ->required(),
                TextInput::make('truck_type'),
                Toggle::make('has_bonus')
                    ->required(),
                TextInput::make('tare')
                    ->numeric(),
                DateTimePicker::make('appeal_token_expires_at'),
            ]);
    }
}
