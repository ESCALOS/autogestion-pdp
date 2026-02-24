<?php

declare(strict_types=1);

namespace App\Livewire\Supplier\Auth;

use App\Enums\CompanyStatusEnum;
use App\Enums\CompanyTypeEnum;
use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Enums\PhoneCountryEnum;
use App\Models\Document;
use App\Models\Supplier;
use App\Models\SupplierUser;
use Exception;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

#[Layout('components.layouts.guest')]
final class Create extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public int $supplierType = 0;

    public function mount($supplierType = 0): void
    {
        $this->supplierType = $supplierType;
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('datos_proveedor')
                        ->label('Datos del Proveedor')
                        ->description('Información básica de su empresa')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('ruc')
                                        ->label('RUC')
                                        ->placeholder($this->supplierType === 1 ? '10XXXXXXXXX' : '20XXXXXXXXX')
                                        ->unique(table: Supplier::class, column: 'ruc')
                                        ->validationMessages([
                                            'unique' => 'El RUC ya está registrado.',
                                        ])
                                        ->required()
                                        ->numeric()
                                        ->length(11)
                                        ->rule(function () {
                                            return function (string $attribute, $value, $fail) {
                                                $prefix = $this->supplierType === 1 ? '10' : '20';
                                                if (! str_starts_with($value, $prefix)) {
                                                    $fail("El RUC debe comenzar con {$prefix} para este tipo de proveedor.");
                                                }
                                            };
                                        })
                                        ->extraInputAttributes(['class' => 'dark:text-gray-800']),

                                    TextInput::make('business_name')
                                        ->label('Razón Social')
                                        ->placeholder('Nombre de la empresa')
                                        ->required()
                                        ->maxLength(255)
                                        ->extraInputAttributes(['class' => 'text-gray-800']),
                                ]),
                        ])
                        ->extraAttributes(['class' => 'text-gray-800']),

                    Step::make('datos_representante')
                        ->label('Datos del Representante')
                        ->description('Información del representante legal del proveedor')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('representative_dni')
                                        ->label('DNI')
                                        ->placeholder('12345678')
                                        ->unique(table: SupplierUser::class, column: 'dni')
                                        ->validationMessages([
                                            'unique' => 'El DNI ya está registrado.',
                                        ])
                                        ->required()
                                        ->numeric()
                                        ->maxLength(8)
                                        ->extraInputAttributes(['class' => 'text-gray-800']),

                                    TextInput::make('representative_name')
                                        ->label('Nombres')
                                        ->placeholder('Juan Carlos')
                                        ->required()
                                        ->maxLength(255)
                                        ->extraInputAttributes(['class' => 'text-gray-800']),

                                    TextInput::make('representative_last_name')
                                        ->label('Apellidos')
                                        ->placeholder('Pérez García')
                                        ->required()
                                        ->maxLength(255)
                                        ->extraInputAttributes(['class' => 'text-gray-800']),
                                ]),

                            Grid::make(3)
                                ->schema([
                                    TextInput::make('representative_email')
                                        ->label('Correo Electrónico')
                                        ->placeholder('usuario@ejemplo.com')
                                        ->unique(table: SupplierUser::class, column: 'email')
                                        ->validationMessages([
                                            'unique' => 'El correo electrónico ya está registrado.',
                                        ])
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->extraInputAttributes(['class' => 'text-gray-800']),

                                    TextInput::make('representative_password')
                                        ->label('Contraseña')
                                        ->placeholder('Mínimo 8 caracteres')
                                        ->password()
                                        ->required()
                                        ->minLength(8)
                                        ->extraInputAttributes(['class' => 'text-gray-800']),

                                    TextInput::make('representative_password_confirmation')
                                        ->label('Confirmar Contraseña')
                                        ->placeholder('Repita la contraseña')
                                        ->password()
                                        ->required()
                                        ->same('representative_password')
                                        ->extraInputAttributes(['class' => 'text-gray-800']),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    Select::make('phone_prefix')
                                        ->label('País')
                                        ->placeholder('Selecciona un país')
                                        ->options(PhoneCountryEnum::options())
                                        ->required()
                                        ->native(false)
                                        ->searchable()
                                        ->extraInputAttributes(['class' => 'text-gray-800']),

                                    TextInput::make('phone_number')
                                        ->label('Número de Teléfono')
                                        ->placeholder('9123456789')
                                        ->required()
                                        ->numeric()
                                        ->minLength(7)
                                        ->maxLength(15)
                                        ->regex('/^[0-9]+$/', 'Solo se permiten números sin espacios')
                                        ->extraInputAttributes([
                                            'class' => 'text-gray-800',
                                            'inputmode' => 'numeric',
                                            'pattern' => '[0-9]*',
                                            '@input' => 'value = value.replace(/[^0-9]/g, "")',
                                        ]),
                                ]),                        ]),

                    Step::make('documentos')
                        ->label('Documentos Requeridos')
                        ->description('Suba los documentos necesarios para completar el registro')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    FileUpload::make('ruc_document')
                                        ->label('Ficha RUC')
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                        ->maxSize(10240)
                                        ->required()
                                        ->disk('public')
                                        ->directory(fn () => "PROVEEDORES/{$this->data['ruc']}/DOCUMENTOS")
                                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                            $extension = $file->getClientOriginalExtension();
                                            $typeName = mb_strtoupper(str_replace(' ', '_', DocumentTypeEnum::RUC_RECORD->getLabel()));

                                            return "{$typeName}.{$extension}";
                                        })
                                        ->helperText('Formatos aceptados: PDF, JPG, PNG (máx. 10MB)'),

                                    FileUpload::make('representative_dni_document')
                                        ->label('DNI del Representante')
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                        ->maxSize(10240)
                                        ->required()
                                        ->disk('public')
                                        ->directory(fn () => "PROVEEDORES/{$this->data['ruc']}/DOCUMENTOS")
                                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                            $extension = $file->getClientOriginalExtension();
                                            $typeName = mb_strtoupper(str_replace(' ', '_', DocumentTypeEnum::REPRESENTATIVE_DNI->getLabel()));

                                            return "{$typeName}.{$extension}";
                                        })
                                        ->helperText('Formatos aceptados: PDF, JPG, PNG (máx. 10MB)'),

                                    ...($this->supplierType === 2 ? [
                                        FileUpload::make('sunarp_document')
                                            ->label('Ficha SUNARP')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                            ->maxSize(10240)
                                            ->required()
                                            ->disk('public')
                                            ->directory(fn () => "PROVEEDORES/{$this->data['ruc']}/DOCUMENTOS")
                                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                $extension = $file->getClientOriginalExtension();
                                                $typeName = mb_strtoupper(str_replace(' ', '_', DocumentTypeEnum::SUNARP_RECORD->getLabel()));

                                                return "{$typeName}.{$extension}";
                                            })
                                            ->helperText('Formatos aceptados: PDF, JPG, PNG (máx. 10MB)'),

                                        FileUpload::make('power_of_attorney_document')
                                            ->label('Vigencia de Poder')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                            ->maxSize(10240)
                                            ->required()
                                            ->disk('public')
                                            ->directory(fn () => "PROVEEDORES/{$this->data['ruc']}/DOCUMENTOS")
                                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                $extension = $file->getClientOriginalExtension();
                                                $typeName = mb_strtoupper(str_replace(' ', '_', DocumentTypeEnum::POWER_OF_ATTORNEY_VALIDITY->getLabel()));

                                                return "{$typeName}.{$extension}";
                                            })
                                            ->helperText('Formatos aceptados: PDF, JPG, PNG (máx. 10MB)'),
                                    ] : []),
                                ]),
                        ]),
                ])
                    ->submitAction(new HtmlString('<button type="submit" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-10 px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">Registrar Proveedor</button>'))
                    ->extraAlpineAttributes(['@create-driver.window' => 'step = \'form.datos-de-la-empresa::data::wizard-step\''])
                    ->extraAttributes(['class' => 'bg-white text-gray-800 p-6 rounded-lg shadow-lg']),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        try {
            // Validar el formulario
            $data = $this->form->getState();

            DB::transaction(function () use ($data) {
                // Crear el proveedor
                $supplier = Supplier::create([
                    'type' => CompanyTypeEnum::from($this->supplierType),
                    'ruc' => $data['ruc'],
                    'business_name' => $data['business_name'],
                    'status' => CompanyStatusEnum::PENDIENTE,
                    'is_active' => false,
                    'phone_prefix' => $data['phone_prefix'],
                    'phone_number' => $data['phone_number'],
                    'phone_country' => PhoneCountryEnum::from($data['phone_prefix'])->getLabel(),
                ]);

                // Crear el usuario representante en supplier_users
                SupplierUser::create([
                    'supplier_id' => $supplier->id,
                    'dni' => $data['representative_dni'],
                    'name' => $data['representative_name'],
                    'lastname' => $data['representative_last_name'],
                    'email' => $data['representative_email'],
                    'password' => Hash::make($data['representative_password']),
                    'is_supplier_representative' => true,
                ]);

                // Guardar documento RUC
                $this->saveDocument(
                    $supplier,
                    DocumentTypeEnum::RUC_RECORD,
                    $data['ruc_document']
                );

                // Guardar documento DNI del representante
                $this->saveDocument(
                    $supplier,
                    DocumentTypeEnum::REPRESENTATIVE_DNI,
                    $data['representative_dni_document']
                );

                // Guardar documentos adicionales para proveedores jurídicos
                if ($this->supplierType === CompanyTypeEnum::JURIDICA->value) {
                    if (isset($data['sunarp_document'])) {
                        $this->saveDocument(
                            $supplier,
                            DocumentTypeEnum::SUNARP_RECORD,
                            $data['sunarp_document']
                        );
                    }

                    if (isset($data['power_of_attorney_document'])) {
                        $this->saveDocument(
                            $supplier,
                            DocumentTypeEnum::POWER_OF_ATTORNEY_VALIDITY,
                            $data['power_of_attorney_document']
                        );
                    }
                }
            });

            $this->dispatch('supplier-created');

            // Notificación de éxito
            Notification::make()
                ->title('Proveedor registrado exitosamente')
                ->body('Su solicitud está pendiente de aprobación.')
                ->success()
                ->send();

            // Redireccionar al login de proveedores
            $this->redirect(route('supplier.login'), navigate: true);

        } catch (Exception $e) {
            Log::alert('Error al registrar el proveedor: '.$e->getMessage());
            Notification::make()
                ->title('Error al registrar el proveedor')
                ->body('Ha ocurrido un error inesperado. Por favor, contacte al soporte.')
                ->danger()
                ->send();
        }
    }

    #[On('select-supplier-type')]
    public function onSelectSupplierType(int $type): void
    {
        $this->supplierType = $type;
    }

    public function render()
    {
        return view('livewire.supplier.auth.create');
    }

    protected function saveDocument(Supplier $supplier, DocumentTypeEnum $type, string|array $filePath): void
    {
        // Convertir array a string si es necesario (FileUpload puede devolver un array)
        $path = is_array($filePath) ? implode(',', $filePath) : $filePath;

        // El archivo ya está almacenado por Filament, solo necesitamos crear el registro
        Document::create([
            'documentable_type' => Supplier::class,
            'documentable_id' => $supplier->id,
            'type' => $type,
            'path' => $path,
            'status' => DocumentStatusEnum::PENDING,
            'submitted_date' => now(),
        ]);
    }
}
