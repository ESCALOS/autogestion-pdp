<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trucks\Tables;

use App\Enums\EntityStatusEnum;
use App\Filament\Exports\TruckExporter;
use App\Models\Truck;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class TrucksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.business_name')
                    ->label('Empresa')
                    ->sortable(),
                TextColumn::make('license_plate')
                    ->label('Placa')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->label('Estado')
                    ->sortable(),
                TextColumn::make('nationality')
                    ->label('Nacionalidad')
                    ->searchable(),
                IconColumn::make('is_internal')
                    ->label('Interno')
                    ->boolean(),
                TextColumn::make('truck_type')
                    ->label('Tipo de Camión')
                    ->badge()
                    ->searchable(),
                IconColumn::make('has_bonus')
                    ->label('Bono')
                    ->boolean(),
                TextColumn::make('tare')
                    ->label('Tara (kg)')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Creado')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Actualizado')
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
                    ->visible(fn (Truck $record): bool => $record->status !== EntityStatusEnum::ACTIVE),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(TruckExporter::class)
                    ->columnMapping(false)
                    ->modal(false),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(TruckExporter::class)
                        ->columnMapping(false)
                        ->modal(false),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->poll('60s');
    }
}
