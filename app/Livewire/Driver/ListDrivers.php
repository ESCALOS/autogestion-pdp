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
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
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
                            $query->whereRaw('lower(name) like ?', ['%' . strtolower($search) . '%'])
                                ->orWhereRaw('lower(lastname) like ?', ['%' . strtolower($search) . '%'])
                                ->orWhereRaw("lower(concat(name, ' ', lastname)) like ?", ['%' . strtolower($search) . '%']);
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
                    ->options(\App\Enums\EntityStatusEnum::class),
                SelectFilter::make('document_type')
                    ->label('Tipo de Documento')
                    ->options(\App\Enums\DriverDocumentTypeEnum::class),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('edit_document')
                        ->label('Editar Documento')
                        ->icon('heroicon-o-document-check')
                        ->color('info')
                        ->visible(function (Driver $record): bool {
                            return $record->documents()
                                ->where(function ($query): void {
                                    $query->whereNotNull('expiration_date')
                                        ->orWhereNotNull('course_date');
                                })
                                ->exists();
                        })
                        ->schema([
                            Grid::make(1)
                                ->schema([
                                    Select::make('document_id')
                                        ->label('Documento')
                                        ->options(function (Driver $record): array {
                                            return $record->documents()
                                                ->where(function ($query): void {
                                                    $query->whereNotNull('expiration_date')
                                                        ->orWhereNotNull('course_date');
                                                })
                                                ->get()
                                                ->mapWithKeys(function (Document $document) {
                                                    // Calcular fecha de vencimiento
                                                    if ($document->expiration_date) {
                                                        $expirationDate = $document->expiration_date;
                                                    } elseif ($document->course_date && $document->type->getValidityYears()) {
                                                        $expirationDate = $document->course_date->addYears($document->type->getValidityYears());
                                                    } else {
                                                        return [];
                                                    }

                                                    $daysUntilExpiration = now()->diffInDays($expirationDate, false);

                                                    // Determinar estado basado en status del documento y fecha
                                                    $status = match ($document->status) {
                                                        DocumentStatusEnum::REJECTED => '❌ Rechazado',
                                                        DocumentStatusEnum::NEEDS_UPDATE => '❌ Vencido',
                                                        DocumentStatusEnum::PENDING => '⏳ Pendiente',
                                                        DocumentStatusEnum::APPROVED => match (true) {
                                                            $daysUntilExpiration < 0 => '❌ Vencido',
                                                            $daysUntilExpiration <= 15 => '⚠️ Próximo a vencer',
                                                            default => '✓ Vigente',
                                                        },
                                                        default => '❓ Desconocido',
                                                    };

                                                    $label = "{$document->type->getLabel()} - Vence: {$expirationDate->format('d/m/Y')} ({$status})";

                                                    return [$document->id => $label];
                                                })
                                                ->toArray();
                                        })
                                        ->required()
                                        ->native(false)
                                        ->searchable()
                                        ->live(),
                                    FileUpload::make('document_file')
                                        ->label('Nuevo Documento')
                                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                                        ->maxSize(5120)
                                        ->required()
                                        ->directory(fn (Driver $record) => 'EMPRESAS/'.Auth::user()->company->ruc."/DRIVERS/{$record->document_number}")
                                        ->helperText('Sube el nuevo documento en formato PDF o imagen (máx. 5MB)'),
                                    DatePicker::make('course_date')
                                        ->label('Fecha de Inducción/Curso')
                                        ->native(false)
                                        ->required()
                                        ->closeOnDateSelection()
                                        ->displayFormat('d/m/Y')
                                        ->helperText('Selecciona la fecha de realización del curso o inducción')
                                        ->hidden(function ($get, Driver $record): bool {
                                            $documentId = $get('document_id');
                                            if (!$documentId) {
                                                return true;
                                            }
                                            $document = Document::find($documentId);

                                            return !($document && $document->type->getValidityYears());
                                        }),
                                    DatePicker::make('expiration_date')
                                        ->label('Fecha de Vencimiento')
                                        ->native(false)
                                        ->required()
                                        ->minDate(today())
                                        ->closeOnDateSelection()
                                        ->displayFormat('d/m/Y')
                                        ->helperText('Selecciona la nueva fecha de vencimiento del documento')
                                        ->hidden(function ($get, Driver $record): bool {
                                            $documentId = $get('document_id');
                                            if (!$documentId) {
                                                return false;
                                            }
                                            $document = Document::find($documentId);

                                            return $document && $document->type->getValidityYears();
                                        }),
                                ]),
                        ])
                        ->modalHeading('Editar Documento')
                        ->modalDescription('Actualiza el documento y su fecha de vencimiento.')
                        ->modalSubmitActionLabel('Guardar Cambios')
                        ->action(function (Driver $record, array $data): void {
                            try {
                                DB::transaction(function () use ($record, $data) {
                                    $document = Document::find($data['document_id']);

                                    if ($document) {
                                        $updateData = [
                                            'path' => $data['document_file'],
                                            'submitted_date' => now(),
                                            'status' => DocumentStatusEnum::PENDING,
                                        ];

                                        // Si el documento tiene validez calculable, calcular y guardar como expiration_date
                                        if ($document->type->getValidityYears()) {
                                            $courseDate = Carbon::parse($data['course_date']);
                                            $updateData['expiration_date'] = $courseDate->addYears($document->type->getValidityYears());
                                            $updateData['course_date'] = null;
                                        } else {
                                            // Si no, guardar directamente como expiration_date
                                            $updateData['expiration_date'] = $data['expiration_date'];
                                            $updateData['course_date'] = null;
                                        }

                                        $document->update($updateData);

                                        // Cambiar estado del driver a Pendiente de Aprobación
                                        $record->update([
                                            'status' => EntityStatusEnum::PENDING_APPROVAL,
                                        ]);
                                    }
                                });

                                Notification::make()
                                    ->title('Documento actualizado exitosamente')
                                    ->body('El documento y la fecha han sido actualizados. El conductor está pendiente de aprobación.')
                                    ->success()
                                    ->send();

                                $this->dispatch('$refresh');
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error al actualizar el documento')
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
            ])
            ->poll('60s');
    }

    public function render(): View
    {
        return view('livewire.driver.list-drivers');
    }
}
