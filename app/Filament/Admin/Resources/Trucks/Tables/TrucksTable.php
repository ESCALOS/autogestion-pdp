<?php

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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrucksTable
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Actualizado')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            ->poll('5s');
    }
}
