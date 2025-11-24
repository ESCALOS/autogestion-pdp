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
                    ->sortable()
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        Select::make('date_type')
                            ->label('Tipo de Fecha')
                            ->options([
                                'created_at' => 'Fecha de Creación',
                                'updated_at' => 'Fecha de Actualización',
                            ])
                            ->default('created_at')
                            ->required(),
                        DatePicker::make('date_from')
                            ->label('Desde'),
                        DatePicker::make('date_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $dateType = $data['date_type'] ?? 'created_at';

                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate($dateType, '>=', $date),
                            )
                            ->when(
                                $data['date_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate($dateType, '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        $dateType = $data['date_type'] ?? 'created_at';
                        $dateLabel = $dateType === 'created_at' ? 'Creación' : 'Actualización';

                        if ($data['date_from'] ?? null) {
                            $indicators[] = $dateLabel.' desde '.\Carbon\Carbon::parse($data['date_from'])->format('d/m/Y');
                        }

                        if ($data['date_until'] ?? null) {
                            $indicators[] = $dateLabel.' hasta '.\Carbon\Carbon::parse($data['date_until'])->format('d/m/Y');
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
                    ->exporter(TruckExporter::class),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(TruckExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->poll('60s');
    }
}
