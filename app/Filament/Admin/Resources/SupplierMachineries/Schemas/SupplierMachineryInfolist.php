<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SupplierMachineries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class SupplierMachineryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('supplier.business_name')
                    ->label('Proveedor'),
                TextEntry::make('license_plate')
                    ->label('Placa')
                    ->copyable(),
                TextEntry::make('brand')
                    ->label('Marca'),
                TextEntry::make('model')
                    ->label('Modelo'),
                TextEntry::make('year')
                    ->label('Año'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('appeal_token_expires_at')
                    ->label('Token de Apelación Expira')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
