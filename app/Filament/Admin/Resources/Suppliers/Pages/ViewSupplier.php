<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Suppliers\Pages;

use App\Enums\CompanyStatusEnum;
use App\Enums\CompanyTypeEnum;
use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Filament\Admin\Resources\Suppliers\SupplierResource;
use App\Mail\SupplierApprovedMail;
use App\Mail\SupplierRejectedMail;
use App\Models\Supplier;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class ViewSupplier extends Page
{
    public Supplier $record;

    public array $documentStatuses = [];

    public array $rejectionReasons = [];

    public ?int $selectedDocumentId = null;

    protected static string $resource = SupplierResource::class;

    protected string $view = 'filament.resources.supplier-resource.pages.view-supplier';

    public function mount(int|string $record): void
    {
        $this->record = Supplier::with(['documents', 'documents.validator'])->findOrFail(json_decode($record)->id);

        foreach ($this->record->documents as $document) {
            $this->documentStatuses[$document->id] = $document->status->value;
            $this->rejectionReasons[$document->id] = $document->rejection_reason ?? '';
        }
    }

    public function getTitle(): string
    {
        return "Validar Documentos - {$this->record->business_name}";
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
            if ($status !== DocumentStatusEnum::APPROVED->value) {
                return false;
            }
        }

        return true;
    }

    public function getRequiredDocumentTypes(): array
    {
        $baseDocuments = [
            DocumentTypeEnum::RUC_RECORD,
            DocumentTypeEnum::REPRESENTATIVE_DNI,
        ];

        if ($this->record->type === CompanyTypeEnum::JURIDICA) {
            $baseDocuments[] = DocumentTypeEnum::SUNARP_RECORD;
            $baseDocuments[] = DocumentTypeEnum::POWER_OF_ATTORNEY_VALIDITY;
        }

        return $baseDocuments;
    }

    public function saveValidation(): void
    {
        try {
            DB::beginTransaction();

            $allApproved = true;
            $hasChanges = false;

            foreach ($this->record->documents as $document) {
                $newStatus = DocumentStatusEnum::from((int) $this->documentStatuses[$document->id]);

                if ($document->status !== $newStatus) {
                    $hasChanges = true;

                    $document->update([
                        'status' => $newStatus,
                        'rejection_reason' => $newStatus === DocumentStatusEnum::REJECTED
                            ? $this->rejectionReasons[$document->id]
                            : null,
                        'validated_by' => Auth::id(),
                        'validated_date' => now(),
                    ]);
                }

                if ($newStatus !== DocumentStatusEnum::APPROVED) {
                    $allApproved = false;
                }
            }

            if ($allApproved && $this->canApproveAll()) {
                $this->record->update(['status' => CompanyStatusEnum::APROBADO, 'is_active' => true]);

                // TODO: Create SupplierApprovedMail class
                // if ($this->record->representativeUser?->email) {
                //     Mail::to($this->record->representativeUser->email)
                //         ->queue(new SupplierApprovedMail($this->record));
                // }

                Notification::make()
                    ->title('Proveedor Aprobado')
                    ->success()
                    ->body('Todos los documentos han sido aprobados y el proveedor ha sido validado.')
                    ->send();
            } else {
                $appealToken = Str::random(64);
                $expiresAt = now()->addDays(30);

                $this->record->update([
                    'status' => CompanyStatusEnum::RECHAZADO,
                    'appeal_token' => $appealToken,
                    'appeal_token_expires_at' => $expiresAt,
                ]);

                $rejectedDocuments = [];
                foreach ($this->record->documents as $document) {
                    if ($this->documentStatuses[$document->id] === DocumentStatusEnum::REJECTED->value) {
                        $rejectedDocuments[] = [
                            'type' => $document->type->getLabel(),
                            'reason' => $this->rejectionReasons[$document->id] ?? 'No especificado',
                        ];
                    }
                }

                // TODO: Create SupplierRejectedMail class
                // if ($this->record->representativeUser?->email && ! empty($rejectedDocuments)) {
                //     $appealUrl = route('supplier.appeal.show', $appealToken);
                //     Mail::to($this->record->representativeUser->email)
                //         ->queue(new SupplierRejectedMail($this->record, $rejectedDocuments, $appealUrl));
                // }

                Notification::make()
                    ->title('Documentos Validados')
                    ->warning()
                    ->body('Los documentos han sido validados. El proveedor requiere correcciones.')
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
            Action::make('back')
                ->label('Volver')
                ->url(self::getResource()::getUrl('index'))
                ->color('gray'),
            Action::make('edit')
                ->label('Editar Proveedor')
                ->url($this->record->id.'/edit')
                ->color('warning'),
        ];
    }
}
