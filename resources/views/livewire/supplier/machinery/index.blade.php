<div>
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">
            <a href="{{ route('supplier.dashboard') }}" wire:navigate>←</a>
            Gestión de Maquinaria
        </h2>
        <a
            href="{{ route('supplier.machinery.create') }}"
            wire:navigate
            class="bg-primary-500 hover:bg-primary-600 rounded px-4 py-2 text-white"
        >
            Nueva maquinaria
        </a>
    </div>
    {{ $this->table }}
</div>
