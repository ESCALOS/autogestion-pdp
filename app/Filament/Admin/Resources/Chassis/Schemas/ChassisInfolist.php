<?php

namespace App\Filament\Admin\Resources\Chassis\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ChassisInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('company_id')
                    ->numeric(),
                TextEntry::make('license_plate'),
                TextEntry::make('status'),
                TextEntry::make('vehicle_type')
                    ->placeholder('-'),
                TextEntry::make('axle_count')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('has_bonus')
                    ->boolean(),
                TextEntry::make('tare')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('safe_weight')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('height')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('length')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('width')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_insulated')
                    ->boolean(),
                TextEntry::make('material')
                    ->placeholder('-'),
                IconEntry::make('accepts_20ft')
                    ->boolean(),
                IconEntry::make('accepts_40ft')
                    ->boolean(),
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
