<?php

namespace App\Filament\Admin\Resources\Drivers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DriverInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('company.name'),
                TextEntry::make('document_type')
                    ->badge(),
                TextEntry::make('document_number'),
                TextEntry::make('name'),
                TextEntry::make('lastname'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('appeal_token_expires_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('license_number'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
