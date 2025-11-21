@props([
    'title',
    'icon',
    'stats',
])

<div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition-all hover:shadow-md">
    <div class="mb-5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div
                class="flex h-10 w-10 items-center justify-center rounded-lg"
                style="background: rgba(139, 45, 32, 0.1); color: #8b2d23"
            >
                {{ $icon }}
            </div>
            <div>
                <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                <p class="text-xs text-slate-500">Total: {{ $stats['total'] }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div class="flex items-center justify-between rounded-lg bg-amber-50 px-4 py-3">
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-amber-500"></div>
                <span class="text-sm font-medium text-amber-900">Pendiente de aprobación</span>
            </div>
            <span class="text-lg font-bold text-amber-700">{{ $stats['pending'] }}</span>
        </div>

        <div class="flex items-center justify-between rounded-lg bg-emerald-50 px-4 py-3">
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                <span class="text-sm font-medium text-emerald-900">Activos</span>
            </div>
            <span class="text-lg font-bold text-emerald-700">{{ $stats['approved'] }}</span>
        </div>

        <div class="flex items-center justify-between rounded-lg bg-rose-50 px-4 py-3">
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-rose-500"></div>
                <span class="text-sm font-medium text-rose-900">Rechazados</span>
            </div>
            <span class="text-lg font-bold text-rose-700">{{ $stats['rejected'] }}</span>
        </div>
    </div>
</div>
