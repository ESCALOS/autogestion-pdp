<?php

declare(strict_types=1);

namespace App\Livewire\Driver;

use App\Enums\DocumentStatusEnum;
use App\Enums\EntityStatusEnum;
use App\Models\Driver;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

#[Layout('components.layouts.appeal')]
final class AppealForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?string $token = null;

    public ?Driver $driver = null;

    public ?array $data = [];

    public bool $success = false;

    public function mount(string $token): void
    {
        $this->token = $token;

        $this->driver = Driver::with('company')
            ->where('appeal_token', $token)
            ->where('appeal_token_expires_at', '>', now())
            ->with(['documents', 'company'])
            ->first();

        if (! $this->driver) {
            abort(404, 'El enlace de apelación es inválido o ha expirado.');
        }

        $documentsToAppeal = $this->driver->documents()
            ->where(function ($query) {
                $query->where('status', DocumentStatusEnum::REJECTED)
                    ->orWhere('status', DocumentStatusEnum::NEEDS_UPDATE)
                    ->orWhere('status', DocumentStatusEnum::EXPIRING_SOON)
                    ->orWhere('expiration_date', '<', now());
            })
            ->get();

        if ($documentsToAppeal->isEmpty()) {
            abort(404, 'No hay documentos rechazados, por vencer o vencidos para apelar.');
        }

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $documentsToUpdate = $this->driver->documents()
            ->where(function ($query) {
                $query->where('status', DocumentStatusEnum::REJECTED)
                    ->orWhere('status', DocumentStatusEnum::NEEDS_UPDATE)
                    ->orWhere('status', DocumentStatusEnum::EXPIRING_SOON)
                    ->orWhere('expiration_date', '<', now());
            })
            ->get();

        $components = [];

        foreach ($documentsToUpdate as $document) {
            $isExpired = $document->expiration_date && $document->expiration_date < now();
            $isExpiringSoon = $document->status === DocumentStatusEnum::EXPIRING_SOON && ! $isExpired;
            $isRequired = ! $isExpiringSoon; // Solo es obligatorio si NO está próximo a vencer

            if ($isExpired) {
                $description = "Documento vencido el: {$document->expiration_date->format('d/m/Y')}";
            } elseif ($isExpiringSoon) {
                $description = "Documento por vencer el: {$document->expiration_date->format('d/m/Y')} (opcional actualizar ahora)";
            } else {
                $description = "Motivo de rechazo: {$document->rejection_reason}";
            }

            $formSchema = [
                FileUpload::make("document_{$document->id}")
                    ->label('Cargar nuevo documento')
                    ->required($isRequired)
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'])
                    ->maxSize(5120)
                    ->directory(fn () => "EMPRESAS/{$this->driver->company->ruc}/DRIVERS/{$this->driver->document_number}")
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) use ($document): string {
                        $extension = $file->getClientOriginalExtension();

                        return $document->type->getFileName().'.'.$extension;
                    })
                    ->helperText($isRequired ? 'Formatos permitidos: PDF, JPG, JPEG, PNG (máximo 5MB)' : 'Opcional. Formatos permitidos: PDF, JPG, JPEG, PNG (máximo 5MB)'),
            ];

            // Agregar campo de fecha según el tipo de documento
            if ($document->type->requiresCourseDate()) {
                // Para cursos: mostrar fecha del curso
                $validityYears = $document->type->getValidityYears();
                $formSchema[] = DatePicker::make("course_date_{$document->id}")
                    ->label('Fecha del Curso')
                    ->helperText($isRequired ? "Vigencia: {$validityYears} años. La fecha de vencimiento se calculará automáticamente." : "Requerido si sube un nuevo documento. Vigencia: {$validityYears} años.")
                    ->required(fn (callable $get): bool => ! empty($get("document_{$document->id}")))
                    ->native(false)
                    ->maxDate(now())
                    ->closeOnDateSelection()
                    ->displayFormat('d/m/Y');
            } elseif ($document->expiration_date) {
                // Para otros documentos: mostrar fecha de vencimiento
                $formSchema[] = DatePicker::make("expiration_date_{$document->id}")
                    ->label('Nueva fecha de vencimiento')
                    ->required(fn (callable $get): bool => ! empty($get("document_{$document->id}")))
                    ->native(false)
                    ->minDate(now()->addDay())
                    ->closeOnDateSelection()
                    ->helperText('Requerido si sube un nuevo documento');
            }

            $components[] = Section::make($document->type->getLabel())
                ->description($description)
                ->schema($formSchema)
                ->collapsible()
                ->collapsed(false)
                ->icon('heroicon-o-document-text');
        }

        return $schema
            ->components($components)
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        try {
            DB::beginTransaction();

            $documentsToUpdate = $this->driver->documents()
                ->where(function ($query) {
                    $query->where('status', DocumentStatusEnum::REJECTED)
                        ->orWhere('status', DocumentStatusEnum::NEEDS_UPDATE)
                        ->orWhere('status', DocumentStatusEnum::EXPIRING_SOON)
                        ->orWhere('expiration_date', '<', now());
                })
                ->get();

            foreach ($documentsToUpdate as $document) {
                $fieldName = "document_{$document->id}";

                if (isset($data[$fieldName]) && ! empty($data[$fieldName])) {
                    // El FileUpload de Filament ya guarda el archivo automáticamente
                    $newPath = is_array($data[$fieldName]) ? $data[$fieldName][0] : $data[$fieldName];

                    // Obtener extensiones del archivo antiguo y nuevo
                    $oldExtension = pathinfo($document->path, PATHINFO_EXTENSION);
                    $newExtension = pathinfo($newPath, PATHINFO_EXTENSION);

                    // Solo eliminar el archivo anterior si la extensión es diferente
                    if ($oldExtension !== $newExtension && $document->path && Storage::exists($document->path)) {
                        Storage::delete($document->path);
                    }

                    $updateData = [
                        'path' => $newPath,
                        'status' => DocumentStatusEnum::PENDING,
                        'rejection_reason' => null,
                        'validated_by' => null,
                        'validated_date' => null,
                        'submitted_date' => now(),
                    ];

                    // Manejar fechas según el tipo de documento
                    if ($document->type->requiresCourseDate()) {
                        // Para cursos: obtener course_date y calcular expiration_date
                        $courseDateField = "course_date_{$document->id}";
                        if (isset($data[$courseDateField]) && ! empty($data[$courseDateField])) {
                            $courseDate = \Carbon\Carbon::parse($data[$courseDateField]);
                            $updateData['course_date'] = $courseDate;
                            $updateData['expiration_date'] = $courseDate->copy()->addYears($document->type->getValidityYears());
                        }
                    } else {
                        // Para otros documentos: usar expiration_date directamente
                        $expirationField = "expiration_date_{$document->id}";
                        if (isset($data[$expirationField]) && ! empty($data[$expirationField])) {
                            $updateData['expiration_date'] = $data[$expirationField];
                        }
                    }

                    $document->update($updateData);
                }
            }

            // Actualizar estado del conductor a revisión de documentos y limpiar token
            $this->driver->update([
                'status' => EntityStatusEnum::PENDING_APPROVAL,
                'appeal_token' => null,
                'appeal_token_expires_at' => null,
            ]);

            DB::commit();

            $this->success = true;

            Notification::make()
                ->title('Documentos enviados exitosamente')
                ->success()
                ->body('Sus documentos han sido recibidos y serán revisados nuevamente.')
                ->send();

        } catch (Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error al procesar la apelación')
                ->danger()
                ->body('Ocurrió un error al procesar su apelación. Por favor, intente nuevamente.')
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.driver.appeal-form');
    }
}
