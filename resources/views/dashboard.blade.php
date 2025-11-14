<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
        <p class="text-sm text-slate-600">Bienvenido seleccione un módulo para comenzar.</p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
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

    <h4 class="mt-8 mb-4 text-lg font-semibold text-slate-900">Solicitudes</h4>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach ($modules as $module)
            <x-dashboard.stat-card :title="$module['title']" :stats="$module['stats']">
                <x-slot name="icon">
                    <x-dynamic-component :component="'icon.' . $module['icon']" :size="18" />
                </x-slot>
            </x-dashboard.stat-card>
        @endforeach
    </div>
</x-layouts.app>
