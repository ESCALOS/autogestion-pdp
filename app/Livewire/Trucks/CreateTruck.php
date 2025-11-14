<?php

declare(strict_types=1);

namespace App\Livewire\Trucks;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Enums\EntityStatusEnum;
use App\Models\Document;
use App\Models\Truck;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class CreateTruck extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('truck_data')
                        ->label('Datos del Camión')
                        ->icon('heroicon-o-truck')
                        ->description('Información del vehículo')
                        ->schema([
                            TextInput::make('license_plate')
                                ->label('Placa')
                                ->required()
                                ->maxLength(10)
                                ->unique('trucks', 'license_plate', ignoreRecord: true)
                                ->validationMessages([
                                    'unique' => 'Ya existe un camión con esta placa.',
                                ]),

                            Select::make('nationality')
                                ->label('Nacionalidad')
                                ->options([
                                    'Peruana' => 'Peruana',
                                    'Extranjera' => 'Extranjera',
                                ])
                                ->required()
                                ->native(false),

                            Select::make('truck_type')
                                ->label('Tipo de Camión')
                                ->options([
                                    'T2' => 'T2',
                                    'T3' => 'T3',
                                    'T-Especial' => 'T-Especial',
                                    'Otro' => 'Otro',
                                ])
                                ->required()
                                ->native(false),

                            TextInput::make('tare')
                                ->label('Tara (Toneladas)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(99999.99)
                                ->suffix('Ton'),

                            Checkbox::make('is_internal')
                                ->label('¿Es interno?')
                                ->default(false),

                            Checkbox::make('has_bonus')
                                ->label('¿Tiene bonificación?')
                                ->default(false),
                        ])
                        ->columns(2),

                    Step::make('documents')
                        ->label('Documentos Obligatorios')
                        ->icon('heroicon-o-document-text')
                        ->description('Documentos del vehículo')
                        ->schema([
                            Section::make('Documentos del Vehículo')
                                ->description('Tarjeta de Propiedad, SOAT y Póliza de Seguro')
                                ->schema([
                                    // Tarjeta de Propiedad
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.tarjeta_propiedad.file')
                                                ->label('Tarjeta de Propiedad')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/TRUCKS/{$this->data['license_plate']}")
                                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                    $extension = $file->getClientOriginalExtension();
                                                    return 'TARJETA_PROPIEDAD.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.tarjeta_propiedad.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // SOAT
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.soat.file')
                                                ->label('SOAT')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/TRUCKS/{$this->data['license_plate']}")
                                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                    $extension = $file->getClientOriginalExtension();
                                                    return 'SOAT.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.soat.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Póliza de Seguro
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.poliza_seguro.file')
                                                ->label('Póliza de Seguro')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/TRUCKS/{$this->data['license_plate']}")
                                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                    $extension = $file->getClientOriginalExtension();
                                                    return 'POLIZA_SEGURO.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.poliza_seguro.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Revisión Técnica
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.revision_tecnica.file')
                                                ->label('Revisión Técnica')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/TRUCKS/{$this->data['license_plate']}")
                                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                    $extension = $file->getClientOriginalExtension();
                                                    return 'REVISION_TECNICA.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.revision_tecnica.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),
                                ]),
                        ]),

                    Step::make('optional_documents')
                        ->label('Documentos Opcionales')
                        ->icon('heroicon-o-document-plus')
                        ->description('Habilitación MTC y Bonificación')
                        ->schema([
                            Section::make('Documentos Opcionales')
                                ->description('Habilitación MTC y Bonificación (si aplica)')
                                ->schema([
                                    // Habilitación MTC
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.habilitacion_mtc.file')
                                                ->label('Habilitación MTC')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/TRUCKS/{$this->data['license_plate']}")
                                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                    $extension = $file->getClientOriginalExtension();
                                                    return 'HABILITACION_MTC.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.habilitacion_mtc.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Bonificación
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.bonificacion.file')
                                                ->label('Bonificación')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/TRUCKS/{$this->data['license_plate']}")
                                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                    $extension = $file->getClientOriginalExtension();
                                                    return 'BONIFICACION.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.bonificacion.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),
                                ]),
                        ]),
                ])
                    ->submitAction(new HtmlString('<button type="submit" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-10 px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">Crear Camión</button>'))
                    ->extraAlpineAttributes(['@truck-created.window' => 'step = \'form.datos-del-camion::data::wizard-step\''])
                    ->skippable(false),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        try {
            $data = $this->form->getState();

            DB::transaction(function () use ($data) {
                // Crear el camión
                $truck = Truck::create([
                    'company_id' => Auth::user()->company_id,
                    'license_plate' => $data['license_plate'],
                    'nationality' => $data['nationality'],
                    'truck_type' => $data['truck_type'],
                    'tare' => $data['tare'] ?? null,
                    'is_internal' => $data['is_internal'] ?? false,
                    'has_bonus' => $data['has_bonus'] ?? false,
                    'status' => EntityStatusEnum::PENDING_APPROVAL,
                ]);

                // Crear los documentos obligatorios
                $documentTypes = [
                    'tarjeta_propiedad' => DocumentTypeEnum::TARJETA_PROPIEDAD,
                    'soat' => DocumentTypeEnum::SOAT,
                    'poliza_seguro' => DocumentTypeEnum::POLIZA_SEGURO,
                    'revision_tecnica' => DocumentTypeEnum::REVISION_TECNICA,
                ];

                foreach ($documentTypes as $key => $type) {
                    if (isset($data['documents'][$key]['file']) && $data['documents'][$key]['file']) {
                        Document::create([
                            'documentable_type' => Truck::class,
                            'documentable_id' => $truck->id,
                            'type' => $type,
                            'path' => $data['documents'][$key]['file'],
                            'submitted_date' => now(),
                            'expiration_date' => $data['documents'][$key]['expiration_date'],
                            'status' => DocumentStatusEnum::PENDING, // Pendiente
                        ]);
                    }
                }

                // Crear documentos opcionales
                $optionalDocumentTypes = [
                    'habilitacion_mtc' => DocumentTypeEnum::HABILITACION_MTC,
                    'bonificacion' => DocumentTypeEnum::BONIFICACION,
                ];

                foreach ($optionalDocumentTypes as $key => $type) {
                    if (isset($data['documents'][$key]['file']) && $data['documents'][$key]['file']) {
                        Document::create([
                            'documentable_type' => Truck::class,
                            'documentable_id' => $truck->id,
                            'type' => $type,
                            'path' => $data['documents'][$key]['file'],
                            'submitted_date' => now(),
                            'expiration_date' => $data['documents'][$key]['expiration_date'] ?? null,
                            'status' => DocumentStatusEnum::PENDING, // Pendiente
                        ]);
                    }
                }
            });

            Notification::make()
                ->title('Camión creado exitosamente')
                ->success()
                ->send();

            $this->js(<<<'JS'
                Swal.fire({
                    icon: 'success',
                    title: 'Camión creado exitosamente',
                    text: 'El camión ha sido creado y está pendiente de aprobación.',
                    confirmButtonText: 'Aceptar'
                });
            JS);

            $this->reset();
            $this->form->fill();
            $this->dispatch('truck-created');

        } catch (Exception $e) {
            Notification::make()
                ->title('Error al crear el camión')
                ->body($e->getMessage())
                ->danger()
                ->send();
            Log::error('Error creating truck: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.trucks.create-truck');
    }
}
