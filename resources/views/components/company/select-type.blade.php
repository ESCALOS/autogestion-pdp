<div class="mx-auto w-full max-w-2xl">
    <!-- Header -->
    <div class="mb-8 hidden text-center md:block">
        <h2 class="mb-2 text-3xl font-bold text-gray-800">Registro de Empresa</h2>
        <p class="text-gray-600">Complete el formulario según el tipo de empresa</p>
    </div>

    <!-- Card Container -->
    <div class="rounded-lg bg-white p-8 shadow-lg">
        <div class="mb-6 text-center">
            <h3 class="mb-2 text-xl font-semibold text-gray-800">Seleccione el Tipo de Empresa</h3>
            <p class="text-gray-600">Elija la categoría que corresponde a su empresa</p>
        </div>

        <!-- Options Grid -->
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Empresa Natural -->
            <div
                class="hover:border-primary-500 hover:bg-primary-50 group cursor-pointer rounded-lg border-2 border-gray-200 p-6 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                x-on:click="
                    setTimeout(() => {
                        type = 1
                        $dispatch('select-company-type', { type: 1 })
                    }, 100)
                "
            >
                <div class="text-center">
                    <div
                        class="bg-primary-100 group-hover:bg-primary-200 mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full transition-all duration-300"
                    >
                        <x-icon.user
                            class="text-primary-600 h-8 w-8 transition-transform duration-300 group-hover:scale-110"
                        />
                    </div>
                    <h4
                        class="group-hover:text-primary-700 text-lg font-semibold text-gray-800 transition-colors duration-300"
                    >
                        Empresa Natural
                    </h4>
                    <p class="group-hover:text-primary-600 mt-2 text-sm text-gray-500 transition-colors duration-300">
                        Persona física o empresario individual
                    </p>
                </div>
            </div>

            <!-- Empresa Jurídica -->
            <div
                class="hover:border-primary-500 hover:bg-primary-50 group cursor-pointer rounded-lg border-2 border-gray-200 p-6 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                x-on:click="
                    setTimeout(() => {
                        type = 2
                        $dispatch('select-company-type', { type: 2 })
                    }, 100)
                "
            >
                <div class="text-center">
                    <div
                        class="bg-primary-100 group-hover:bg-primary-200 mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full transition-all duration-300"
                    >
                        <x-icon.building
                            class="text-primary-600 h-8 w-8 transition-transform duration-300 group-hover:scale-110"
                        />
                    </div>
                    <h4
                        class="group-hover:text-primary-700 text-lg font-semibold text-gray-800 transition-colors duration-300"
                    >
                        Empresa Jurídica
                    </h4>
                    <p class="group-hover:text-primary-600 mt-2 text-sm text-gray-500 transition-colors duration-300">
                        Sociedad anónima, limitada u otra forma jurídica
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-start">
            <a
                href="{{ route('login') }}"
                class="text-primary-600 hover:text-primary-700 flex items-center text-sm font-medium"
                wire:navigate
            >
                <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        fill-rule="evenodd"
                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                        clip-rule="evenodd"
                    />
                </svg>
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>
