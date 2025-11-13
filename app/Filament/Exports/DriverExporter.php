<?php

namespace App\Filament\Exports;

use App\Models\Driver;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class DriverExporter extends Exporter
{
    protected static ?string $model = Driver::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('company.business_name')
                ->label('Empresa'),
            ExportColumn::make('document_type')
                ->label('Tipo de Documento')
                ->state(fn ($record) => $record->document_type?->getLabel()),
            ExportColumn::make('document_number')
                ->label('Número de Documento'),
            ExportColumn::make('name')
                ->label('Nombre'),
            ExportColumn::make('lastname')
                ->label('Apellido'),
            ExportColumn::make('license_number')
                ->label('Licencia'),
            ExportColumn::make('status')
                ->label('Estado')
                ->state(fn ($record) => $record->status?->getLabel()),
            ExportColumn::make('created_at')
                ->label('Fecha de Creación')
                ->state(fn ($record) => $record->created_at?->format('d/m/Y H:i')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de conductores se completó con ' . Number::format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
