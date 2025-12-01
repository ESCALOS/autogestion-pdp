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
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class ListTrucks extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions, InteractsWithSchemas, InteractsWithTable;

    protected $listeners = ['truck-created' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(Truck::query()->where('company_id', Auth::user()->company_id))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('license_plate')
                    ->label('Placa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('truck_type')
                    ->label('Tipo')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nationality')
                    ->label('Nacionalidad')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tare')
                    ->label('Tara (Ton)')
                    ->numeric(decimalPlaces: 3)
                    ->sortable(),
                TextColumn::make('is_internal')
                    ->label('Interno')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('has_bonus')
                    ->label('Bonificación')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado En')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(EntityStatusEnum::class),
                SelectFilter::make('truck_type')
                    ->label('Tipo')
                    ->searchable()
                    ->preload()
                    ->options(TruckTypeEnum::class),
                SelectFilter::make('nationality')
                    ->label('Nacionalidad')
                    ->options(function () {
                        return Truck::query()
                            ->where('company_id', Auth::user()->company_id)
                            ->distinct()
                            ->pluck('nationality', 'nationality')
                            ->toArray();
                    }),
                TernaryFilter::make('is_internal')
                    ->label('Interno')
                    ->placeholder('Todos')
                    ->trueLabel('Solo Internos')
                    ->falseLabel('Solo Externos'),
                TernaryFilter::make('has_bonus')
                    ->label('Bonificación')
                    ->placeholder('Todos')
                    ->trueLabel('Con Bonificación')
                    ->falseLabel('Sin Bonificación'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('add_bonus')
                        ->label('Agregar Bonificación')
                        ->icon('heroicon-o-document-plus')
                        ->color('success')
                        ->visible(fn (Truck $record): bool => in_array($record->status, [EntityStatusEnum::ACTIVE, EntityStatusEnum::PENDING_APPROVAL]) &&
                            ! $record->documents()->where('type', DocumentTypeEnum::BONIFICACION)->exists()
                        )
                        ->form([
                            Grid::make(1)
                                ->schema([
                                    FileUpload::make('bonus_document')
                                        ->label('Documento de Bonificación')
                                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                                        ->maxSize(5120)
                                        ->required()
                                        ->directory(fn (Truck $record) => 'EMPRESAS/'.Auth::user()->company->ruc."/TRUCKS/{$record->license_plate}")
                                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                            $extension = $file->getClientOriginalExtension();

                                            return DocumentTypeEnum::BONIFICACION->getFileName().'.'.$extension;
                                        })
                                        ->helperText('Sube el documento de bonificación en formato PDF o imagen (máx. 5MB)'),

                                    DatePicker::make('bonus_expiration_date')
                                        ->label('Fecha de Vencimiento')
                                        ->native(false)
                                        ->required()
                                        ->minDate(today())
                                        ->closeOnDateSelection()
                                        ->displayFormat('d/m/Y')
                                        ->helperText('Selecciona la fecha de vencimiento del documento'),
                                ]),
                        ])
                        ->modalHeading('Agregar Documento de Bonificación')
                        ->modalDescription('Completa los datos del documento de bonificación para este tracto.')
                        ->modalSubmitActionLabel('Agregar Bonificación')
                        ->action(function (Truck $record, array $data): void {
                            try {
                                DB::transaction(function () use ($record, $data) {
                                    // Crear el documento de bonificación
                                    Document::create([
                                        'documentable_type' => Truck::class,
                                        'documentable_id' => $record->id,
                                        'type' => DocumentTypeEnum::BONIFICACION,
                                        'path' => $data['bonus_document'],
                                        'submitted_date' => now(),
                                        'expiration_date' => $data['bonus_expiration_date'],
                                        'status' => DocumentStatusEnum::PENDING,
                                    ]);

                                    // Actualizar el tracto
                                    $record->update([
                                        'has_bonus' => true,
                                        'status' => EntityStatusEnum::PENDING_APPROVAL,
                                    ]);
                                });

                                Notification::make()
                                    ->title('Bonificación agregada exitosamente')
                                    ->body('El documento de bonificación ha sido agregado y el tracto está pendiente de aprobación.')
                                    ->success()
                                    ->send();

                                $this->dispatch('$refresh');
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error al agregar la bonificación')
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
            ->poll('5s');
    }

    public function render(): View
    {
        return view('livewire.truck.list-trucks');
    }
}
