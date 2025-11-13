<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
        <p class="text-sm text-slate-600">Bienvenido seleccione un módulo para comenzar.</p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <x-access-card title="Conductores" description="Gestión de conductores" :route="route('drivers.index')">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="20" height="20" fill="currentColor">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3z" />
                    <path d="M8 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                </svg>
            </x-slot>
        </x-access-card>

        <x-access-card title="Camiones" description="Gestión de vehículos" :route="route('trucks.index')">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M3 11h11v2H3v-2zm13 0h3l3 3v3h-2a2 2 0 1 1-4 0h-8a2 2 0 1 1-4 0H2v-3l1-5h14z" />
                </svg>
            </x-slot>
        </x-access-card>

        <x-access-card title="Chassis" description="Registro de chasis" :route="route('chassis.index')">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M12 2L3 7v10l9 5 9-5V7l-9-5zm0 2.2L18.7 7 12 10.8 5.3 7 12 4.2z" />
                </svg>
            </x-slot>
        </x-access-card>
    </div>

    <h4 class="mt-8 mb-4 text-lg font-semibold text-slate-900">Solicitudes</h4>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Drivers Requests -->
        <div class="rounded-lg bg-white p-4 shadow">
            <div class="mb-4 flex items-center">
                <div
                    class="mr-3 flex h-9 w-9 items-center justify-center rounded-md"
                    style="background: rgba(139, 45, 32, 0.06); color: #8b2d23"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 16 16"
                        width="18"
                        height="18"
                        fill="currentColor"
                    >
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3z" />
                        <path d="M8 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-slate-900">Drivers</div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded bg-amber-50 p-3 text-center">
                    <div class="text-xl font-semibold text-amber-700">{{ $drivers['pending'] }}</div>
                    <div class="text-xs text-amber-600">Pendientes</div>
                </div>
                <div class="rounded bg-emerald-50 p-3 text-center">
                    <div class="text-xl font-semibold text-emerald-700">{{ $drivers['approved'] }}</div>
                    <div class="text-xs text-emerald-600">Aprobadas</div>
                </div>
                <div class="rounded bg-rose-50 p-3 text-center">
                    <div class="text-xl font-semibold text-rose-700">{{ $drivers['rejected'] }}</div>
                    <div class="text-xs text-rose-600">Rechazadas</div>
                </div>
            </div>
        </div>

        <!-- Trucks Requests -->
        <div class="rounded-lg bg-white p-4 shadow">
            <div class="mb-4 flex items-center">
                <div
                    class="mr-3 flex h-9 w-9 items-center justify-center rounded-md"
                    style="background: rgba(139, 45, 32, 0.06); color: #8b2d23"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        width="18"
                        height="18"
                        fill="currentColor"
                    >
                        <path d="M3 11h11v2H3v-2zm13 0h3l3 3v3h-2a2 2 0 1 1-4 0h-8a2 2 0 1 1-4 0H2v-3l1-5h14z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-slate-900">Trucks</div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded bg-amber-50 p-3 text-center">
                    <div class="text-xl font-semibold text-amber-700">{{ $trucks['pending'] ?? 0 }}</div>
                    <div class="text-xs text-amber-600">Pendientes</div>
                </div>
                <div class="rounded bg-emerald-50 p-3 text-center">
                    <div class="text-xl font-semibold text-emerald-700">{{ $trucks['approved'] ?? 0 }}</div>
                    <div class="text-xs text-emerald-600">Aprobadas</div>
                </div>
                <div class="rounded bg-rose-50 p-3 text-center">
                    <div class="text-xl font-semibold text-rose-700">{{ $trucks['rejected'] ?? 0 }}</div>
                    <div class="text-xs text-rose-600">Rechazadas</div>
                </div>
            </div>
        </div>

        <!-- Chassis Requests -->
        <div class="rounded-lg bg-white p-4 shadow">
            <div class="mb-4 flex items-center">
                <div
                    class="mr-3 flex h-9 w-9 items-center justify-center rounded-md"
                    style="background: rgba(139, 45, 32, 0.06); color: #8b2d23"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        width="18"
                        height="18"
                        fill="currentColor"
                    >
                        <path d="M12 2L3 7v10l9 5 9-5V7l-9-5zm0 2.2L18.7 7 12 10.8 5.3 7 12 4.2z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-slate-900">Chassis</div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded bg-amber-50 p-3 text-center">
                    <div class="text-xl font-semibold text-amber-700">{{ $chassis['pending'] ?? 0 }}</div>
                    <div class="text-xs text-amber-600">Pendientes</div>
                </div>
                <div class="rounded bg-emerald-50 p-3 text-center">
                    <div class="text-xl font-semibold text-emerald-700">{{ $chassis['approved'] ?? 0 }}</div>
                    <div class="text-xs text-emerald-600">Aprobadas</div>
                </div>
                <div class="rounded bg-rose-50 p-3 text-center">
                    <div class="text-xl font-semibold text-rose-700">{{ $chassis['rejected'] ?? 0 }}</div>
                    <div class="text-xs text-rose-600">Rechazadas</div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
