@props([
    'title',
    'description',
    'route',
    'icon',
])

<a
    href="{{ $route }}"
    wire:navigate
    class="group block rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition-all hover:shadow-md hover:ring-[#8b2d23]"
>
    <div class="flex items-start gap-4">
        <div
            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg transition-transform group-hover:scale-110"
            style="background-color: #8b2d23; color: #ffffff"
        >
            {!! $icon !!}
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="text-lg font-semibold text-slate-900 group-hover:text-[#8b2d23]">{{ $title }}</h3>
            <p class="mt-1 text-sm text-slate-600">{{ $description }}</p>
            <div class="mt-3 flex items-center text-sm font-medium" style="color: #8b2d23">
                <span>Acceder</span>
                <svg class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </div>
    </div>
</a>
