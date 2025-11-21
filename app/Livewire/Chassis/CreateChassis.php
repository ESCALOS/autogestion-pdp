<?php

declare(strict_types=1);

namespace App\Livewire\Chassis;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Enums\EntityStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Models\Chassis;
use App\Models\Document;
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

final class CreateChassis extends Component implements HasSchemas
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
                    Step::make('chassis_data')
                        ->label('Datos del Chassis')
                        ->icon('heroicon-o-truck')
                        ->description('Información del vehículo')
                        ->schema([
                            TextInput::make('license_plate')
                                ->label('Placa')
                                ->required()
                                ->maxLength(10)
                                ->unique('chassis', 'license_plate', modifyRuleUsing: function ($rule) {
                                    return $rule->where('company_id', Auth::user()->company_id);
                                })
                                ->validationMessages([
                                    'unique' => 'Ya existe un chassis con esta placa en tu empresa.',
                                ]),

                            Select::make('vehicle_type')
                                ->label('Tipo de Vehículo')
                                ->options(VehicleTypeEnum::class)
                                ->searchable()
                                ->preload()
                                ->native(false),

                            TextInput::make('axle_count')
                                ->label('Número de Ejes')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(10),

                            TextInput::make('tare')
                                ->label('Tara (Toneladas)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(99999.99)
                                ->suffix('Ton'),

                            TextInput::make('safe_weight')
                                ->label('Peso Seguro (Toneladas)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(99999.99)
                                ->suffix('Ton'),

                            TextInput::make('length')
                                ->label('Largo (Metros)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(99.99)
                                ->suffix('m'),

                            TextInput::make('width')
                                ->label('Ancho (Metros)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(99.99)
                                ->suffix('m'),

                            TextInput::make('height')
                                ->label('Alto (Metros)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(99.99)
                                ->suffix('m'),

                            Select::make('material')
                                ->label('Material')
                                ->options([
                                    'Acero' => 'Acero',
                                    'Aluminio' => 'Aluminio',
                                    'Fibra de Vidrio' => 'Fibra de Vidrio',
                                    'Madera' => 'Madera',
                                    'Otro' => 'Otro',
                                ])
                                ->native(false),

                            Checkbox::make('is_insulated')
                                ->label('¿Es aislado?')
                                ->default(false),

                            Checkbox::make('accepts_20ft')
                                ->label('¿Acepta contenedores de 20ft?')
                                ->default(false),

                            Checkbox::make('accepts_40ft')
                                ->label('¿Acepta contenedores de 40ft?')
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
                            Section::make('Documentos del Chassis')
                                ->description('Revisión Técnica (obligatorio)')
                                ->schema([
                                    // Revisión Técnica
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.chassis_revision_tecnica.file')
                                                ->label('Revisión Técnica')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/CHASSIS/{$this->data['license_plate']}")
                                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                    $extension = $file->getClientOriginalExtension();
                                                    return 'REVISION_TECNICA.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.chassis_revision_tecnica.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->required()
                                                ->native(false)
                                                ->minDate(now()->addDay())
                                                ->closeOnDateSelection()
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
                                            FileUpload::make('documents.chassis_habilitacion_mtc.file')
                                                ->label('Habilitación MTC')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/CHASSIS/{$this->data['license_plate']}")
                                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                    $extension = $file->getClientOriginalExtension();
                                                    return 'HABILITACION_MTC.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.chassis_habilitacion_mtc.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->native(false)
                                                ->minDate(now()->addDay())
                                                ->closeOnDateSelection()
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),

                                    // Bonificación
                                    Grid::make(3)
                                        ->schema([
                                            FileUpload::make('documents.chassis_bonificacion.file')
                                                ->label('Bonificación')
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(5120)
                                                ->directory(fn () => 'EMPRESAS/'.Auth::user()->company->ruc."/CHASSIS/{$this->data['license_plate']}")
                                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                    $extension = $file->getClientOriginalExtension();
                                                    return 'BONIFICACION.'.$extension;
                                                })
                                                ->columnSpan(2),

                                            DatePicker::make('documents.chassis_bonificacion.expiration_date')
                                                ->label('Fecha de Vencimiento')
                                                ->native(false)
                                                ->minDate(now()->addDay())
                                                ->closeOnDateSelection()
                                                ->displayFormat('d/m/Y')
                                                ->columnSpan(1),
                                        ]),
                                ]),
                        ]),
                ])
                    ->submitAction(new HtmlString('<button type="submit" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-10 px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">Crear Chassis</button>'))
                    ->extraAlpineAttributes(['@chassis-created.window' => 'step = \'form.datos-del-chassis::data::wizard-step\''])
                    ->skippable(false),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        try {
            $data = $this->form->getState();

            DB::transaction(function () use ($data) {
                // Crear el chassis
                $chassis = Chassis::create([
                    'company_id' => Auth::user()->company_id,
                    'license_plate' => $data['license_plate'],
                    'vehicle_type' => $data['vehicle_type'] ?? null,
                    'axle_count' => $data['axle_count'] ?? null,
                    'tare' => $data['tare'] ?? null,
                    'safe_weight' => $data['safe_weight'] ?? null,
                    'length' => $data['length'] ?? null,
                    'width' => $data['width'] ?? null,
                    'height' => $data['height'] ?? null,
                    'material' => $data['material'] ?? null,
                    'is_insulated' => $data['is_insulated'] ?? false,
                    'accepts_20ft' => $data['accepts_20ft'] ?? false,
                    'accepts_40ft' => $data['accepts_40ft'] ?? false,
                    'has_bonus' => $data['has_bonus'] ?? false,
                    'status' => EntityStatusEnum::PENDING_APPROVAL,
                ]);

                // Crear el documento obligatorio
                $documentTypes = [
                    'chassis_revision_tecnica' => DocumentTypeEnum::CHASSIS_REVISION_TECNICA,
                ];

                foreach ($documentTypes as $key => $type) {
                    if (isset($data['documents'][$key]['file']) && $data['documents'][$key]['file']) {
                        Document::create([
                            'documentable_type' => Chassis::class,
                            'documentable_id' => $chassis->id,
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
                    'chassis_habilitacion_mtc' => DocumentTypeEnum::CHASSIS_HABILITACION_MTC,
                    'chassis_bonificacion' => DocumentTypeEnum::CHASSIS_BONIFICACION,
                ];

                foreach ($optionalDocumentTypes as $key => $type) {
                    if (isset($data['documents'][$key]['file']) && $data['documents'][$key]['file']) {
                        Document::create([
                            'documentable_type' => Chassis::class,
                            'documentable_id' => $chassis->id,
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
                ->title('Chassis creado exitosamente')
                ->success()
                ->send();

            $this->js(<<<'JS'
                Swal.fire({
                    icon: 'success',
                    title: 'Chassis creado exitosamente',
                    text: 'El chassis ha sido creado y está pendiente de aprobación.',
                    confirmButtonText: 'Aceptar'
                });
            JS);

            $this->reset();
            $this->form->fill();
            $this->dispatch('chassis-created');

        } catch (Exception $e) {
            Notification::make()
                ->title('Error al crear el chassis')
                ->body($e->getMessage())
                ->danger()
                ->send();
            Log::error('Error creating chassis: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.chassis.create-chassis');
    }
}
