<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/validation-documents.css') }}">

    <div class="validation-container">
        {{-- Información del Conductor --}}
        <div class="info-card">
            <h2 class="info-card-title">Información del Conductor</h2>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Nombre Completo:</span>
                    <span class="info-value">{{ $record->full_name }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Tipo de Documento:</span>
                    <span class="info-value">{{ $record->document_type->getLabel() }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Número de Documento:</span>
                    <span class="info-value">{{ $record->document_number }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Empresa:</span>
                    <span class="info-value">{{ $record->company->business_name ?? 'N/A' }}</span>
                </div>

                @if($record->email)
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $record->email }}</span>
                </div>
                @endif

                @if($record->phone)
                <div class="info-item">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value">{{ $record->phone }}</span>
                </div>
                @endif

                <div class="info-item">
                    <span class="info-label">Estado:</span>
                    <x-filament::badge :color="$record->status->getColor()">
                        {{ $record->status->getLabel() }}
                    </x-filament::badge>
                </div>
            </div>
        </div>

        {{-- Documentos para Validar --}}
        <div class="info-card">
            <h2 class="info-card-title">Documentos para Validar</h2>

            @if($record->documents->isEmpty())
                <div class="rejection-reason-container">
                    <x-filament::badge color="warning">
                        No hay documentos subidos para este conductor
                    </x-filament::badge>
                </div>
            @else
                <div class="documents-container">
                    @foreach($this->getRequiredDocumentTypes() as $documentType)
                    @php
                        $document = $record->documents->firstWhere('type', $documentType);
                        $currentStatus = $document ? ($documentStatuses[$document->id] ?? $document->status) : 1;
                    @endphp

                    @if($document)
                    <div wire:key="document-{{ $document->id }}" class="document-card {{ $currentStatus == 2 ? 'approved' : ($currentStatus == 3 ? 'rejected' : '') }}">
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
                                <x-filament::badge :color="match($currentStatus) {
                                    2 => 'success',
                                    3 => 'danger',
                                    default => 'warning'
                                }">
                                    {{ match($currentStatus) {
                                        2 => 'Aprobado',
                                        3 => 'Rechazado',
                                        default => 'Pendiente'
                                    } }}
                                </x-filament::badge>
                            </div>                                @if($document->submitted_date)
                                    <p class="document-date">
                                        Fecha de envío: {{ $document->submitted_date->format('d/m/Y') }}
                                    </p>
                                @endif

                                @if($document->expiration_date)
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
                                    <x-filament::icon icon="heroicon-o-eye" class="w-4 h-4 mr-1" />
                                    Ver Documento
                                </x-filament::button>

                                <x-filament::button
                                    wire:click="approveDocument({{ $document->id }})"
                                    size="sm"
                                    color="success"
                                    :disabled="$currentStatus == 2"
                                >
                                    <x-filament::icon icon="heroicon-o-check-circle" class="w-4 h-4 mr-1" />
                                    Aprobar
                                </x-filament::button>

                                <x-filament::button
                                    wire:click="rejectDocument({{ $document->id }})"
                                    size="sm"
                                    color="danger"
                                    :disabled="$currentStatus == 3"
                                >
                                    <x-filament::icon icon="heroicon-o-x-circle" class="w-4 h-4 mr-1" />
                                    Rechazar
                                </x-filament::button>

                                @if($currentStatus != 1)
                                    <x-filament::button
                                        wire:click="resetDocument({{ $document->id }})"
                                        size="sm"
                                        color="gray"
                                    >
                                        <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4 mr-1" />
                                        Restablecer
                                    </x-filament::button>
                                @endif
                            </div>

                            {{-- Razón de rechazo --}}
                            @if($currentStatus == 3)
                                <div class="rejection-reason-container">
                                    <label class="rejection-label">
                                        Razón del Rechazo *
                                    </label>
                                    <textarea
                                        wire:model="rejectionReasons.{{ $document->id }}"
                                        rows="3"
                                        class="rejection-textarea"
                                        placeholder="Ingrese la razón del rechazo..."
                                    ></textarea>
                                </div>
                            @endif
                    </div>
                    @endif
                @endforeach
                </div>
            @endif
        </div>

        {{-- Botones de acción --}}
        <div class="actions-footer">
            <x-filament::button
                wire:click="saveValidation"
                color="primary"
                size="lg"
            >
                <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 mr-2" />
                {{ $this->canApproveAll() ? 'Aprobar Conductor' : 'Guardar Validación' }}
            </x-filament::button>
        </div>
    </div>

    {{-- Modal para ver documentos --}}
    <x-filament::modal id="document-modal" width="7xl">
        <x-slot name="heading">
            Visualización de Documento
        </x-slot>

        @if($selectedDocument)
            <div class="modal-content">
                @php
                    $extension = strtolower(pathinfo($selectedDocument, PATHINFO_EXTENSION));
                    $documentUrl = route('documents.view', ['document' => $selectedDocumentId]);
                @endphp

                @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <img src="{{ $documentUrl }}" alt="Documento" class="modal-image">
                @elseif($extension === 'pdf')
                    <iframe src="{{ $documentUrl }}" class="modal-iframe"></iframe>
                @else
                    <div class="not-supported-container">
                        <div class="not-supported-content">
                            <x-filament::icon icon="heroicon-o-document" class="not-supported-icon" />
                            <p class="not-supported-text">Formato no soportado para vista previa</p>
                            <a href="{{ route('documents.download', ['document' => $selectedDocumentId]) }}" target="_blank" class="download-link">
                                Descargar documento
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </x-filament::modal>

    @script
    <script>
        $wire.on('open-document-modal', () => {
            $wire.dispatch('open-modal', { id: 'document-modal' });
        });
    </script>
    @endscript
</x-filament-panels::page>
