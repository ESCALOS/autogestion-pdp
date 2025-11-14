@props(['size' => 20])

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    width="{{ $size }}"
    height="{{ $size }}"
    fill="currentColor"
    {{ $attributes }}
>
    <path d="M12 2L3 7v10l9 5 9-5V7l-9-5zm0 2.2L18.7 7 12 10.8 5.3 7 12 4.2z" />
</svg>
