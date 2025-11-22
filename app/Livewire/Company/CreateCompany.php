<?php

declare(strict_types=1);

namespace App\Livewire\Company;

use App\Enums\CompanyDocumentStatusEnum;
use App\Enums\CompanyDocumentTypeEnum;
use App\Enums\CompanyStatusEnum;
use App\Enums\CompanyTypeEnum;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Models\User;
use Exception;
use Filament\Forms\Components\FileUpload;
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
final class CreateCompany extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public int $companyType = 0;

    public function mount($companyType = 0): void
    {
        $this->companyType = $companyType;
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('datos_empresa')
                        ->label('Datos de la Empresa')
                        ->description('Información básica de su empresa')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('ruc')
                                        ->label('RUC')
                                        ->placeholder($this->companyType === 1 ? '10XXXXXXXXX' : '20XXXXXXXXX')
                                        ->unique(table: Company::class, column: 'ruc')
                                        ->validationMessages([
                                            'unique' => 'El RUC ya está registrado.',
                                        ])
                                        ->required()
                                        ->numeric()
                                        ->length(11)
                                        ->rule(function () {
                                            return function (string $attribute, $value, $fail) {
                                                $prefix = $this->companyType === 1 ? '10' : '20';
                                                if (!str_starts_with($value, $prefix)) {
                                                    $fail("El RUC debe comenzar con {$prefix} para este tipo de empresa.");
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
                        ->description('Información del representante legal de la empresa')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('representative_dni')
                                        ->label('DNI')
                                        ->placeholder('12345678')
                                        ->unique(table: User::class, column: 'dni')
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
                                        ->unique(table: User::class, column: 'email')
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
                        ]),

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
                                        ->maxSize(5120)
                                        ->required()
                                        ->directory(fn () => "EMPRESAS/{$this->data['ruc']}/DOCUMENTOS")
                                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                            $extension = $file->getClientOriginalExtension();
                                            $typeName = mb_strtoupper(str_replace(' ', '_', CompanyDocumentTypeEnum::RUC_RECORD->getLabel()));

                                            return "{$typeName}.{$extension}";
                                        })
                                        ->helperText('Formatos aceptados: PDF, JPG, PNG (máx. 5MB)'),

                                    FileUpload::make('representative_dni_document')
                                        ->label('DNI del Representante')
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                        ->maxSize(5120)
                                        ->required()
                                        ->directory(fn () => "EMPRESAS/{$this->data['ruc']}/DOCUMENTOS")
                                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                            $extension = $file->getClientOriginalExtension();
                                            $typeName = mb_strtoupper(str_replace(' ', '_', CompanyDocumentTypeEnum::REPRESENTATIVE_DNI->getLabel()));

                                            return "{$typeName}.{$extension}";
                                        })
                                        ->helperText('Formatos aceptados: PDF, JPG, PNG (máx. 5MB)'),

                                    ...($this->companyType === 2 ? [
                                        FileUpload::make('sunarp_document')
                                            ->label('Ficha SUNARP')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                            ->maxSize(5120)
                                            ->required()
                                            ->directory(fn () => "EMPRESAS/{$this->data['ruc']}/DOCUMENTOS")
                                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                $extension = $file->getClientOriginalExtension();
                                                $typeName = mb_strtoupper(str_replace(' ', '_', CompanyDocumentTypeEnum::SUNARP_RECORD->getLabel()));

                                                return "{$typeName}.{$extension}";
                                            })
                                            ->helperText('Formatos aceptados: PDF, JPG, PNG (máx. 5MB)'),

                                        FileUpload::make('power_of_attorney_document')
                                            ->label('Vigencia de Poder')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                            ->maxSize(5120)
                                            ->required()
                                            ->directory(fn () => "EMPRESAS/{$this->data['ruc']}/DOCUMENTOS")
                                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                $extension = $file->getClientOriginalExtension();
                                                $typeName = mb_strtoupper(str_replace(' ', '_', CompanyDocumentTypeEnum::POWER_OF_ATTORNEY_VALIDITY->getLabel()));

                                                return "{$typeName}.{$extension}";
                                            })
                                            ->helperText('Formatos aceptados: PDF, JPG, PNG (máx. 5MB)'),
                                    ] : []),
                                ]),
                        ]),
                ])
                    ->submitAction(new HtmlString('<button type="submit" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-10 px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">Registrar Empresa</button>'))
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
                // Crear la empresa
                $company = Company::create([
                    'type' => CompanyTypeEnum::from($this->companyType),
                    'ruc' => $data['ruc'],
                    'business_name' => $data['business_name'],
                    'status' => CompanyStatusEnum::PENDIENTE,
                    'is_active' => false,
                ]);

                // Crear el usuario representante
                User::create([
                    'company_id' => $company->id,
                    'dni' => $data['representative_dni'],
                    'name' => $data['representative_name'],
                    'lastname' => $data['representative_last_name'],
                    'email' => $data['representative_email'],
                    'password' => Hash::make($data['representative_password']),
                    'is_company_representative' => true,
                ]);

                // Guardar documento RUC
                $this->saveDocument(
                    $company,
                    CompanyDocumentTypeEnum::RUC_RECORD,
                    $data['ruc_document']
                );

                // Guardar documento DNI del representante
                $this->saveDocument(
                    $company,
                    CompanyDocumentTypeEnum::REPRESENTATIVE_DNI,
                    $data['representative_dni_document']
                );

                // Guardar documentos adicionales para empresas jurídicas
                if ($this->companyType === CompanyTypeEnum::JURIDICA->value) {
                    if (isset($data['sunarp_document'])) {
                        $this->saveDocument(
                            $company,
                            CompanyDocumentTypeEnum::SUNARP_RECORD,
                            $data['sunarp_document']
                        );
                    }

                    if (isset($data['power_of_attorney_document'])) {
                        $this->saveDocument(
                            $company,
                            CompanyDocumentTypeEnum::POWER_OF_ATTORNEY_VALIDITY,
                            $data['power_of_attorney_document']
                        );
                    }
                }
            });

            $this->dispatch('company-created');

            // Notificación de éxito
            Notification::make()
                ->title('Empresa registrada exitosamente')
                ->body('Su solicitud está pendiente de aprobación.')
                ->success()
                ->send();

            // Redireccionar al login
            $this->redirect(route('login'), navigate: true);

        } catch (Exception $e) {
            Log::alert('Error al registrar la empresa: ' . $e->getMessage());
            Notification::make()
                ->title('Error al registrar la empresa')
                ->body('Ha ocurrido un error inesperado. Por favor, contacte al soporte.')
                ->danger()
                ->send();
        }
    }
    #[On('select-company-type')]
    public function onSelectCompanyType(int $type): void
    {
        $this->companyType = $type;
    }

    public function render()
    {
        return view('livewire.company.create-company');
    }

    protected function saveDocument(Company $company, CompanyDocumentTypeEnum $type, string $filePath): void
    {
        // El archivo ya está almacenado por Filament, solo necesitamos crear el registro
        CompanyDocument::create([
            'company_id' => $company->id,
            'type' => $type,
            'path' => $filePath,
            'status' => CompanyDocumentStatusEnum::PENDIENTE,
            'submitted_date' => now(),
        ]);
    }
}
