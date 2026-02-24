<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MachineryRequirements\Actions;

use App\Models\MachineryRequirement;
use App\Services\NotifySupplierRequirementService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

final class ApproveAction extends Action
{
    protected function setUp(): void
    {
        $this
            ->label('Aprobar')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Aprobar Requerimiento')
            ->modalDescription('¿Confirma que desea aprobar este requerimiento de maquinaria?')
            ->modalSubmitActionLabel('Sí, aprobar')
            ->action(function (MachineryRequirement $record): void {
                if (! Auth::user()->can('Update:MachineryRequirement')) {
                    Notification::make()
                        ->title('No autorizado')
                        ->body('No tiene permiso para aprobar requerimientos.')
                        ->danger()
                        ->send();

                    return;
                }

                if (! $record->isPending()) {
                    Notification::make()
                        ->title('Error')
                        ->body('No se puede aprobar un requerimiento que no está pendiente.')
                        ->danger()
                        ->send();

                    return;
                }

                $record->update([
                    'approval_status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);

                // Notificar a proveedores con maquinaria disponible
                app(NotifySupplierRequirementService::class)->notifyProvidersForRequirement($record);

                Notification::make()
                    ->title('Requerimiento aprobado')
                    ->body('El requerimiento ha sido aprobado exitosamente y se han notificado a los proveedores.')
                    ->success()
                    ->send();
            })
            ->visible(function (MachineryRequirement $record): bool {
                return $record->isPending() && Auth::user()->can('Update:MachineryRequirement');
            });
    }

    public static function getDefaultName(): string
    {
        return 'approve';
    }
}
