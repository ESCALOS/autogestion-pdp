<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Company;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

final class CompanyExporter extends Exporter
{
    protected static ?string $model = Company::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('ruc')
                ->label('ID'),
            ExportColumn::make('business_name')
                ->label('Name'),
            ExportColumn::make('type')
                ->label('Type')
                ->state(fn ($record) => $record->type?->getLabel()),
            ExportColumn::make('status')
                ->label('Status')
                ->state(fn ($record) => $record->status?->getLabel()),
            ExportColumn::make('is_active')
                ->label('Active')
                ->state(fn ($record) => $record->is_active ? 'Yes' : 'No'),
            ExportColumn::make('created_at')
                ->label('Creation Date')
                ->state(fn ($record) => $record->created_at?->format('d/m/Y H:i')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de empresas se completó con '.Number::format($export->successful_rows).' '.str('fila')->plural($export->successful_rows).' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('fila')->plural($failedRowsCount).' fallaron.';
        }

        return $body;
    }
}
