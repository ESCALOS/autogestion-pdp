<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Truck;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

final class TruckExporter extends Exporter
{
    protected static ?string $model = Truck::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('company.business_name')
                ->label('Company'),
            ExportColumn::make('license_plate')
                ->label('Truck ID'),
            ExportColumn::make('license_plate')
                ->label('Truck License'),
            ExportColumn::make('nationality')
                ->label('License State'),
            ExportColumn::make('is_internal')
                ->label('Internal')
                ->state(fn ($record) => $record->is_internal ? 'Yes' : 'No'),
            ExportColumn::make('truck_type')
                ->label('Tipo Camión')
                ->state(fn ($record) => $record->truck_type?->getLabel()),
            ExportColumn::make('has_bonus')
                ->label('Has Bonus')
                ->state(fn ($record) => $record->has_bonus ? 'Yes' : 'No'),
            ExportColumn::make('status')
                ->label('Status')
                ->state(fn ($record) => $record->status?->getLabel()),
            ExportColumn::make('tare')
                ->label('Tara de Truck'),
            ExportColumn::make('created_at')
                ->label('Fecha de Creación')
                ->state(fn ($record) => $record->created_at?->format('d/m/Y H:i')),

            // Truck Documents
            ExportColumn::make('soat')
                ->label('SOAT Expiration Date')
                ->state(fn ($record) => $record->documents->where('type', \App\Enums\DocumentTypeEnum::SOAT)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('poliza_seguro')
                ->label('Vencimiento de Póliza')
                ->state(fn ($record) => $record->documents->where('type', \App\Enums\DocumentTypeEnum::POLIZA_SEGURO)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('bonificacion')
                ->label('Vencimiento de Resolución de Bonificación')
                ->state(fn ($record) => $record->documents->where('type', \App\Enums\DocumentTypeEnum::BONIFICACION)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('habilitacion_mtc')
                ->label('Vencimiento de Habilitación Vehicular')
                ->state(fn ($record) => $record->documents->where('type', \App\Enums\DocumentTypeEnum::HABILITACION_MTC)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('revision_tecnica')
                ->label('Vencimiento de Revisión Técnica')
                ->state(fn ($record) => $record->documents->where('type', \App\Enums\DocumentTypeEnum::REVISION_TECNICA)->first()?->expiration_date?->format('d/m/Y')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de tractos se completó con '.Number::format($export->successful_rows).' '.str('fila')->plural($export->successful_rows).' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('fila')->plural($failedRowsCount).' fallaron.';
        }

        return $body;
    }
}
