<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MachineryRequirements\Tables;

use App\Enums\MachineryOperationTypeEnum;
use App\Enums\MachineryUnitTypeEnum;
use App\Filament\Admin\Resources\MachineryRequirements\Actions\ApproveAction;
use App\Filament\Admin\Resources\MachineryRequirements\Actions\RejectAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class MachineryRequirementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cost_center')
                    ->label('Centro de Costo')
                    ->searchable(),

                TextColumn::make('vessel_name')
                    ->label('Nave')
                    ->searchable(),

                TextColumn::make('operation_type')
                    ->label('Tipo Operación')
                    ->formatStateUsing(fn (MachineryOperationTypeEnum $state) => $state->getLabel())
                    ->badge(),

                TextColumn::make('unit_type')
                    ->label('Tipo Unidades')
                    ->formatStateUsing(fn (MachineryUnitTypeEnum $state) => $state->getLabel())
                    ->badge(),

                TextColumn::make('units_quantity')
                    ->label('Cantidad Unidades')
                    ->numeric(),

                TextColumn::make('activation_time')
                    ->label('Activación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('days_quantity')
                    ->label('Jornadas')
                    ->numeric()
                    ->visible(fn () => Auth::user()?->can('View:MachineryRequirement')),

                BadgeColumn::make('approval_status')
                    ->label('Estado')
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'pending' => 'Pendiente',
                            'approved' => 'Aprobado',
                            'rejected' => 'Rechazado',
                            default => $state,
                        };
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('user.full_name')
                    ->label('Creado por')
                    ->visible(fn () => Auth::user()?->can('View:MachineryRequirement')),

                TextColumn::make('approver.full_name')
                    ->label('Aprobado por')
                    ->visible(fn () => Auth::user()?->can('View:MachineryRequirement')),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('operation_type')
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
                    ]),

                SelectFilter::make('unit_type')
                    ->label('Tipo de Unidades')
                    ->options([
                        MachineryUnitTypeEnum::UNITS_WITH_HOPPER->value => MachineryUnitTypeEnum::UNITS_WITH_HOPPER->getLabel(),
                        MachineryUnitTypeEnum::PLATFORM->value => MachineryUnitTypeEnum::PLATFORM->getLabel(),
                        MachineryUnitTypeEnum::HEAVY_MACHINERY->value => MachineryUnitTypeEnum::HEAVY_MACHINERY->getLabel(),
                        MachineryUnitTypeEnum::CRANE->value => MachineryUnitTypeEnum::CRANE->getLabel(),
                        MachineryUnitTypeEnum::FORKLIFT->value => MachineryUnitTypeEnum::FORKLIFT->getLabel(),
                        MachineryUnitTypeEnum::BULLDOZER->value => MachineryUnitTypeEnum::BULLDOZER->getLabel(),
                        MachineryUnitTypeEnum::EXCAVATOR->value => MachineryUnitTypeEnum::EXCAVATOR->getLabel(),
                    ]),
            ])
            ->deferFilters(false)
            ->actions([
                ApproveAction::make(),
                RejectAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query, Request $request) {
                $user = $request->user();

                // Si puede ver todos, mostrar todos
                if ($user->can('View:MachineryRequirement')) {
                    return $query;
                }

                // Otros solo ven los suyos
                return $query->where('user_id', $user->id);
            });
    }
}
