<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SupplierMachineries\Tables;

use App\Enums\EntityStatusEnum;
use App\Filament\Exports\SupplierMachineryExporter;
use App\Models\SupplierMachinery;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class SupplierMachineriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.business_name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('license_plate')
                    ->label('Placa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('truck_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->getLabel() ?? '-'),
                TextColumn::make('documents_count')
                    ->label('Documentos')
                    ->counts('documents')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->schema([
                        Select::make('date_type')
                            ->label('Tipo de Fecha')
                            ->options([
                                'created_at' => 'Fecha de Creación',
                                'updated_at' => 'Fecha de Actualización',
                            ])
                            ->default('created_at')
                            ->required()
                            ->columnSpanFull(),
                        DatePicker::make('date_from')
                            ->label('Fecha Desde')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->displayFormat('d/m/Y'),
                        Select::make('time_from')
                            ->label('Hora Desde')
                            ->options(function () {
                                $hours = [];
                                for ($i = 0; $i < 24; $i++) {
                                    for ($j = 0; $j < 60; $j += 15) {
                                        $time = sprintf('%02d:%02d', $i, $j);
                                        $hours[$time] = $time;
                                    }
                                }

                                return $hours;
                            })
                            ->searchable()
                            ->placeholder(' '),
                        DatePicker::make('date_until')
                            ->label('Fecha Hasta')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->displayFormat('d/m/Y'),
                        Select::make('time_until')
                            ->label('Hora Hasta')
                            ->options(function () {
                                $hours = [];
                                for ($i = 0; $i < 24; $i++) {
                                    for ($j = 0; $j < 60; $j += 15) {
                                        $time = sprintf('%02d:%02d', $i, $j);
                                        $hours[$time] = $time;
                                    }
                                }

                                return $hours;
                            })
                            ->searchable()
                            ->placeholder(' '),
                    ])
                    ->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        $dateType = $data['date_type'] ?? 'created_at';

                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                function (Builder $query, $date) use ($data, $dateType): Builder {
                                    $datetime = \Carbon\Carbon::parse($date);
                                    if (! empty($data['time_from'])) {
                                        [$hour, $minute] = explode(':', $data['time_from']);
                                        $datetime->setTime((int) $hour, (int) $minute, 0);
                                    } else {
                                        $datetime->startOfDay();
                                    }

                                    return $query->where($dateType, '>=', $datetime);
                                },
                            )
                            ->when(
                                $data['date_until'] ?? null,
                                function (Builder $query, $date) use ($data, $dateType): Builder {
                                    $datetime = \Carbon\Carbon::parse($date);
                                    if (! empty($data['time_until'])) {
                                        [$hour, $minute] = explode(':', $data['time_until']);
                                        $datetime->setTime((int) $hour, (int) $minute, 59);
                                    } else {
                                        $datetime->endOfDay();
                                    }

                                    return $query->where($dateType, '<=', $datetime);
                                },
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        $dateType = $data['date_type'] ?? 'created_at';
                        $dateLabel = $dateType === 'created_at' ? 'Creación' : 'Actualización';

                        if ($data['date_from'] ?? null) {
                            $dateStr = \Carbon\Carbon::parse($data['date_from'])->format('d/m/Y');
                            if (! empty($data['time_from'])) {
                                $dateStr .= ' '.$data['time_from'];
                            }
                            $indicators[] = $dateLabel.' desde '.$dateStr;
                        }

                        if ($data['date_until'] ?? null) {
                            $dateStr = \Carbon\Carbon::parse($data['date_until'])->format('d/m/Y');
                            if (! empty($data['time_until'])) {
                                $dateStr .= ' '.$data['time_until'];
                            }
                            $indicators[] = $dateLabel.' hasta '.$dateStr;
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Validar')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->visible(fn (SupplierMachinery $record): bool => $record->status !== EntityStatusEnum::ACTIVE),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(SupplierMachineryExporter::class)
                    ->columnMapping(false)
                    ->modal(false),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(SupplierMachineryExporter::class)
                        ->columnMapping(false)
                        ->modal(false),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->poll('60s');
    }
}
