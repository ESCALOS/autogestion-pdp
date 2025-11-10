<?php

namespace App\Filament\Admin\Resources\Chassis\Tables;

use App\Enums\ChassisStatusEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ChassisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.bussines_name')
                    ->numeric()
                    ->label('Empresa')
                    ->sortable(),
                TextColumn::make('license_plate')
                    ->searchable()
                    ->label('Placa'),
                TextColumn::make('status')
                    ->searchable()
                    ->label('Estado'),
                TextColumn::make('vehicle_type')
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
                    ->label('Acepta 20ft?'),
                IconColumn::make('accepts_40ft')
                    ->boolean()
                    ->label('Acepta 40ft?'),
                // TextColumn::make('appeal_token_expires_at')
                //     ->dateTime()
                //     ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Creado')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Actualizado')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado de Chassis')
                    ->options(ChassisStatusEnum::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
