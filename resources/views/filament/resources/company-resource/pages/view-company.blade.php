<x-filament-panels::page>
    @vite(['resources/css/validation-documents.css'])

    <div class="validation-container">
        {{-- Información de la Empresa --}}
        <div class="info-card">
            <h2 class="info-card-title">Información de la Empresa</h2>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">RUC:</span>
                    <span class="info-value">{{ $record->ruc }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Razón Social:</span>
                    <span class="info-value">{{ $record->business_name }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Tipo:</span>
                    <span class="info-value">{{ $record->type === 2 ? 'Persona Jurídica' : 'Persona Natural' }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Representante Legal:</span>
                    <span class="info-value">{{ $record->representative->full_name }}</span>
                </div>

                @if ($record->representative->email)
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $record->representative->email ?? 'N/A' }}</span>
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
                        $document = $record->documents->firstWhere('type', $requiredType);
                        $currentStatus = $document ? $documentStatuses[$document->id] ?? $document->status->value : null;
                    @endphp

                    <div
                        class="document-card {{ $currentStatus == 2 ? 'approved' : ($currentStatus == 3 ? 'rejected' : '') }}"
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
                                    <x-filament::badge
                                        :color="App\Enums\CompanyDocumentStatusEnum::from($currentStatus)->getColor()"
                                    >
                                        {{ App\Enums\CompanyDocumentStatusEnum::from($currentStatus)->getLabel() }}
                                    </x-filament::badge>
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
                                    wire:click="approveDocument({{ $document->id }})"
                                    size="sm"
                                    color="success"
                                    :disabled="$currentStatus == 2"
                                >
                                    <x-filament::icon icon="heroicon-o-check-circle" class="mr-1 h-4 w-4" />
                                    Aprobar
                                </x-filament::button>

                                <x-filament::button
                                    wire:click="rejectDocument({{ $document->id }})"
                                    size="sm"
                                    color="danger"
                                    :disabled="$currentStatus == 3"
                                >
                                    <x-filament::icon icon="heroicon-o-x-circle" class="mr-1 h-4 w-4" />
                                    Rechazar
                                </x-filament::button>

                                @if ($currentStatus != 1)
                                    <x-filament::button
                                        wire:click="resetDocument({{ $document->id }})"
                                        size="sm"
                                        color="gray"
                                    >
                                        <x-filament::icon icon="heroicon-o-arrow-path" class="mr-1 h-4 w-4" />
                                        Restablecer
                                    </x-filament::button>
                                @endif
                            </div>

                            {{-- Razón de rechazo --}}
                            @if ($currentStatus == 3)
                                <div class="rejection-reason-container">
                                    <label class="rejection-label">Razón del Rechazo *</label>
                                    <textarea
                                        wire:model="rejectionReasons.{{ $document->id }}"
                                        rows="3"
                                        class="rejection-textarea"
                                        placeholder="Ingrese la razón del rechazo..."
                                    ></textarea>
                                </div>
                            @endif
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
            <x-filament::button wire:click="saveValidation" color="primary" size="lg">
                <x-filament::icon icon="heroicon-o-check" class="mr-2 h-5 w-5" />
                {{ $this->canApproveAll() ? 'Aprobar Empresa' : 'Guardar Validación' }}
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
                    $documentUrl = route('company.document.view', $selectedDocument->id);
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
