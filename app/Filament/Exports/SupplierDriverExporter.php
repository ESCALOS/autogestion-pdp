<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Enums\DocumentTypeEnum;
use App\Models\SupplierDriver;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

final class SupplierDriverExporter extends Exporter
{
    protected static ?string $model = SupplierDriver::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('supplier.business_name')
                ->label('Proveedor'),
            ExportColumn::make('full_name')
                ->label('Nombre del Conductor')
                ->state(fn ($record) => trim($record->name.' '.$record->lastname)),
            ExportColumn::make('status')
                ->label('Estado del Conductor')
                ->state(fn ($record) => $record->status->getLabel()),
            ExportColumn::make('document_number')
                ->label('Número de Documento'),
            ExportColumn::make('license_number')
                ->label('Número de Licencia'),
            ExportColumn::make('licencia_vencimiento')
                ->label('Vencimiento de Licencia')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::LICENCIA_DE_CONDUCIR)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('document_type')
                ->label('Tipo de Documento')
                ->state(fn ($record) => $record->document_type?->getLabel()),
            ExportColumn::make('created_at')
                ->label('Fecha de Creación')
                ->state(fn ($record) => $record->created_at?->format('d/m/Y H:i')),
            ExportColumn::make('updated_at')
                ->label('Fecha de Actualización')
                ->state(fn ($record) => $record->updated_at?->format('d/m/Y H:i')),

            // Driver Documents
            ExportColumn::make('mercancias')
                ->label('Certificado de Mercancías Peligrosas')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::CURSO_MERCANCIAS)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('seguridad_portuaria')
                ->label('Certificado de Seguridad Portuaria')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::CURSO_SEGURIDAD_PORTUARIA)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('induccion_seguridad')
                ->label('Inducción de Seguridad y Medio Ambiente')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::INDUCCION_SEGURIDAD)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('pbip')
                ->label('Certificado PBIP')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::CURSO_PBIP)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('declaracion_jurada')
                ->label('Declaración de No Antecedentes')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::DECLARACION_JURADA)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('sctr')
                ->label('Vencimiento de SCTR')
                ->state(fn ($record) => $record->documents->where('type', DocumentTypeEnum::SCTR)->first()?->expiration_date?->format('d/m/Y')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de conductores se completó con '.Number::format($export->successful_rows).' '.str('fila')->plural($export->successful_rows).' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('fila')->plural($failedRowsCount).' fallaron.';
        }

        return $body;
    }
}
