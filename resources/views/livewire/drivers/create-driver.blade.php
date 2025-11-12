<div>
    <div class="mb-4 flex items-center justify-between">
        <h2 class="mb-3 text-2xl font-bold">Crear Nuevo Conductor</h2>
        <a href="{{ route('drivers.index') }}" wire:navigate class="text-blue-500 hover:underline">Volver</a>
    </div>

    <form wire:submit.prevent="create">
        {{ $this->form }}
    </form>
</div>
