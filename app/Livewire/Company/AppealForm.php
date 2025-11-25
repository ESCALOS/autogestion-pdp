<?php

declare(strict_types=1);

namespace App\Livewire\Company;

use App\Enums\CompanyDocumentStatusEnum;
use App\Models\Company;
use Exception;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

#[Layout('components.layouts.appeal')]
final class AppealForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?string $token = null;

    public ?Company $company = null;

    public ?array $data = [];

    public bool $success = false;

    public function mount(string $token): void
    {
        $this->token = $token;

        $this->company = Company::where('appeal_token', $token)
            ->where('appeal_token_expires_at', '>', now())
            ->with(['documents', 'representative'])
            ->first();

        if (! $this->company) {
            abort(404, 'El enlace de apelación es inválido o ha expirado.');
        }

        $rejectedDocuments = $this->company->documents()
            ->where('status', CompanyDocumentStatusEnum::RECHAZADO)
            ->get();

        if ($rejectedDocuments->isEmpty()) {
            abort(404, 'No hay documentos rechazados para apelar.');
        }

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $rejectedDocuments = $this->company->documents()
            ->where('status', CompanyDocumentStatusEnum::RECHAZADO)
            ->get();

        $components = [];

        Log::info('Rechazados', ['documents' => $rejectedDocuments->toArray()]);

        foreach ($rejectedDocuments as $document) {
            $components[] = Section::make($document->type->getLabel())
                ->description("Motivo de rechazo: {$document->rejection_reason}")
                ->schema([
                    FileUpload::make("document_{$document->id}")
                        ->label('Cargar nuevo documento')
                        ->required()
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'])
                        ->maxSize(5120)
                        ->directory(fn () => "EMPRESAS/{$this->company->ruc}/DOCUMENTOS")
                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) use ($document): string {
                            $extension = $file->getClientOriginalExtension();
                            $typeName = mb_strtoupper(str_replace(' ', '_', $document->type->getLabel()));
                            Log::info('Generando nombre archivo', ['typeName' => $typeName, 'extension' => $extension]);

                            return "{$typeName}.{$extension}";
                        })
                        ->helperText('Formatos permitidos: PDF, JPG, JPEG, PNG (máximo 5MB)'),
                ])
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

            $rejectedDocuments = $this->company->documents()
                ->where('status', CompanyDocumentStatusEnum::RECHAZADO)
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
                    // Si la extensión es la misma, el storage lo reemplaza automáticamente
                    if ($oldExtension !== $newExtension && $document->path && Storage::exists($document->path)) {
                        Storage::delete($document->path);
                    }

                    // Actualizar documento a pendiente
                    $document->update([
                        'path' => $newPath,
                        'status' => CompanyDocumentStatusEnum::PENDIENTE,
                        'rejection_reason' => null,
                        'validated_by' => null,
                        'validated_date' => null,
                        'submitted_date' => now(),
                    ]);
                }
            }

            // Actualizar estado de la empresa a pendiente y limpiar token
            $this->company->update([
                'status' => 1, // Pendiente
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

            Log::error('Error al procesar la apelación de documentos', [
                'company_id' => $this->company->id,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Error al procesar la apelación')
                ->danger()
                ->body('Ocurrió un error al procesar su apelación. Por favor, intente nuevamente.')
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.company.appeal-form');
    }
}
