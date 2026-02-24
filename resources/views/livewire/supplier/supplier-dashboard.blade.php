<div>
    <x-layouts.app>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Escritorio Proveedor</h1>
            <p class="mt-1 text-sm text-slate-600">Gestione la información de su proveedor desde un solo lugar</p>
        </div>

        @if ($supplier)
            {{-- Módulos de Acceso Rápido --}}
            <div class="mb-8">
                <h2 class="mb-4 text-xl font-semibold text-slate-900">Acceso Rápido</h2>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <x-dashboard.access-card
                        title="Información General"
                        description="Ver detalles de la empresa"
                        route="#"
                    >
                        <x-slot name="icon">
                            <div class="rounded-lg bg-blue-100 p-3">
                                <x-filament::icon icon="heroicon-o-information-circle" class="w-6 h-6 text-blue-600" />
                            </div>
                        </x-slot>
                    </x-dashboard.access-card>

                    <x-dashboard.access-card
                        title="Documentos"
                        description="Gestión de documentos"
                        route="#"
                    >
                        <x-slot name="icon">
                            <div class="rounded-lg bg-purple-100 p-3">
                                <x-filament::icon icon="heroicon-o-document-text" class="w-6 h-6 text-purple-600" />
                            </div>
                        </x-slot>
                    </x-dashboard.access-card>

                    <x-dashboard.access-card
                        title="Contacto"
                        description="Información de contacto"
                        route="#"
                    >
                        <x-slot name="icon">
                            <div class="rounded-lg bg-green-100 p-3">
                                <x-filament::icon icon="heroicon-o-phone" class="w-6 h-6 text-green-600" />
                            </div>
                        </x-slot>
                    </x-dashboard.access-card>
                </div>
            </div>

            {{-- Detalle de Estado --}}
            <div>
                <h2 class="mb-4 text-xl font-semibold text-slate-900">Estado del Proveedor</h2>
                <div class="mb-4 grid grid-cols-1 gap-5 lg:grid-cols-3">
                    {{-- Estado General --}}
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Estado de Solicitud</p>
                                @if ($supplier->status->value === 'APROBADO')
                                    <p class="text-lg font-semibold text-green-600">Aprobado</p>
                                @elseif ($supplier->status->value === 'RECHAZADO')
                                    <p class="text-lg font-semibold text-red-600">Rechazado</p>
                                @else
                                    <p class="text-lg font-semibold text-yellow-600">Pendiente</p>
                                @endif
                            </div>
                            <div class="rounded-lg bg-slate-100 p-3">
                                @if ($supplier->status->value === 'APROBADO')
                                    <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-green-600" />
                                @elseif ($supplier->status->value === 'RECHAZADO')
                                    <x-filament::icon icon="heroicon-o-x-circle" class="w-6 h-6 text-red-600" />
                                @else
                                    <x-filament::icon icon="heroicon-o-clock" class="w-6 h-6 text-yellow-600" />
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Información de la Empresa --}}
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Razón Social</p>
                                <p class="text-lg font-semibold text-slate-900 truncate">{{ $supplier->business_name }}</p>
                                <p class="text-xs text-slate-500 mt-1">RUC: {{ $supplier->ruc }}</p>
                            </div>
                            <div class="rounded-lg bg-blue-100 p-3">
                                <x-filament::icon icon="heroicon-o-building-office-2" class="w-6 h-6 text-blue-600" />
                            </div>
                        </div>
                    </div>

                    {{-- Teléfono y Verificación --}}
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Teléfono Verificado</p>
                                @if ($supplier->phone_verified_at)
                                    <p class="text-lg font-semibold text-green-600">{{ $supplier->phone_prefix }} {{ $supplier->phone_number }}</p>
                                    <p class="text-xs text-slate-500 mt-1">Verificado el {{ $supplier->phone_verified_at->format('d/m/Y') }}</p>
                                @else
                                    <p class="text-lg font-semibold text-yellow-600">Pendiente de Verificar</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $supplier->phone_prefix }} {{ $supplier->phone_number }}</p>
                                @endif
                            </div>
                            <div class="rounded-lg bg-purple-100 p-3">
                                @if ($supplier->phone_verified_at)
                                    <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-green-600" />
                                @else
                                    <x-filament::icon icon="heroicon-o-phone" class="w-6 h-6 text-purple-600" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-6">
                <p class="text-yellow-800">No se encontró información del proveedor.</p>
            </div>
        @endif
    </x-layouts.app>
</div>
