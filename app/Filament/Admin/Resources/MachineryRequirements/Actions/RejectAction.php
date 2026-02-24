<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MachineryRequirements\Actions;

use App\Models\MachineryRequirement;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

final class RejectAction extends Action
{
    protected function setUp(): void
    {
        $this
            ->label('Rechazar')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->form([
                Textarea::make('rejection_reason')
                    ->label('Motivo del rechazo')
                    ->placeholder('Explique por qué se rechaza este requerimiento...')
                    ->required()
                    ->rows(4),
            ])
            ->modalHeading('Rechazar Requerimiento')
            ->modalDescription('Complete el formulario para rechazar este requerimiento.')
            ->modalSubmitActionLabel('Rechazar')
            ->action(function (MachineryRequirement $record, array $data): void {
                if (! Auth::user()->can('Delete:MachineryRequirement')) {
                    Notification::make()
                        ->title('No autorizado')
                        ->body('No tiene permiso para rechazar requerimientos.')
                        ->danger()
                        ->send();

                    return;
                }

                if (! $record->isPending()) {
                    Notification::make()
                        ->title('Error')
                        ->body('No se puede rechazar un requerimiento que no está pendiente.')
                        ->danger()
                        ->send();

                    return;
                }

                $record->update([
                    'approval_status' => 'rejected',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'rejection_reason' => $data['rejection_reason'],
                ]);

                Notification::make()
                    ->title('Requerimiento rechazado')
                    ->body('El requerimiento ha sido rechazado.')
                    ->success()
                    ->send();
            })
            ->visible(function (MachineryRequirement $record): bool {
                return $record->isPending() && Auth::user()->can('Delete:MachineryRequirement');
            });
    }

    public static function getDefaultName(): string
    {
        return 'reject';
    }
}
