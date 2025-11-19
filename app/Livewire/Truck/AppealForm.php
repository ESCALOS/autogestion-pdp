<?php

declare(strict_types=1);

namespace App\Livewire\Truck;

use App\Enums\DocumentStatusEnum;
use App\Enums\EntityStatusEnum;
use App\Models\Truck;
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

    public ?Truck $truck = null;

    public ?array $data = [];

    public bool $success = false;

    public function mount(string $token): void
    {
        $this->token = $token;

        $this->truck = Truck::where('appeal_token', $token)
            ->where('appeal_token_expires_at', '>', now())
            ->with(['documents', 'company'])
            ->first();

        if (! $this->truck) {
            abort(404, 'El enlace de apelación es inválido o ha expirado.');
        }

        $rejectedDocuments = $this->truck->documents()
            ->where(function ($query) {
                $query->where('status', DocumentStatusEnum::REJECTED)
                    ->orWhere('expiration_date', '<', now());
            })
            ->get();

        if ($rejectedDocuments->isEmpty()) {
            abort(404, 'No hay documentos rechazados o vencidos para apelar.');
        }

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $rejectedDocuments = $this->truck->documents()
            ->where(function ($query) {
                $query->where('status', DocumentStatusEnum::REJECTED)
                    ->orWhere('expiration_date', '<', now());
            })
            ->get();

        $components = [];

        foreach ($rejectedDocuments as $document) {
            $isExpired = $document->expiration_date && $document->expiration_date < now();
            $description = $isExpired
                ? "Documento vencido el: {$document->expiration_date->format('d/m/Y')}"
                : "Motivo de rechazo: {$document->rejection_reason}";

            $formSchema = [
                FileUpload::make("document_{$document->id}")
                    ->label('Cargar nuevo documento')
                    ->required()
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'])
                    ->maxSize(5120)
                    ->directory(fn () => "EMPRESAS/{$this->truck->company->ruc}/TRUCKS/{$this->truck->license_plate}")
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) use ($document): string {
                        $extension = $file->getClientOriginalExtension();
                        $typeName = mb_strtoupper(str_replace(' ', '_', $document->type->getLabel()));

                        return "{$typeName}.{$extension}";
                    })
                    ->helperText('Formatos permitidos: PDF, JPG, JPEG, PNG (máximo 5MB)'),
            ];

            // Agregar campo de fecha de vencimiento si el documento lo requiere
            if ($document->expiration_date) {
                $formSchema[] = DatePicker::make("expiration_date_{$document->id}")
                    ->label('Nueva fecha de vencimiento')
                    ->required()
                    ->native(false)
                    ->minDate(now()->addDay())
                    ->closeOnDateSelection()
                    ->helperText('Seleccione la nueva fecha de vencimiento del documento');
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

            $rejectedDocuments = $this->truck->documents()
                ->where(function ($query) {
                    $query->where('status', DocumentStatusEnum::REJECTED)
                        ->orWhere('expiration_date', '<', now());
                })
                ->get();

            foreach ($rejectedDocuments as $document) {
                $fieldName = "document_{$document->id}";

                if (isset($data[$fieldName]) && ! empty($data[$fieldName])) {
                    // El FileUpload de Filament ya guarda el archivo automáticamente
                    $newPath = is_array($data[$fieldName]) ? $data[$fieldName][0] : $data[$fieldName];

                    // Obtener extensiones del archivo antiguo y nuevo
                    $oldExtension = pathinfo($document->path, PATHINFO_EXTENSION);
                    $newExtension = pathinfo($newPath, PATHINFO_EXTENSION);

                    // Solo eliminar el archivo anterior si la extensión es diferente
                    if ($oldExtension !== $newExtension && $document->path && Storage::disk('s3')->exists($document->path)) {
                        Storage::disk('s3')->delete($document->path);
                    }

                    $updateData = [
                        'path' => $newPath,
                        'status' => DocumentStatusEnum::PENDING,
                        'rejection_reason' => null,
                        'validated_by' => null,
                        'validated_date' => null,
                        'submitted_date' => now(),
                    ];

                    // Actualizar fecha de vencimiento si se proporcionó
                    $expirationField = "expiration_date_{$document->id}";
                    if (isset($data[$expirationField]) && ! empty($data[$expirationField])) {
                        $updateData['expiration_date'] = $data[$expirationField];
                    }

                    $document->update($updateData);
                }
            }

            // Actualizar estado del camión a revisión de documentos y limpiar token
            $this->truck->update([
                'status' => EntityStatusEnum::DOCUMENT_REVIEW,
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
        return view('livewire.truck.appeal-form');
    }
}
