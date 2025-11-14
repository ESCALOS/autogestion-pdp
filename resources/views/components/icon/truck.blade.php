@props(['size' => 20])

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    width="{{ $size }}"
    height="{{ $size }}"
    fill="currentColor"
    {{ $attributes }}
>
    <path d="M3 11h11v2H3v-2zm13 0h3l3 3v3h-2a2 2 0 1 1-4 0h-8a2 2 0 1 1-4 0H2v-3l1-5h14z" />
</svg>
