<?php

namespace App\Filament\Admin\Resources\Trucks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TruckInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->relationship('company', 'business_name'),
                TextEntry::make('license_plate'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('nationality')
                    ->placeholder('-'),
                IconEntry::make('is_internal')
                    ->boolean(),
                TextEntry::make('truck_type')
                    ->placeholder('-'),
                IconEntry::make('has_bonus')
                    ->boolean(),
                TextEntry::make('tare')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('appeal_token_expires_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
