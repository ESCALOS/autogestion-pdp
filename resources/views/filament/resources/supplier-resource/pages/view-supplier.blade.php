@php
    use App\Enums\DocumentStatusEnum;
@endphp

<x-filament-panels::page>
    @vite(['resources/css/validation-documents.css'])

    <div
        class="validation-container"
        x-data="{
            documentStatuses: $wire.documentStatuses,
            rejectionReasons: $wire.rejectionReasons,
            approveDocument(id) {
                this.documentStatuses[id] = {{ DocumentStatusEnum::APPROVED->value }}
                this.rejectionReasons[id] = ''
            },
            rejectDocument(id) {
                this.documentStatuses[id] = {{ DocumentStatusEnum::REJECTED->value }}
            },
            resetDocument(id) {
                this.documentStatuses[id] = {{ DocumentStatusEnum::PENDING->value }}
                this.rejectionReasons[id] = ''
            },
            canApproveAll() {
                return Object.values(this.documentStatuses).every(
                    (status) => status === {{ DocumentStatusEnum::APPROVED->value }},
                )
            },
            saveValidation() {
                $wire.set('documentStatuses', this.documentStatuses)
                $wire.set('rejectionReasons', this.rejectionReasons)
                $wire.saveValidation()
            },
        }"
    >
        {{-- Información del Proveedor --}}
        <div class="info-card">
            <h2 class="info-card-title">Información del Proveedor</h2>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">RUC:</span>
                    <span class="info-value">{{ $this->record->ruc }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Razón Social:</span>
                    <span class="info-value">{{ $this->record->business_name }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Tipo:</span>
                    <span class="info-value">{{ $this->record->type->getLabel() }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Representante:</span>
                    <span class="info-value">{{ $this->record->representative?->name }} {{ $this->record->representative?->lastname }}</span>
                </div>

                @if ($this->record->representative?->email)
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $this->record->representative->email }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Documentos para Validar --}}
        <div class="info-card">
            <h2 class="info-card-title">Documentos para Validar</h2>

            <div class="documents-container">
                @foreach ($this->getRequiredDocumentTypes() as $requiredType)
                    @php
                        $document = $this->record->documents->firstWhere('type', $requiredType);
                    @endphp

                    <div
                        class="document-card"
                        x-bind:class="{
                            'approved':
                                {{ $document ? "documentStatuses[{$document->id}] === " . DocumentStatusEnum::APPROVED->value : 'false' }},
                            'rejected':
                                {{ $document ? "documentStatuses[{$document->id}] === " . DocumentStatusEnum::REJECTED->value : 'false' }},
                        }"
                    >
                        <div class="document-header">
                            <x-filament::icon
                                :icon="$requiredType->getIcon()"
                                class="document-icon"
                                :style="'color: ' . match($requiredType->getColor()) {
                                    'primary' => 'rgb(59, 130, 246)',
                                    'secondary' => 'rgb(107, 114, 128)',
                                    'tertiary' => 'rgb(139, 92, 246)',
                                    'info' => 'rgb(6, 182, 212)',
                                    default => 'rgb(107, 114, 128)'
                                }"
                            />
                            <h3 class="document-title">{{ $requiredType->getLabel() }}</h3>
                        </div>

                        @if ($document)
                            <div class="document-info">
                                <div class="status-row">
                                    <span class="status-label">Estado actual:</span>
                                    <template x-if="documentStatuses[{{ $document->id }}] === {{ DocumentStatusEnum::PENDING->value }}">
                                        <x-filament::badge color="warning">Pendiente</x-filament::badge>
                                    </template>
                                    <template x-if="documentStatuses[{{ $document->id }}] === {{ DocumentStatusEnum::APPROVED->value }}">
                                        <x-filament::badge color="success">Aprobado</x-filament::badge>
                                    </template>
                                    <template x-if="documentStatuses[{{ $document->id }}] === {{ DocumentStatusEnum::REJECTED->value }}">
                                        <x-filament::badge color="danger">Rechazado</x-filament::badge>
                                    </template>
                                </div>

                                @if ($document->submitted_date)
                                    <p class="document-date">
                                        Fecha de envío: {{ $document->submitted_date->format('d/m/Y') }}
                                    </p>
                                @endif

                                @if ($document->validated_by && $document->validated_date)
                                    <p class="document-date">
                                        Validado por: {{ $document->validator->name ?? 'N/A' }} -
                                        {{ $document->validated_date->format('d/m/Y') }}
                                    </p>
                                @endif
                            </div>

                            {{-- Acciones del documento --}}
                            <div class="document-actions">
                                <x-filament::button
                                    wire:click="openDocument({{ $document->id }})"
                                    size="sm"
                                    color="info"
                                >
                                    <x-filament::icon icon="heroicon-o-eye" class="mr-1 h-4 w-4" />
                                    Ver Documento
                                </x-filament::button>

                                <x-filament::button
                                    x-on:click="approveDocument({{ $document->id }})"
                                    x-bind:disabled="documentStatuses[{{ $document->id }}] === {{ DocumentStatusEnum::APPROVED->value }}"
                                    size="sm"
                                    color="success"
                                >
                                    <x-filament::icon icon="heroicon-o-check-circle" class="mr-1 h-4 w-4" />
                                    Aprobar
                                </x-filament::button>

                                <x-filament::button
                                    x-on:click="rejectDocument({{ $document->id }})"
                                    x-bind:disabled="documentStatuses[{{ $document->id }}] === {{ DocumentStatusEnum::REJECTED->value }}"
                                    size="sm"
                                    color="danger"
                                >
                                    <x-filament::icon icon="heroicon-o-x-circle" class="mr-1 h-4 w-4" />
                                    Rechazar
                                </x-filament::button>

                                <x-filament::button
                                    x-show="documentStatuses[{{ $document->id }}] !== {{ DocumentStatusEnum::PENDING->value }}"
                                    x-on:click="resetDocument({{ $document->id }})"
                                    size="sm"
                                    color="gray"
                                >
                                    <x-filament::icon icon="heroicon-o-arrow-path" class="mr-1 h-4 w-4" />
                                    Restablecer
                                </x-filament::button>
                            </div>

                            {{-- Razón de rechazo --}}
                            <div
                                x-show="documentStatuses[{{ $document->id }}] === {{ DocumentStatusEnum::REJECTED->value }}"
                                class="rejection-reason-container"
                            >
                                <label class="rejection-label">Razón del Rechazo *</label>
                                <textarea
                                    x-model="rejectionReasons[{{ $document->id }}]"
                                    rows="3"
                                    class="rejection-textarea"
                                    placeholder="Ingrese la razón del rechazo..."
                                ></textarea>
                            </div>
                        @else
                            <div class="rejection-reason-container">
                                <x-filament::badge color="warning">Documento no subido</x-filament::badge>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="actions-footer">
            <x-filament::button x-on:click="saveValidation()" color="primary" size="lg" wire:loading.attr="disabled">
                <x-filament::icon icon="heroicon-o-check" class="mr-2 h-5 w-5" wire:loading.remove />
                <x-filament::loading-indicator class="h-5 w-5" wire:loading />
                <span x-text="canApproveAll() ? 'Aprobar Proveedor' : 'Guardar Validación'"></span>
            </x-filament::button>
        </div>
    </div>

    {{-- Modal para ver documentos --}}
    <x-filament::modal id="document-modal" width="7xl">
        <x-slot name="heading">Visualización de Documento</x-slot>

        @php
            $selectedDocument = $this->getSelectedDocument();
        @endphp

        @if ($selectedDocument)
            <div class="modal-content">
                @php
                    $extension = strtolower(pathinfo($selectedDocument->path, PATHINFO_EXTENSION));
                @endphp

                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <img src="{{ asset('storage/' . $selectedDocument->path) }}" alt="Documento" class="modal-image" />
                @elseif ($extension === 'pdf')
                    <iframe src="{{ asset('storage/' . $selectedDocument->path) }}" class="modal-iframe"></iframe>
                @else
                    <div class="not-supported-container">
                        <div class="not-supported-content">
                            <x-filament::icon icon="heroicon-o-document" class="not-supported-icon" />
                            <p class="not-supported-text">Formato no soportado para vista previa</p>
                            <a href="{{ asset('storage/' . $selectedDocument->path) }}" target="_blank" class="download-link">Descargar documento</a>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </x-filament::modal>

    @script
        <script>
            $wire.on('open-document-modal', () => {
                $wire.dispatch('open-modal', { id: 'document-modal' })
            })
        </script>
    @endscript
</x-filament-panels::page>
