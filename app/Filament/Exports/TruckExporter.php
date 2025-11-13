<?php

namespace App\Filament\Exports;

use App\Models\Truck;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class TruckExporter extends Exporter
{
    protected static ?string $model = Truck::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('company.business_name')
                ->label('Empresa'),
            ExportColumn::make('license_plate')
                ->label('Placa'),
            ExportColumn::make('status')
                ->label('Estado')
                ->state(fn ($record) => $record->status?->getLabel()),
            ExportColumn::make('nationality')
                ->label('Nacionalidad'),
            ExportColumn::make('truck_type')
                ->label('Tipo de Tracto'),
            ExportColumn::make('tare')
                ->label('Tara (kg)'),
            ExportColumn::make('created_at')
                ->label('Fecha de Creación')
                ->state(fn ($record) => $record->created_at?->format('d/m/Y H:i')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de tractos se completó con ' . Number::format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
