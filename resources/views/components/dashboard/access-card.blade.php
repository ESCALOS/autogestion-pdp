@props([
    'title',
    'description',
    'route',
    'icon',
])

<div
    class="flex items-start space-x-4 rounded-lg border bg-white p-4 text-slate-900 shadow-md transition-shadow hover:shadow-lg"
    style="border-color: #e9d7d4"
>
    <div
        class="inline-flex shrink-0 items-center justify-center rounded-md p-3"
        style="background-color: #8b2d23; color: #ffffff"
    >
        {!! $icon !!}
    </div>
    <div class="flex-1">
        <h5 class="text-lg font-semibold text-slate-900">{{ $title }}</h5>
        <p class="text-sm text-slate-600">{{ $description }}</p>
        <a
            href="{{ $route }}"
            wire:navigate
            class="mt-2 inline-block font-medium hover:underline"
            style="color: #8b2d23"
        >
            Acceder →
        </a>
    </div>
</div>
