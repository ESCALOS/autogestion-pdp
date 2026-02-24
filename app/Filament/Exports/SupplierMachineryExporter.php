<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Enums\DocumentTypeEnum;
use App\Models\SupplierMachinery;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

final class SupplierMachineryExporter extends Exporter
{
    protected static ?string $model = SupplierMachinery::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('supplier.business_name')
                ->label('Proveedor'),
            ExportColumn::make('license_plate')
                ->label('Placa'),
            ExportColumn::make('nationality')
                ->label('Procedencia'),
            ExportColumn::make('is_internal')
                ->label('Interno')
                ->state(fn ($record) => $record->is_internal ? 'Sí' : 'No'),
            ExportColumn::make('truck_type')
                ->label('Tipo de Vehículo')
                ->state(fn ($record) => $record->truck_type?->getLabel()),
            ExportColumn::make('has_bonus')
                ->label('Tiene Bonificación')
                ->state(fn ($record) => $record->has_bonus ? 'Sí' : 'No'),
            ExportColumn::make('status')
                ->label('Estado')
                ->state(fn ($record) => $record->status?->getLabel()),
            ExportColumn::make('tare')
                ->label('Tara del Vehículo'),
            ExportColumn::make('created_at')
                ->label('Fecha de Creación')
                ->state(fn ($record) => $record->created_at?->format('d/m/Y H:i')),
            ExportColumn::make('updated_at')
                ->label('Fecha de Actualización')
                ->state(fn ($record) => $record->updated_at?->format('d/m/Y H:i')),

            // Machinery Documents
            ExportColumn::make('soat')
                ->label('Vencimiento SOAT')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::SOAT)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('poliza_seguro')
                ->label('Vencimiento de Póliza')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::POLIZA_SEGURO)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('bonificacion')
                ->label('Vencimiento de Resolución de Bonificación')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::BONIFICACION)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('habilitacion_mtc')
                ->label('Vencimiento de Habilitación Vehicular')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::HABILITACION_MTC)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('revision_tecnica')
                ->label('Vencimiento de Revisión Técnica')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::REVISION_TECNICA)->first()?->expiration_date?->format('d/m/Y')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de maquinaria se completó con '.Number::format($export->successful_rows).' '.str('fila')->plural($export->successful_rows).' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('fila')->plural($failedRowsCount).' fallaron.';
        }

        return $body;
    }
}
