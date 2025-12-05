<x-filament-panels::page>
    @vite(['resources/css/validation-documents.css'])

    <div
        class="validation-container"
        x-data="{
            documentStatuses: $wire.documentStatuses,
            rejectionReasons: $wire.rejectionReasons,
            approveDocument(id) {
                this.documentStatuses[id] = 2
                this.rejectionReasons[id] = ''
            },
            rejectDocument(id) {
                this.documentStatuses[id] = 3
            },
            resetDocument(id) {
                this.documentStatuses[id] = 1
                this.rejectionReasons[id] = ''
            },
            canApproveAll() {
                return Object.values(this.documentStatuses).every(
                    (status) => status === 2,
                )
            },
            saveValidation() {
                $wire.set('documentStatuses', this.documentStatuses)
                $wire.set('rejectionReasons', this.rejectionReasons)
                $wire.saveValidation()
            },
        }"
    >
        {{-- Información del Vehículo --}}
        <div class="info-card">
            <h2 class="info-card-title">Información del Vehículo</h2>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Placa:</span>
                    <span class="info-value">{{ $record->license_plate }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Nacionalidad:</span>
                    <span class="info-value">{{ $record->nationality ?? 'N/A' }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Tipo de Camión:</span>
                    <span class="info-value">{{ $record->truck_type ?? 'N/A' }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Tara:</span>
                    <span class="info-value">{{ $record->tare ? number_format($record->tare, 3) : 'N/A' }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">¿Es Interno?:</span>
                    <x-filament::badge :color="$record->is_internal ? 'success' : 'gray'">
                        {{ $record->is_internal ? 'Sí' : 'No' }}
                    </x-filament::badge>
                </div>

                <div class="info-item">
                    <span class="info-label">¿Tiene Bonificación?:</span>
                    <x-filament::badge :color="$record->has_bonus ? 'success' : 'gray'">
                        {{ $record->has_bonus ? 'Sí' : 'No' }}
                    </x-filament::badge>
                </div>

                <div class="info-item">
                    <span class="info-label">Empresa:</span>
                    <span class="info-value">{{ $record->company->business_name ?? 'N/A' }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Estado:</span>
                    <x-filament::badge :color="$record->status->getColor()">
                        {{ $record->status->getLabel() }}
                    </x-filament::badge>
                </div>

                <div class="info-item">
                    <span class="info-label">Fecha de Registro:</span>
                    <span class="info-value">{{ $record->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Documentos para Validar --}}
        <div class="info-card">
            <h2 class="info-card-title">Documentos para Validar</h2>

            @if ($record->documents->isEmpty())
                <div class="rejection-reason-container">
                    <x-filament::badge color="warning">No hay documentos subidos para este vehículo</x-filament::badge>
                </div>
            @else
                <div class="documents-container">
                    @foreach ($this->getRequiredDocumentTypes() as $documentType)
                        @php
                            $document = $record->documents->firstWhere('type', $documentType);
                        @endphp

                        @if ($document)
                            <div
                                class="document-card"
                                x-bind:class="{
                                    'approved': documentStatuses[{{ $document->id }}] === 2,
                                    'rejected': documentStatuses[{{ $document->id }}] === 3,
                                }"
                            >
                                <div class="document-header">
                                    <x-filament::icon
                                        :icon="$document->type->getIcon()"
                                        class="document-icon"
                                        :style="'color: ' . match($document->type->getColor()) {
                                            'primary' => 'rgb(59, 130, 246)',
                                            'secondary' => 'rgb(107, 114, 128)',
                                            'tertiary' => 'rgb(139, 92, 246)',
                                            'info' => 'rgb(6, 182, 212)',
                                            default => 'rgb(107, 114, 128)'
                                        }"
                                    />
                                    <h3 class="document-title">{{ $document->type->getLabel() }}</h3>
                                </div>

                                <div class="document-info">
                                    <div class="status-row">
                                        <span class="status-label">Estado actual:</span>
                                        <template x-if="documentStatuses[{{ $document->id }}] === 1">
                                            <x-filament::badge color="warning">Pendiente</x-filament::badge>
                                        </template>
                                        <template x-if="documentStatuses[{{ $document->id }}] === 2">
                                            <x-filament::badge color="success">Aprobado</x-filament::badge>
                                        </template>
                                        <template x-if="documentStatuses[{{ $document->id }}] === 3">
                                            <x-filament::badge color="danger">Rechazado</x-filament::badge>
                                        </template>
                                    </div>

                                    @if ($document->submitted_date)
                                        <p class="document-date">
                                            Fecha de envío: {{ $document->submitted_date->format('d/m/Y') }}
                                        </p>
                                    @endif

                                    @if ($document->expiration_date)
                                        <p class="document-date">
                                            Fecha de vencimiento: {{ $document->expiration_date->format('d/m/Y') }}
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
                                        x-bind:disabled="documentStatuses[{{ $document->id }}] === 2"
                                        size="sm"
                                        color="success"
                                    >
                                        <x-filament::icon icon="heroicon-o-check-circle" class="mr-1 h-4 w-4" />
                                        Aprobar
                                    </x-filament::button>

                                    <x-filament::button
                                        x-on:click="rejectDocument({{ $document->id }})"
                                        x-bind:disabled="documentStatuses[{{ $document->id }}] === 3"
                                        size="sm"
                                        color="danger"
                                    >
                                        <x-filament::icon icon="heroicon-o-x-circle" class="mr-1 h-4 w-4" />
                                        Rechazar
                                    </x-filament::button>

                                    <x-filament::button
                                        x-show="documentStatuses[{{ $document->id }}] !== 1"
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
                                    x-show="documentStatuses[{{ $document->id }}] === 3"
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
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Botones de acción --}}
        <div class="actions-footer">
            <x-filament::button x-on:click="saveValidation()" color="primary" size="lg" wire:loading.attr="disabled">
                <x-filament::icon icon="heroicon-o-check" class="mr-2 h-5 w-5" wire:loading.remove />
                <x-filament::loading-indicator class="mr-2 h-5 w-5" wire:loading />
                <span x-text="canApproveAll() ? 'Aprobar Vehículo' : 'Guardar Validación'"></span>
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
                    $documentUrl = route('entity.document.view', $selectedDocument->id);
                @endphp

                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <img src="{{ $documentUrl }}" alt="Documento" class="modal-image" />
                @elseif ($extension === 'pdf')
                    <iframe src="{{ $documentUrl }}" class="modal-iframe"></iframe>
                @else
                    <div class="not-supported-container">
                        <div class="not-supported-content">
                            <x-filament::icon icon="heroicon-o-document" class="not-supported-icon" />
                            <p class="not-supported-text">Formato no soportado para vista previa</p>
                            <a href="{{ $documentUrl }}" target="_blank" class="download-link">Descargar documento</a>
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
