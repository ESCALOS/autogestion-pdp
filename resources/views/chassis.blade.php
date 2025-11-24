<x-layouts.app>
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">
            <a href="{{ route('dashboard') }}" wire:navigate>←</a>
            Gestión de Carretas
        </h2>
        <a
            href="{{ route('chassis.create') }}"
            wire:navigate
            class="bg-primary-500 hover:bg-primary-600 rounded px-4 py-2 text-white"
        >
            Nuevo Carreta
        </a>
    </div>
    @livewire('chassis.list-chassis')
</x-layouts.app>
