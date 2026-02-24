<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SupplierDrivers\Pages;

use App\Enums\EntityStatusEnum;
use App\Filament\Admin\Resources\SupplierDrivers\SupplierDriverResource;
use App\Mail\SupplierDriverApprovedMail;
use App\Mail\SupplierDriverRejectedMail;
use App\Models\SupplierDriver;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class ViewSupplierDriver extends Page
{
    public SupplierDriver $record;

    public array $documentStatuses = [];

    public array $rejectionReasons = [];

    public ?int $selectedDocumentId = null;

    protected static string $resource = SupplierDriverResource::class;

    protected string $view = 'filament.resources.supplier-driver-resource.pages.view-supplier-driver';

    public function mount(int|string $record): void
    {
        $this->record = SupplierDriver::with('documents', 'supplier.representative')->findOrFail(json_decode($record)->id);
        // Inicializar estados de documentos
        foreach ($this->record->documents as $document) {
            $this->documentStatuses[$document->id] = $document->status->value;
            $this->rejectionReasons[$document->id] = $document->rejection_reason ?? '';
        }
    }

    public function getTitle(): string
    {
        return "Validar Documentos - {$this->record->full_name}";
    }

    public function openDocument(int $documentId): void
    {
        $document = $this->record->documents->find($documentId);
        if ($document) {
            $this->selectedDocumentId = $document->id;
            $this->dispatch('open-document-modal');
        }
    }

    public function getSelectedDocument()
    {
        if ($this->selectedDocumentId) {
            return $this->record->documents->find($this->selectedDocumentId);
        }

        return null;
    }

    public function canApproveAll(): bool
    {
        foreach ($this->documentStatuses as $status) {
            if ($status !== 2) {
                return false;
            }
        }

        return true;
    }

    public function getRequiredDocumentTypes(): array
    {
        return $this->record->documents->pluck('type')->unique()->toArray();
    }

    public function saveValidation(): void
    {
        try {
            DB::beginTransaction();

            $allApproved = true;
            $hasRejected = false;
            $hasChanges = false;

            foreach ($this->record->documents as $document) {
                $newStatus = $this->documentStatuses[$document->id];

                if ($document->status !== $newStatus) {
                    $hasChanges = true;

                    $document->update([
                        'status' => $newStatus,
                        'rejection_reason' => $newStatus === 3
                            ? $this->rejectionReasons[$document->id]
                            : null,
                        'validated_by' => Auth::id(),
                        'validated_date' => now(),
                    ]);
                }

                if ($newStatus === 3) {
                    $hasRejected = true;
                    $allApproved = false;
                } elseif ($newStatus !== 2) {
                    $allApproved = false;
                }
            }

            if ($hasRejected) {
                $appealToken = Str::random(64);
                $expiresAt = now()->addDays(30);

                $this->record->update([
                    'status' => EntityStatusEnum::NEEDS_UPDATE,
                    'appeal_token' => $appealToken,
                    'appeal_token_expires_at' => $expiresAt,
                ]);

                $rejectedDocuments = [];
                foreach ($this->record->documents as $document) {
                    if ($this->documentStatuses[$document->id] === 3) {
                        $rejectedDocuments[] = [
                            'type' => $document->type->getLabel(),
                            'reason' => $this->rejectionReasons[$document->id] ?? 'No especificado',
                        ];
                    }
                }

                $supplierContactEmail = $this->record->supplier?->representative?->email ?? null;

                // TODO: Create SupplierDriverRejectedMail class
                // if ($supplierContactEmail && ! empty($rejectedDocuments)) {
                //     try {
                //         $appealUrl = route('supplier-driver.appeal.show', $appealToken);
                //         Mail::to($supplierContactEmail)
                //             ->queue(new SupplierDriverRejectedMail($this->record, $rejectedDocuments, $appealUrl));

                //         Log::info('Correo de rechazo enviado a: '.$supplierContactEmail);
                //     } catch (Exception $e) {
                //         Log::error('Error al enviar correo de rechazo: '.$e->getMessage());

                //         Notification::make()
                //             ->title('Advertencia')
                //             ->warning()
                //             ->body('Los documentos fueron rechazados pero no se pudo enviar el correo.')
                //             ->send();
                //     }
                // }

                Notification::make()
                    ->title('Documentos Rechazados')
                    ->warning()
                    ->body('Los documentos han sido rechazados. El conductor requiere actualizar documentos.')
                    ->send();
            } elseif ($allApproved && $this->canApproveAll()) {
                $this->record->update(['status' => EntityStatusEnum::ACTIVE]);

                $supplierContactEmail = $this->record->supplier?->representative?->email ?? null;
                // if ($supplierContactEmail) {
                //     try {
                //         Mail::to($supplierContactEmail)
                //             ->queue(new SupplierDriverApprovedMail($this->record));

                //         Log::info('Correo de aprobación enviado a: '.$supplierContactEmail);
                //     } catch (Exception $e) {
                //         Log::error('Error al enviar correo de aprobación: '.$e->getMessage());
                //     }
                // }

                Notification::make()
                    ->title('Conductor Aprobado')
                    ->success()
                    ->body('Todos los documentos han sido aprobados y el conductor ha sido validado.')
                    ->send();
            } else {
                $this->record->update(['status' => EntityStatusEnum::PENDING_APPROVAL]);

                Notification::make()
                    ->title('Documentos en Revisión')
                    ->info()
                    ->body('Los documentos están en proceso de validación.')
                    ->send();
            }

            DB::commit();

            if (! $hasChanges) {
                Notification::make()
                    ->title('Sin cambios')
                    ->info()
                    ->body('No se han realizado cambios en la validación.')
                    ->send();

                return;
            }

            $this->redirect(self::getResource()::getUrl('index'));

        } catch (Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Ocurrió un error al guardar la validación: '.$e->getMessage())
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Editar Conductor')
                ->url(self::getResource()::getUrl('edit', ['record' => $this->record]))
                ->color('warning'),
        ];
    }
}
