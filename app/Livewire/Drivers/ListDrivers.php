<?php

declare(strict_types=1);

namespace App\Livewire\Drivers;

use App\Models\Driver;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class ListDrivers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions, InteractsWithSchemas, InteractsWithTable;

    protected $listeners = ['driver-created' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(Driver::query()->where('company_id', Auth::user()->company_id))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('document_number')
                    ->label('Número de Documento')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('license_number')
                    ->label('Número de Licencia')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado En')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(\App\Enums\EntityStatusEnum::class),
                SelectFilter::make('document_type')
                    ->label('Tipo de Documento')
                    ->options(\App\Enums\DriverDocumentTypeEnum::class),
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
        return view('livewire.drivers.list-drivers');
    }
}
