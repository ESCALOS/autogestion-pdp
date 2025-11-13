<?php

declare(strict_types=1);

namespace App\Livewire\Trucks;

use App\Models\Truck;
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

final class ListTrucks extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions, InteractsWithSchemas, InteractsWithTable;

    protected $listeners = ['truck-created' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(Truck::query()->where('company_id', Auth::user()->company_id))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('license_plate')
                    ->label('Placa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('truck_type')
                    ->label('Tipo')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nationality')
                    ->label('Nacionalidad')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tare')
                    ->label('Tara (Ton)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('is_internal')
                    ->label('Interno')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->sortable(),
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
                SelectFilter::make('truck_type')
                    ->label('Tipo')
                    ->options(function () {
                        return Truck::query()
                            ->where('company_id', Auth::user()->company_id)
                            ->distinct()
                            ->pluck('truck_type', 'truck_type')
                            ->toArray();
                    }),
                SelectFilter::make('nationality')
                    ->label('Nacionalidad')
                    ->options(function () {
                        return Truck::query()
                            ->where('company_id', Auth::user()->company_id)
                            ->distinct()
                            ->pluck('nationality', 'nationality')
                            ->toArray();
                    }),
                TernaryFilter::make('is_internal')
                    ->label('Interno')
                    ->placeholder('Todos')
                    ->trueLabel('Solo Internos')
                    ->falseLabel('Solo Externos'),
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
        return view('livewire.trucks.list-trucks');
    }
}
