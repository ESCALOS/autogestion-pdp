<div
    x-data="{
        type: @entangle('companyType'),
    }"
    class="relative flex min-h-[calc(100vh-80px)] items-center justify-center py-8"
>
    <!-- Card de selección de tipo -->
    <div
        x-show="type === 0"
        x-transition:enter="transition delay-150 duration-500 ease-out"
        x-transition:enter-start="translate-x-full scale-95 transform opacity-0"
        x-transition:enter-end="translate-x-0 scale-100 transform opacity-100"
        x-transition:leave="transition duration-300 ease-in"
        x-transition:leave-start="translate-x-0 scale-100 transform opacity-100"
        x-transition:leave-end="-translate-x-full scale-95 transform opacity-0"
        class="absolute top-0 right-0 left-0 flex min-h-full items-center justify-center px-4"
    >
        <x-company.select-type />
    </div>

    <!-- Formulario de registro -->
    <div
        x-show="type > 0"
        x-transition:enter="transition delay-150 duration-500 ease-out"
        x-transition:enter-start="translate-x-full scale-95 transform opacity-0"
        x-transition:enter-end="translate-x-0 scale-100 transform opacity-100"
        x-transition:leave="transition duration-300 ease-in"
        x-transition:leave-start="translate-x-0 scale-100 transform opacity-100"
        x-transition:leave-end="translate-x-full scale-95 transform opacity-0"
        class="absolute top-0 right-0 left-0 flex min-h-full flex-col items-center justify-start px-4 py-4 md:justify-center md:py-0"
    >
        <div class="mb-4 text-center md:mb-8">
            <div class="mb-2 flex items-center justify-center gap-2 md:mb-4 md:gap-4">
                <a
                    x-on:click="type = 0"
                    class="text-primary-500 hover:text-primary-700 cursor-pointer text-xl transition-colors md:text-2xl"
                    title="Volver a la selección de tipo de empresa"
                >
                    &larr;
                </a>
                <h2 class="text-2xl font-bold text-gray-800 md:text-3xl">
                    Empresa
                    <span x-text="type === 1 ? 'Natural' : 'Jurídica'"></span>
                </h2>
            </div>
            <p class="text-sm text-gray-600 md:text-base">Complete el formulario según el tipo de empresa</p>
        </div>

        <div class="w-full max-w-4xl">
            <form wire:submit.prevent="create">
                {{ $this->form }}
            </form>
        </div>
    </div>
</div>
