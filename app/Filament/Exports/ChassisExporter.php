<?php

namespace App\Filament\Exports;

use App\Models\Chassis;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ChassisExporter extends Exporter
{
    protected static ?string $model = Chassis::class;

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
            ExportColumn::make('vehicle_type')
                ->label('Tipo de Vehículo'),
            ExportColumn::make('axle_count')
                ->label('Número de Ejes'),
            ExportColumn::make('tare')
                ->label('Tara'),
            ExportColumn::make('material')
                ->label('Material'),
            ExportColumn::make('accepts_20ft')
                ->label('Acepta 20ft')
                ->state(fn ($record) => $record->accepts_20ft ? 'Sí' : 'No'),
            ExportColumn::make('accepts_40ft')
                ->label('Acepta 40ft')
                ->state(fn ($record) => $record->accepts_40ft ? 'Sí' : 'No'),
            ExportColumn::make('created_at')
                ->label('Fecha de Creación')
                ->state(fn ($record) => $record->created_at?->format('d/m/Y H:i')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de carretas se completó con ' . Number::format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
