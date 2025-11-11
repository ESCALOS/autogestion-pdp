<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $title ?? config('app.name')])
    </head>
    <body class="bg-secondary-500 antialiased">
        <header class="bg-primary-500 w-full py-4 text-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <h1 class="text-xl font-bold">{{ config('app.name') }}</h1>
                <img src="{{ asset('images/logo-pdp.webp') }}" alt="{{ config('app.name') }} Logo" class="h-12" />
            </div>
        </header>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
        @livewire('notifications')
        @filamentScripts
    </body>
</html>
