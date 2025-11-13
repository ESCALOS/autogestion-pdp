<?php

declare(strict_types=1);

namespace App\Livewire\Drivers;

use App\Enums\DocumentTypeEnum;
use App\Enums\DriverDocumentTypeEnum;
use App\Enums\EntityStatusEnum;
use App\Models\Document;
use App\Models\Driver;
use Exception;
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

final class CreateDriver extends Component implements HasSchemas
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
                    Step::make('driver_data')
                        ->label('Datos del Conductor')
                        ->icon('heroicon-o-user')
                        ->description('Información del conductor')
                        ->schema([
                            Select::make('document_type')
                                ->label('Tipo de Documento')
                                ->options(DriverDocumentTypeEnum::class)
                                ->required()
                                ->native(false)
                                ->default(DriverDocumentTypeEnum::DNI),

                            TextInput::make('document_number')
                                ->label('Número de Documento')
                                ->required()
                                ->maxLength(20)
                                ->unique('drivers', 'document_number', ignoreRecord: true)
                                ->validationMessages([
                                    'unique' => 'Ya existe un conductor con este número de documento.',
                                ]),

                            TextInput::make('name')
                                ->label('Nombres')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('lastname')
                                ->label('Apellidos')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('license_number')
                                ->label('Número de Licencia')
                                ->required()
                                ->maxLength(20),
                        ])
                        ->columns(2),

                    Step::make('Documentos')
                        ->icon('heroicon-o-document-text')
                        ->description('Documentos del conductor')
                        ->schema([
                            Section::make('Documentos de Identidad y Licencias')
                                ->description('DNI y Licencias de Conducir')
                                ->schema([
                                    // DNI
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.dni.file')
                                                ->label('DNI')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->validationAttribute('DNI')
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/DRIVERS/{$this->data['document_number']}")
                                                ->columnSpan(2),

                                            DatePicker::make('documents.dni.expiration_date')
                                                ->label('Fecha de vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Licencia de Conducir A1
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.licencia_de_conducir.file')
                                                ->label('Licencia de conducir')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/DRIVERS/{$this->data['document_number']}")
                                                ->columnSpan(2),

                                            DatePicker::make('documents.licencia_de_conducir.expiration_date')
                                                ->label('Fecha de vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),
                                    // Inducción de Seguridad
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.induccion_seguridad.file')
                                                ->label('Inducción de Seguridad y Medio Ambiente Virtual')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/DRIVERS/{$this->data['document_number']}")
                                                ->columnSpan(2),

                                            DatePicker::make('documents.induccion_seguridad.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Declaración Jurada
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.declaracion_jurada.file')
                                                ->label('Declaración Jurada de no poseer Antecedentes')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/DRIVERS/{$this->data['document_number']}")
                                                ->columnSpan(2),

                                            DatePicker::make('documents.declaracion_jurada.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),
                                ]),
                        ]),

                    Step::make('Cursos')
                        ->icon('heroicon-o-academic-cap')
                        ->description('Certificados de cursos requeridos')
                        ->schema([
                            Section::make('Certificados de Cursos')
                                ->description('PBIP, Seguridad Portuaria y Mercancías Peligrosas')
                                ->schema([
                                    // Curso PBIP
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.curso_pbip.file')
                                                ->label('Certificado Curso Básico PBIP I')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/DRIVERS/{$this->data['document_number']}")
                                                ->columnSpan(2),

                                            DatePicker::make('documents.curso_pbip.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Curso Seguridad Portuaria
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.curso_seguridad_portuaria.file')
                                                ->label('Certificado Curso Básico de Seguridad Portuaria')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/DRIVERS/{$this->data['document_number']}")
                                                ->columnSpan(2),

                                            DatePicker::make('documents.curso_seguridad_portuaria.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Curso Mercancías Peligrosas
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.curso_mercancias.file')
                                                ->label('Certificado Curso Básico de Mercancías Peligrosas')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/DRIVERS/{$this->data['document_number']}")
                                                ->columnSpan(2),

                                            DatePicker::make('documents.curso_mercancias.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),
                                ]),
                        ]),

                    Step::make('SCTR')
                        ->icon('heroicon-o-shield-check')
                        ->description('Seguro Complementario de Trabajo de Riesgo')
                        ->schema([
                            Section::make('SCTR')
                                ->description('Salud y Pensión')
                                ->schema([
                                    // SCTR
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.sctr.file')
                                                ->label('SCTR (Salud y Pensión)')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/DRIVERS/{$this->data['document_number']}")
                                                ->columnSpan(2),

                                            DatePicker::make('documents.sctr.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),
                                ]),
                        ]),
                ])
                    ->submitAction(new HtmlString('<button type="submit" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-10 px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">Crear Conductor</button>'))
                    ->extraAlpineAttributes(['@driver-created.window' => 'step = \'form.datos-del-conductor::data::wizard-step\''])
                    ->skippable(false),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        try {
            $data = $this->form->getState();

            DB::transaction(function () use ($data) {
                // Crear el conductor
                $driver = Driver::create([
                    'company_id' => Auth::user()->company_id,
                    'document_type' => $data['document_type'],
                    'document_number' => $data['document_number'],
                    'name' => $data['name'],
                    'lastname' => $data['lastname'],
                    'license_number' => $data['license_number'],
                    'status' => EntityStatusEnum::PENDING_APPROVAL,
                ]);

                // Crear los documentos
                $documentTypes = [
                    'dni' => DocumentTypeEnum::DNI,
                    'licencia_de_conducir' => DocumentTypeEnum::LICENCIA_DE_CONDUCIR,
                    'curso_pbip' => DocumentTypeEnum::CURSO_PBIP,
                    'curso_seguridad_portuaria' => DocumentTypeEnum::CURSO_SEGURIDAD_PORTUARIA,
                    'curso_mercancias' => DocumentTypeEnum::CURSO_MERCANCIAS,
                    'sctr' => DocumentTypeEnum::SCTR,
                    'induccion_seguridad' => DocumentTypeEnum::INDUCCION_SEGURIDAD,
                    'declaracion_jurada' => DocumentTypeEnum::DECLARACION_JURADA,
                ];

                foreach ($documentTypes as $key => $type) {
                    if (isset($data['documents'][$key]['file']) && $data['documents'][$key]['file']) {
                        Document::create([
                            'documentable_type' => Driver::class,
                            'documentable_id' => $driver->id,
                            'type' => $type,
                            'path' => $data['documents'][$key]['file'],
                            'submitted_date' => now(),
                            'expiration_date' => $data['documents'][$key]['expiration_date'],
                            'status' => 1, // Pendiente
                        ]);
                    }
                }
            });

            Notification::make()
                ->title('Conductor creado exitosamente')
                ->success()
                ->send();

            $this->js(<<<'JS'
                Swal.fire({
                    icon: 'success',
                    title: 'Conductor creado exitosamente',
                    text: 'El conductor ha sido creado y está pendiente de aprobación.',
                    confirmButtonText: 'Aceptar'
                });
            JS);

            $this->reset();

            $this->form->fill();
            $this->dispatch('driver-created');

        } catch (Exception $e) {
            Notification::make()
                ->title('Error al crear el conductor')
                ->body($e->getMessage())
                ->danger()
                ->send();
            Log::error('Error creating driver: '.$e->getMessage());
        }
    }

    public function resetForm(): void
    {
        $this->form->reset();
    }

    public function render(): View
    {
        return view('livewire.drivers.create-driver');
    }
}
