<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Chassis\Tables;

use App\Enums\EntityStatusEnum;
use App\Filament\Exports\ChassisExporter;
use App\Models\Chassis;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class ChassisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.business_name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('license_plate')
                    ->searchable()
                    ->label('Placa'),
                TextColumn::make('status')
                    ->searchable()
                    ->label('Estado'),
                TextColumn::make('vehicle_type')
                    ->badge()
                    ->searchable()
                    ->label('Tipo de Vehículo'),
                TextColumn::make('axle_count')
                    ->numeric()
                    ->sortable()
                    ->label('Número de Ejes')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('has_bonus')
                    ->label('Tiene Bonif.?')
                    ->boolean(),
                TextColumn::make('tare')
                    ->numeric()
                    ->label('Tara')
                    ->sortable(),
                TextColumn::make('safe_weight')
                    ->numeric()
                    ->sortable()
                    ->label('Peso Seguro')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('height')
                    ->numeric()
                    ->sortable()
                    ->label('Altura')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('length')
                    ->numeric()
                    ->sortable()
                    ->label('Longitud')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('width')
                    ->numeric()
                    ->sortable()
                    ->label('Anchura')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_insulated')
                    ->boolean()
                    ->label('Aislado?')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('material')
                    ->searchable()
                    ->label('Material')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('accepts_20ft')
                    ->boolean()
                    ->label('Acepta 20ft?')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('accepts_40ft')
                    ->boolean()
                    ->label('Acepta 40ft?')
                    ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('appeal_token_expires_at')
                //     ->dateTime()
                //     ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Creado')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Actualizado'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado de Chassis')
                    ->options(EntityStatusEnum::class),
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
                    ->visible(fn (Chassis $record): bool => $record->status !== EntityStatusEnum::ACTIVE),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(ChassisExporter::class),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(ChassisExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->poll('60s');
    }
}
