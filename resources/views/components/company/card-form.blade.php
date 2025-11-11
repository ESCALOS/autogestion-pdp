<div class="mx-auto w-full max-w-4xl px-4 sm:px-0">
    <!-- Card del formulario -->
    <div class="rounded-lg bg-white p-4 shadow-lg sm:p-8">
        <!-- Header del formulario -->
        <div class="mb-6 text-center sm:mb-8">
            <h3 class="mb-2 text-xl font-bold text-gray-800 sm:text-2xl">
                Registro de Empresa
                <span x-text="type === 1 ? 'Natural' : 'Jurídica'"></span>
            </h3>
            <p class="text-sm text-gray-600 sm:text-base">Complete todos los campos requeridos</p>
        </div>
        <!-- Contenido del formulario -->
        {{ $slot }}
    </div>
</div>
