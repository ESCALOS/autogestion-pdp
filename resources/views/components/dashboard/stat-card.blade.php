@props([
    'title',
    'icon',
    'stats',
])

<div class="rounded-lg bg-white p-4 shadow">
    <div class="mb-4 flex items-center">
        <div
            class="mr-3 flex h-9 w-9 items-center justify-center rounded-md"
            style="background: rgba(139, 45, 32, 0.06); color: #8b2d23"
        >
            {{ $icon }}
        </div>
        <div>
            <div class="text-sm font-medium text-slate-900">{{ $title }}</div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-3">
        <div class="rounded bg-amber-50 p-3 text-center">
            <div class="text-xl font-semibold text-amber-700">{{ $stats['pending'] }}</div>
            <div class="text-xs text-amber-600">Pendientes</div>
        </div>
        <div class="rounded bg-emerald-50 p-3 text-center">
            <div class="text-xl font-semibold text-emerald-700">{{ $stats['approved'] }}</div>
            <div class="text-xs text-emerald-600">Aprobadas</div>
        </div>
        <div class="rounded bg-rose-50 p-3 text-center">
            <div class="text-xl font-semibold text-rose-700">{{ $stats['rejected'] }}</div>
            <div class="text-xs text-rose-600">Rechazadas</div>
        </div>
    </div>
</div>
