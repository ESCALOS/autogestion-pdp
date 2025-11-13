<?php

declare(strict_types=1);

namespace App\Livewire\Chassis;

use App\Models\Chassis;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class ListChassis extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions, InteractsWithSchemas, InteractsWithTable;

    protected $listeners = ['chassis-created' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(Chassis::query()->where('company_id', Auth::user()->company_id))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('license_plate')
                    ->label('Placa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('vehicle_type')
                    ->label('Tipo de Vehículo')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('axle_count')
                    ->label('Ejes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tare')
                    ->label('Tara (Ton)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('safe_weight')
                    ->label('Peso Seguro (Ton)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('length')
                    ->label('Largo (m)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('width')
                    ->label('Ancho (m)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('height')
                    ->label('Alto (m)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_insulated')
                    ->label('Aislado')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('material')
                    ->label('Material')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('accepts_20ft')
                    ->label('20ft')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('accepts_40ft')
                    ->label('40ft')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('has_bonus')
                    ->label('Bonificación')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado En')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(\App\Enums\EntityStatusEnum::class),
                SelectFilter::make('vehicle_type')
                    ->label('Tipo de Vehículo')
                    ->options(function () {
                        return Chassis::query()
                            ->where('company_id', Auth::user()->company_id)
                            ->distinct()
                            ->pluck('vehicle_type', 'vehicle_type')
                            ->toArray();
                    }),
                TernaryFilter::make('is_insulated')
                    ->label('Aislado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo Aislados')
                    ->falseLabel('Solo No Aislados'),
                TernaryFilter::make('accepts_20ft')
                    ->label('Acepta 20ft')
                    ->placeholder('Todos')
                    ->trueLabel('Acepta 20ft')
                    ->falseLabel('No Acepta 20ft'),
                TernaryFilter::make('accepts_40ft')
                    ->label('Acepta 40ft')
                    ->placeholder('Todos')
                    ->trueLabel('Acepta 40ft')
                    ->falseLabel('No Acepta 40ft'),
                TernaryFilter::make('has_bonus')
                    ->label('Bonificación')
                    ->placeholder('Todos')
                    ->trueLabel('Con Bonificación')
                    ->falseLabel('Sin Bonificación'),
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                //
            ])
            ->poll('5s');
    }

    public function render(): View
    {
        return view('livewire.chassis.list-chassis');
    }
}
