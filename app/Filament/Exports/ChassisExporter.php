<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Chassis;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

final class ChassisExporter extends Exporter
{
    protected static ?string $model = Chassis::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('company.business_name')
                ->label('Company'),
            ExportColumn::make('license_plate')
                ->label('Equipment Number'),
            ExportColumn::make('vehicle_type')
                ->label('Equipment Type')
                ->state(fn ($record) => $record->vehicle_type?->getLabel() ?? $record->vehicle_type),
            ExportColumn::make('iso_group')
                ->label('ISO Group')
                ->state(fn () => 'CH'),
            ExportColumn::make('cannot_be_sealed')
                ->label('Cannot Be Sealed')
                ->state(fn () => 'Yes'),                                
            ExportColumn::make('has_wheels')
                ->label('Has Wheels')
                ->state(fn () => 'Yes'),
            ExportColumn::make('axle_count')
                ->label('Axle Count'),
            ExportColumn::make('chassis_type')
                ->label('Tipo Chassis')
                ->state(fn ($record) => $record->vehicle_type?->getLabel() ?? $record->vehicle_type),                
            ExportColumn::make('has_bonus')
                ->label('Bonificación')
                ->state(fn ($record) => $record->has_bonus ? 'Yes' : 'No'),                
            ExportColumn::make('tare')
                ->label('Tare Weight (kg)'),
            ExportColumn::make('safe_weight')
                ->label('Safe Weight (kg)'),
            ExportColumn::make('length')
                ->label('Length (mm)'),
            ExportColumn::make('height')
                ->label('Height (mm)'),
            ExportColumn::make('width')
                ->label('Width (mm)'),                                                              
            ExportColumn::make('status')
                ->label('Estado')
                ->state(fn ($record) => $record->status?->getLabel() ?? $record->status),
            ExportColumn::make('is_insulated')
                ->label('Is Insulated')
                ->state(fn ($record) => $record->is_insulated ? 'Yes' : 'No'),
            ExportColumn::make('material')
                ->label('Material'),
            ExportColumn::make('accepts_20ft')
                ->label('Accepts 20ft')
                ->state(fn ($record) => $record->accepts_20ft ? 'Yes' : 'No'),
            ExportColumn::make('accepts_40ft')
                ->label('Accepts 40ft')
                ->state(fn ($record) => $record->accepts_40ft ? 'Yes' : 'No'),
            ExportColumn::make('created_at')
                ->label('Creation Date')
                ->state(fn ($record) => $record->created_at?->format('d/m/Y H:i')),

            // Chassis Documents
           ExportColumn::make('bonificacion')
                ->label('Vencimiento de Resolución de Bonificación')
                ->state(fn ($record) => $record->documents->where('type', \App\Enums\DocumentTypeEnum::CHASSIS_BONIFICACION)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('habilitacion_mtc')
                ->label('Vencimiento de Habilitación Vehicular')
                ->state(fn ($record) => $record->documents->where('type', \App\Enums\DocumentTypeEnum::CHASSIS_HABILITACION_MTC)->first()?->expiration_date?->format('d/m/Y')),
            ExportColumn::make('revision_tecnica')
                ->label('Vencimiento de Revisión Técnica')
                ->state(fn ($record) => $record->documents->where('type', \App\Enums\DocumentTypeEnum::CHASSIS_REVISION_TECNICA)->first()?->expiration_date?->format('d/m/Y')),            
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de carretas se completó con '.Number::format($export->successful_rows).' '.str('fila')->plural($export->successful_rows).' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('fila')->plural($failedRowsCount).' fallaron.';
        }

        return $body;
    }
}
