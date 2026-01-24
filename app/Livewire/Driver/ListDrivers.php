<?php

declare(strict_types=1);

namespace App\Livewire\Driver;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Enums\EntityStatusEnum;
use App\Models\Document;
use App\Models\Driver;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class ListDrivers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions, InteractsWithSchemas, InteractsWithTable;

    protected $listeners = ['driver-created' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(Driver::query()->where('company_id', Auth::user()->company_id))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->searchable(query: function ($query, string $search): void {
                        $query->where(function ($query) use ($search): void {
                            $query->whereRaw('lower(name) like ?', ['%'.mb_strtolower($search).'%'])
                                ->orWhereRaw('lower(lastname) like ?', ['%'.mb_strtolower($search).'%'])
                                ->orWhereRaw("lower(concat(name, ' ', lastname)) like ?", ['%'.mb_strtolower($search).'%']);
                        });
                    }),
                TextColumn::make('document_number')
                    ->label('Número de Documento')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('license_number')
                    ->label('Número de Licencia')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado En')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(EntityStatusEnum::class),
                SelectFilter::make('document_type')
                    ->label('Tipo de Documento')
                    ->options(\App\Enums\DriverDocumentTypeEnum::class),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('update_documents')
                        ->label('Actualizar Documentos')
                        ->icon('heroicon-o-document-check')
                        ->color('info')
                        ->visible(fn (Driver $record): bool => $record->documents()->exists())
                        ->schema(function (Driver $record): array {
                            $documents = $record->documents()->get();
                            $components = [];

                            foreach ($documents as $document) {
                                $isExpired = $document->expiration_date && $document->expiration_date < now();
                                $isRejected = $document->status === DocumentStatusEnum::REJECTED;
                                $isNeedsUpdate = $document->status === DocumentStatusEnum::NEEDS_UPDATE;
                                $isExpiringSoon = $document->status === DocumentStatusEnum::EXPIRING_SOON;
                                $isRequired = $isExpired || $isRejected || $isNeedsUpdate;

                                // Determinar descripción del estado
                                if ($isRejected) {
                                    $description = "❌ Rechazado - Motivo: {$document->rejection_reason}";
                                } elseif ($isNeedsUpdate) {
                                    $description = "❌ Vencido el: {$document->expiration_date->format('d/m/Y')}";
                                } elseif ($isExpired) {
                                    $description = "❌ Vencido el: {$document->expiration_date->format('d/m/Y')}";
                                } elseif ($isExpiringSoon) {
                                    $description = "⚠️ Por vencer el: {$document->expiration_date->format('d/m/Y')} (opcional)";
                                } elseif ($document->status === DocumentStatusEnum::PENDING) {
                                    $description = '⏳ Pendiente de aprobación';
                                } else {
                                    $expirationText = $document->expiration_date ? " - Vence: {$document->expiration_date->format('d/m/Y')}" : '';
                                    $description = "✓ Vigente{$expirationText} (opcional)";
                                }

                                $formSchema = [
                                    FileUpload::make("document_{$document->id}")
                                        ->label('Cargar nuevo documento')
                                        ->required($isRequired)
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'])
                                        ->maxSize(5120)
                                        ->directory(fn () => "EMPRESAS/{$record->company->ruc}/DRIVERS/{$record->document_number}")
                                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) use ($document): string {
                                            $extension = $file->getClientOriginalExtension();

                                            return $document->type->getFileName().'.'.$extension;
                                        })
                                        ->helperText($isRequired ? 'Obligatorio. Formatos: PDF, JPG, PNG (máx. 5MB)' : 'Opcional. Formatos: PDF, JPG, PNG (máx. 5MB)'),
                                ];

                                // Agregar campo de fecha según el tipo de documento
                                if ($document->type->requiresCourseDate()) {
                                    $validityYears = $document->type->getValidityYears();
                                    $formSchema[] = DatePicker::make("course_date_{$document->id}")
                                        ->label('Fecha del Curso')
                                        ->helperText("Vigencia: {$validityYears} años")
                                        ->required(fn (callable $get): bool => ! empty($get("document_{$document->id}")))
                                        ->native(false)
                                        ->maxDate(now())
                                        ->closeOnDateSelection()
                                        ->displayFormat('d/m/Y');
                                } elseif ($document->expiration_date) {
                                    $formSchema[] = DatePicker::make("expiration_date_{$document->id}")
                                        ->label('Nueva fecha de vencimiento')
                                        ->required(fn (callable $get): bool => ! empty($get("document_{$document->id}")))
                                        ->native(false)
                                        ->minDate(now()->addDay())
                                        ->closeOnDateSelection()
                                        ->displayFormat('d/m/Y')
                                        ->helperText('Requerido si sube un nuevo documento');
                                }

                                $components[] = Section::make($document->type->getLabel())
                                    ->description($description)
                                    ->schema($formSchema)
                                    ->collapsible()
                                    ->collapsed(! $isRequired)
                                    ->icon($isRequired ? 'heroicon-o-exclamation-circle' : 'heroicon-o-document-text');
                            }

                            return $components;
                        })
                        ->modalHeading('Actualizar Documentos del Conductor')
                        ->modalDescription('Actualiza los documentos del conductor. Los marcados como obligatorios deben ser actualizados.')
                        ->modalSubmitActionLabel('Guardar Cambios')
                        ->modalWidth('2xl')
                        ->action(function (Driver $record, array $data): void {
                            try {
                                DB::transaction(function () use ($record, $data) {
                                    $documents = $record->documents()->get();
                                    $hasUpdates = false;

                                    foreach ($documents as $document) {
                                        $fieldName = "document_{$document->id}";

                                        if (isset($data[$fieldName]) && ! empty($data[$fieldName])) {
                                            $newPath = is_array($data[$fieldName]) ? $data[$fieldName][0] : $data[$fieldName];

                                            // Eliminar archivo anterior si la extensión cambió
                                            $oldExtension = pathinfo((string) $document->path, PATHINFO_EXTENSION);
                                            $newExtension = pathinfo($newPath, PATHINFO_EXTENSION);
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
                                                $courseDateField = "course_date_{$document->id}";
                                                if (isset($data[$courseDateField]) && ! empty($data[$courseDateField])) {
                                                    $courseDate = Carbon::parse($data[$courseDateField]);
                                                    $updateData['course_date'] = $courseDate;
                                                    $updateData['expiration_date'] = $courseDate->copy()->addYears($document->type->getValidityYears());
                                                }
                                            } else {
                                                $expirationField = "expiration_date_{$document->id}";
                                                if (isset($data[$expirationField]) && ! empty($data[$expirationField])) {
                                                    $updateData['expiration_date'] = $data[$expirationField];
                                                }
                                            }

                                            $document->update($updateData);
                                            $hasUpdates = true;
                                        }
                                    }

                                    if ($hasUpdates) {
                                        $record->update([
                                            'status' => EntityStatusEnum::PENDING_APPROVAL,
                                        ]);
                                    }
                                });

                                Notification::make()
                                    ->title('Documentos actualizados exitosamente')
                                    ->body('Los documentos han sido actualizados. El conductor está pendiente de aprobación.')
                                    ->success()
                                    ->send();

                                $this->dispatch('$refresh');
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error al actualizar documentos')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('add_mercancias_course')
                        ->label('Agregar Curso Mercancías')
                        ->icon('heroicon-o-document-plus')
                        ->color('success')
                        ->visible(fn (Driver $record): bool => in_array($record->status, [EntityStatusEnum::ACTIVE, EntityStatusEnum::PENDING_APPROVAL]) &&
                            ! $record->documents()->where('type', DocumentTypeEnum::CURSO_MERCANCIAS)->exists()
                        )
                        ->schema([
                            Grid::make(1)
                                ->schema([
                                    FileUpload::make('mercancias_document')
                                        ->label('Documento de Curso Mercancías Peligrosas')
                                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                                        ->maxSize(5120)
                                        ->required()
                                        ->directory(fn (Driver $record) => 'EMPRESAS/'.Auth::user()->company->ruc."/DRIVERS/{$record->document_number}")
                                        ->helperText('Sube el documento del curso en formato PDF o imagen (máx. 5MB)'),

                                    DatePicker::make('mercancias_course_date')
                                        ->label('Fecha de Inducción/Curso')
                                        ->native(false)
                                        ->required()
                                        ->closeOnDateSelection()
                                        ->displayFormat('d/m/Y')
                                        ->helperText('Selecciona la fecha de realización del curso'),
                                ]),
                        ])
                        ->modalHeading('Agregar Curso Mercancías Peligrosas')
                        ->modalDescription('Completa los datos del curso de mercancías peligrosas para este conductor.')
                        ->modalSubmitActionLabel('Agregar Curso')
                        ->action(function (Driver $record, array $data): void {
                            try {
                                DB::transaction(function () use ($record, $data) {
                                    $courseDate = Carbon::parse($data['mercancias_course_date']);
                                    $expirationDate = $courseDate->addYears(2); // 2 años para CURSO_MERCANCIAS

                                    // Crear el documento del curso
                                    Document::create([
                                        'documentable_type' => Driver::class,
                                        'documentable_id' => $record->id,
                                        'type' => DocumentTypeEnum::CURSO_MERCANCIAS,
                                        'path' => $data['mercancias_document'],
                                        'submitted_date' => now(),
                                        'expiration_date' => $expirationDate,
                                        'status' => DocumentStatusEnum::PENDING,
                                    ]);

                                    // Actualizar el conductor
                                    $record->update([
                                        'status' => EntityStatusEnum::PENDING_APPROVAL,
                                    ]);
                                });

                                Notification::make()
                                    ->title('Curso agregado exitosamente')
                                    ->body('El documento del curso mercancías ha sido agregado y el conductor está pendiente de aprobación.')
                                    ->success()
                                    ->send();

                                $this->dispatch('$refresh');
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error al agregar el curso')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public function render(): View
    {
        return view('livewire.driver.list-drivers');
    }
}
