<?php

declare(strict_types=1);

namespace App\Livewire\Supplier\Machinery;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Enums\EntityStatusEnum;
use App\Enums\MachineryUnitTypeEnum;
use App\Models\Document;
use App\Models\SupplierMachinery;
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

final class Create extends Component implements HasSchemas
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
                    Step::make('machinery_data')
                        ->label('Datos de la Maquinaria')
                        ->icon('heroicon-o-truck')
                        ->description('Información del vehículo')
                        ->schema([
                            TextInput::make('license_plate')
                                ->label('Placa')
                                ->required()
                                ->maxLength(6)
                                ->regex('/^[A-Za-z0-9]+$/')
                                ->unique('supplier_machinery', 'license_plate', modifyRuleUsing: function ($rule) {
                                    return $rule->where('supplier_id', Auth::guard('supplier')->user()->supplier_id);
                                })
                                ->validationMessages([
                                    'unique' => 'Ya existe una maquinaria con esta placa en tu empresa.',
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
                                ->label('Tipo de Unidad')
                                ->options(collect(MachineryUnitTypeEnum::cases())
                                    ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
                                    ->toArray())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false),

                            TextInput::make('tare')
                                ->label('Peso Neto')
                                ->numeric()
                                ->required()
                                ->step(0.001)
                                ->minValue(0)
                                ->maxValue(99.999)
                                ->suffix('Toneladas')
                                ->placeholder('Ej: 12.500')
                                ->helperText('Ingrese el peso en TONELADAS (máx. 99.999)'),

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
                            Section::make('Documentos de la Maquinaria')
                                ->description('Tarjeta de Propiedad, SOAT, MTC y Póliza de Seguro')
                                ->schema([
                                    // Tarjeta de Propiedad
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.tarjeta_propiedad.file')
                                                ->label('Tarjeta de Propiedad')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(10240)
                                                ->required()
                                                ->directory(fn () => 'PROVEEDORES/'.Auth::guard('supplier')->user()->supplier->ruc."/MACHINERY/{$this->data['license_plate']}")
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
                                                ->maxSize(10240)
                                                ->required()
                                                ->directory(fn () => 'PROVEEDORES/'.Auth::guard('supplier')->user()->supplier->ruc."/MACHINERY/{$this->data['license_plate']}")
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
                                                ->closeOnDateSelection()
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Habilitación MTC
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.habilitacion_mtc.file')
                                                ->label('Habilitación MTC')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(10240)
                                                ->required()
                                                ->directory(fn () => 'PROVEEDORES/'.Auth::guard('supplier')->user()->supplier->ruc."/MACHINERY/{$this->data['license_plate']}")
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
                                                ->closeOnDateSelection()
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Póliza de Seguro
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.poliza_seguro.file')
                                                ->label('Póliza de Seguro')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(10240)
                                                ->required()
                                                ->directory(fn () => 'PROVEEDORES/'.Auth::guard('supplier')->user()->supplier->ruc."/MACHINERY/{$this->data['license_plate']}")
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
                                                ->closeOnDateSelection()
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Revisión Técnica
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.revision_tecnica.file')
                                                ->label('Revisión Técnica')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(10240)
                                                ->required()
                                                ->directory(fn () => 'PROVEEDORES/'.Auth::guard('supplier')->user()->supplier->ruc."/MACHINERY/{$this->data['license_plate']}")
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
                                                ->closeOnDateSelection()
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
                                                ->maxSize(10240)
                                                ->directory(fn () => 'PROVEEDORES/'.Auth::guard('supplier')->user()->supplier->ruc."/MACHINERY/{$this->data['license_plate']}")
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
                                                ->closeOnDateSelection()
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1)
                                                ->helperText('Requerido si sube el documento de bonificación.')
                                                ->required(fn (callable $get): bool => ! empty($get('documents.bonificacion.file'))),
                                        ]),
                                ]),
                        ]),
                ])
                    ->submitAction(new HtmlString('<button type="submit" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-10 px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">Crear maquinaria</button>'))
                    ->extraAlpineAttributes(['@machinery-created.window' => 'step = \'form.datos-de-la-maquinaria::data::wizard-step\''])
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

                // Crear la maquinaria
                $machinery = SupplierMachinery::create([
                    'supplier_id' => Auth::guard('supplier')->user()->supplier_id,
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
                            'documentable_type' => SupplierMachinery::class,
                            'documentable_id' => $machinery->id,
                            'type' => $type,
                            'path' => $data['documents'][$key]['file'],
                            'submitted_date' => now(),
                            'expiration_date' => $data['documents'][$key]['expiration_date'] ?? null,
                            'status' => DocumentStatusEnum::PENDING,
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
                            'documentable_type' => SupplierMachinery::class,
                            'documentable_id' => $machinery->id,
                            'type' => $type,
                            'path' => $data['documents'][$key]['file'],
                            'submitted_date' => now(),
                            'expiration_date' => $data['documents'][$key]['expiration_date'] ?? null,
                            'status' => DocumentStatusEnum::PENDING,
                        ]);
                    }
                }
            });

            $this->js(<<<'JS'
                Swal.fire({
                    icon: 'success',
                    title: 'Maquinaria creada exitosamente',
                    text: 'La maquinaria ha sido creada y está pendiente de aprobación.',
                    confirmButtonText: 'Aceptar'
                });
            JS);

            $this->reset();
            $this->form->fill();
            $this->dispatch('machinery-created');

        } catch (Exception $e) {
            Notification::make()
                ->title('Error al crear la maquinaria')
                ->body($e->getMessage())
                ->danger()
                ->send();
            Log::error('Error creating supplier machinery: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.supplier.machinery.create');
    }
}
