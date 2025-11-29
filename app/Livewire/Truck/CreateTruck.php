<?php

declare(strict_types=1);

namespace App\Livewire\Truck;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Enums\EntityStatusEnum;
use App\Enums\TruckTypeEnum;
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
                                ->maxLength(6)
                                ->regex('/^[A-Za-z0-9]+$/')
                                ->unique('trucks', 'license_plate', modifyRuleUsing: function ($rule) {
                                    return $rule->where('company_id', Auth::user()->company_id);
                                })
                                ->validationMessages([
                                    'unique' => 'Ya existe un tracto con esta placa en tu empresa.',
                                    'regex' => 'La placa solo puede contener letras y números, sin espacios ni caracteres especiales.',
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
                                ->options(TruckTypeEnum::class)
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false),

                            TextInput::make('tare')
                                ->label('Peso Neto (Toneladas)')
                                ->numeric()
                                ->required()
                                ->step(0.001)
                                ->minValue(0)
                                ->maxValue(99999.999)
                                ->suffix('Ton'),

                            Checkbox::make('is_internal')
                                ->label('¿Es interno?')
                                ->default(false),
                        ])
                        ->columns(2),

                    Step::make('documents')
                        ->label('Documentos Obligatorios')
                        ->icon('heroicon-o-document-text')
                        ->description('Documentos del vehículo')
                        ->schema([
                            Section::make('Documentos del Vehículo')
                                ->description('Tarjeta de Propiedad, SOAT, MTC y Póliza de Seguro')
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

                                                    return DocumentTypeEnum::TARJETA_PROPIEDAD->getFileName().'.'.$extension;
                                                })
                                                ->columnSpan(3),

                                            // DatePicker::make('documents.tarjeta_propiedad.expiration_date')
                                            //     ->label('Fecha de Vencimiento')
                                            //     ->required()
                                            //     ->native(false)
                                            //     ->displayFormat('d/m/Y')
                                            //     ->columnSpan(1),
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

                                                    return DocumentTypeEnum::SOAT->getFileName().'.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.soat.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->minDate(today())
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Habilitación MTC
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.habilitacion_mtc.file')
                                                ->label('Habilitación MTC')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/TRUCKS/{$this->data['license_plate']}")
                                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                    $extension = $file->getClientOriginalExtension();

                                                    return DocumentTypeEnum::HABILITACION_MTC->getFileName().'.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.habilitacion_mtc.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->native(false)
                                                ->required()
                                                ->minDate(today())
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

                                                    return DocumentTypeEnum::POLIZA_SEGURO->getFileName().'.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.poliza_seguro.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->minDate(today())
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

                                                    return DocumentTypeEnum::REVISION_TECNICA->getFileName().'.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.revision_tecnica.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->minDate(today())
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),
                                ]),
                        ]),

                    Step::make('optional_documents')
                        ->label('Documentos Opcionales')
                        ->icon('heroicon-o-document-plus')
                        ->description('Bonificación')
                        ->schema([
                            Section::make('Documentos Opcionales')
                                ->description('Bonificación (si aplica)')
                                ->schema([

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

                                                    return DocumentTypeEnum::BONIFICACION->getFileName().'.'.$extension;
                                                })
                                                ->columnSpan(2)
                                                ->live(),

                                            DatePicker::make('documents.bonificacion.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->native(false)
                                                ->minDate(today())
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1)
                                                ->helperText('Requerido si sube el documento de bonificación.')
                                                ->required(fn (callable $get): bool => ! empty($get('documents.bonificacion.file'))),
                                        ]),
                                ]),
                        ]),
                ])
                    ->submitAction(new HtmlString('<button type="submit" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-10 px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">Crear tracto</button>'))
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
                // Determinar has_bonus basándose en si se subió el documento
                $hasBonus = ! empty($data['documents']['bonificacion']['file']);

                // Crear el camión
                $truck = Truck::create([
                    'company_id' => Auth::user()->company_id,
                    'license_plate' => $data['license_plate'],
                    'nationality' => $data['nationality'],
                    'truck_type' => $data['truck_type'],
                    'tare' => $data['tare'] ?? null,
                    'is_internal' => $data['is_internal'] ?? false,
                    'has_bonus' => $hasBonus,
                    'status' => EntityStatusEnum::PENDING_APPROVAL,
                ]);

                // Crear los documentos obligatorios
                $documentTypes = [
                    'tarjeta_propiedad' => DocumentTypeEnum::TARJETA_PROPIEDAD,
                    'soat' => DocumentTypeEnum::SOAT,
                    'poliza_seguro' => DocumentTypeEnum::POLIZA_SEGURO,
                    'revision_tecnica' => DocumentTypeEnum::REVISION_TECNICA,
                    'habilitacion_mtc' => DocumentTypeEnum::HABILITACION_MTC,
                ];

                foreach ($documentTypes as $key => $type) {
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

                // Crear documentos opcionales
                $optionalDocumentTypes = [
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
        return view('livewire.truck.create-truck');
    }
}
