<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MachineryRequirements\Schemas;

use App\Enums\MachineryOperationTypeEnum;
use App\Enums\MachineryUnitTypeEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class MachineryRequirementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('cost_center')
                    ->label('Centro de Costo')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: zzzzceco7777zzzz')
                    ->disabled(fn (?Model $record) => $record !== null && ! $record->canBeEdited()),

                TextInput::make('vessel_name')
                    ->label('Nombre de la Nave')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: MSC Elma')
                    ->disabled(fn (?Model $record) => $record !== null && ! $record->canBeEdited()),

                Select::make('operation_type')
                    ->label('Tipo de Operación')
                    ->options([
                        MachineryOperationTypeEnum::IRON_STONE->value => MachineryOperationTypeEnum::IRON_STONE->getLabel(),
                        MachineryOperationTypeEnum::CORN->value => MachineryOperationTypeEnum::CORN->getLabel(),
                        MachineryOperationTypeEnum::CLINKER->value => MachineryOperationTypeEnum::CLINKER->getLabel(),
                        MachineryOperationTypeEnum::COAL->value => MachineryOperationTypeEnum::COAL->getLabel(),
                        MachineryOperationTypeEnum::COPPER->value => MachineryOperationTypeEnum::COPPER->getLabel(),
                        MachineryOperationTypeEnum::ZINC->value => MachineryOperationTypeEnum::ZINC->getLabel(),
                        MachineryOperationTypeEnum::LEAD->value => MachineryOperationTypeEnum::LEAD->getLabel(),
                        MachineryOperationTypeEnum::ALUMINUM->value => MachineryOperationTypeEnum::ALUMINUM->getLabel(),
                        MachineryOperationTypeEnum::GRAINS->value => MachineryOperationTypeEnum::GRAINS->getLabel(),
                        MachineryOperationTypeEnum::FERTILIZERS->value => MachineryOperationTypeEnum::FERTILIZERS->getLabel(),
                    ])
                    ->required()
                    ->disabled(fn (?Model $record) => $record !== null && ! $record->canBeEdited()),

                Select::make('unit_type')
                    ->label('Tipo de Unidades')
                    ->options([
                        MachineryUnitTypeEnum::UNITS_WITH_HOPPER->value => MachineryUnitTypeEnum::UNITS_WITH_HOPPER->getLabel(),
                        MachineryUnitTypeEnum::PLATFORM->value => MachineryUnitTypeEnum::PLATFORM->getLabel(),
                        MachineryUnitTypeEnum::HEAVY_MACHINERY->value => MachineryUnitTypeEnum::HEAVY_MACHINERY->getLabel(),
                        MachineryUnitTypeEnum::CRANE->value => MachineryUnitTypeEnum::CRANE->getLabel(),
                        MachineryUnitTypeEnum::FORKLIFT->value => MachineryUnitTypeEnum::FORKLIFT->getLabel(),
                        MachineryUnitTypeEnum::BULLDOZER->value => MachineryUnitTypeEnum::BULLDOZER->getLabel(),
                        MachineryUnitTypeEnum::EXCAVATOR->value => MachineryUnitTypeEnum::EXCAVATOR->getLabel(),
                    ])
                    ->required()
                    ->disabled(fn (?Model $record) => $record !== null && ! $record->canBeEdited()),

                TextInput::make('units_quantity')
                    ->label('Cantidad de Unidades')
                    ->type('number')
                    ->required()
                    ->minValue(1)
                    ->disabled(fn (?Model $record) => $record !== null && ($record->announcement_launched_at !== null || ! $record->canBeEdited()))
                    ->helperText(fn (?Model $record) => $record !== null && $record->announcement_launched_at !== null
                        ? 'No se puede editar después de lanzar la convocatoria'
                        : ($record !== null && ! $record->canBeEdited()
                            ? 'No se puede editar un requerimiento aprobado o rechazado'
                            : '')),

                DateTimePicker::make('activation_time')
                    ->label('Horario de Activación')
                    ->required()
                    ->disabled(fn (?Model $record) => $record !== null && ($record->isActivationTimeLocked() || ! $record->canBeEdited()))
                    ->native(false)
                    ->seconds(true)
                    ->helperText(fn (?Model $record) => $record === null
                        ? 'Editable antes de la hora ingresada'
                        : (! $record->canBeEdited()
                            ? 'No se puede editar un requerimiento aprobado o rechazado'
                            : 'Editable antes de la hora ingresada')),

                TextInput::make('days_quantity')
                    ->label('Cantidad de Jornadas')
                    ->type('number')
                    ->required()
                    ->minValue(1)
                    ->disabled(fn (?Model $record) => $record !== null && ! $record->canBeEdited())
                    ->visible(fn () => Auth::user()?->can('View:MachineryRequirement')),
            ]);
    }
}
