<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MachineryRequirements\Schemas;

use App\Enums\MachineryOperationTypeEnum;
use App\Enums\MachineryUnitTypeEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

final class MachineryRequirementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(['default' => 2])
                    ->schema([
                        TextEntry::make('cost_center')
                            ->label('Centro de Costo'),

                        TextEntry::make('vessel_name')
                            ->label('Nombre de la Nave'),

                        TextEntry::make('operation_type')
                            ->label('Tipo de Operación')
                            ->formatStateUsing(fn (MachineryOperationTypeEnum $state) => $state->getLabel())
                            ->badge(),

                        TextEntry::make('unit_type')
                            ->label('Tipo de Unidades')
                            ->formatStateUsing(fn (MachineryUnitTypeEnum $state) => $state->getLabel())
                            ->badge(),

                        TextEntry::make('units_quantity')
                            ->label('Cantidad de Unidades'),

                        TextEntry::make('activation_time')
                            ->label('Horario de Activación')
                            ->dateTime('d/m/Y H:i:s'),

                        TextEntry::make('days_quantity')
                            ->label('Cantidad de Jornadas')
                            ->visible(fn () => Auth::user()?->can('View:MachineryRequirement')),

                        TextEntry::make('announcement_launched_at')
                            ->label('Convocatoria Lanzada')
                            ->dateTime('d/m/Y H:i:s')
                            ->placeholder('No lanzada aún'),

                        TextEntry::make('user.full_name')
                            ->label('Creado por'),

                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->dateTime('d/m/Y H:i:s'),

                        TextEntry::make('updated_at')
                            ->label('Actualizado')
                            ->dateTime('d/m/Y H:i:s'),
                    ]),

                Section::make('Estado de Aprobación')
                    ->schema([
                        Grid::make(['default' => 2])
                            ->schema([
                                TextEntry::make('approval_status')
                                    ->label('Estado')
                                    ->formatStateUsing(function (string $state): string {
                                        return match ($state) {
                                            'pending' => 'Pendiente',
                                            'approved' => 'Aprobado',
                                            'rejected' => 'Rechazado',
                                            default => $state,
                                        };
                                    })
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    }),

                                TextEntry::make('approver.full_name')
                                    ->label('Aprobado por')
                                    ->placeholder('—')
                                    ->visible(fn ($record) => $record !== null && $record->approver_id !== null),

                                TextEntry::make('approved_at')
                                    ->label('Fecha de Aprobación')
                                    ->dateTime('d/m/Y H:i:s')
                                    ->placeholder('—')
                                    ->visible(fn ($record) => $record !== null && $record->approved_at !== null),

                                TextEntry::make('rejection_reason')
                                    ->label('Motivo del Rechazo')
                                    ->columnSpanFull()
                                    ->placeholder('—')
                                    ->visible(fn ($record) => $record !== null && $record->rejection_reason !== null),
                            ]),
                    ]),
            ]);
    }
}
