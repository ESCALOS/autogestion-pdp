<x-layouts.app>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Escritorio</h1>
        <p class="mt-1 text-sm text-slate-600">Gestione sus conductores, camiones y carretas desde un solo lugar</p>
    </div>

    @php
        $totalApproved = array_sum(array_column(array_column($modules, 'stats'), 'approved'));
        $totalPending = array_sum(array_column(array_column($modules, 'stats'), 'pending'));
        $totalRejected = array_sum(array_column(array_column($modules, 'stats'), 'rejected'));
        $totalRegistered = array_sum(array_column(array_column($modules, 'stats'), 'total'));
    @endphp


    {{-- Módulos de Acceso Rápido --}}
    <div class="mb-8">
        <h2 class="mb-4 text-xl font-semibold text-slate-900">Acceso Rápido</h2>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($modules as $module)
                <x-dashboard.access-card
                    :title="$module['title']"
                    :description="$module['description']"
                    :route="route($module['route'])"
                >
                    <x-slot name="icon">
                        <x-dynamic-component :component="'icon.' . $module['icon']" />
                    </x-slot>
                </x-dashboard.access-card>
            @endforeach
        </div>
    </div>

    {{-- Detalle por Módulo --}}
    <div>
        <h2 class="mb-4 text-xl font-semibold text-slate-900">Estado por Módulo</h2>
        <div class="mb-4 grid grid-cols-1 gap-5 lg:grid-cols-3">
            @foreach ($modules as $module)
                <x-dashboard.stat-card :title="$module['title']" :stats="$module['stats']">
                    <x-slot name="icon">
                        <x-dynamic-component :component="'icon.' . $module['icon']" :size="20" />
                    </x-slot>
                </x-dashboard.stat-card>
            @endforeach
        </div>
    </div>
</x-layouts.app>
