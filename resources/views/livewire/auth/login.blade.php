<div class="flex min-h-[calc(100vh-80px)] items-center justify-center py-8">
    <div class="w-full max-w-md rounded-lg bg-white p-8 shadow-lg">
        <!-- Header -->
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-700">
                <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                        clip-rule="evenodd"
                    ></path>
                </svg>
            </div>
            <h2 class="text-2xl font-semibold text-gray-800">Sistema de Autogestión Paracas</h2>
            <p class="mt-2 text-gray-600">Ingrese sus credenciales para acceder al sistema</p>
        </div>

        <!-- Form -->
        <form wire:submit="login" class="space-y-6">
            <!-- Email Field -->
            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input
                    type="email"
                    id="email"
                    wire:model="email"
                    placeholder="usuario@ejemplo.com"
                    class="w-full rounded-md border border-gray-300 px-4 py-3 focus:border-transparent focus:ring-2 focus:ring-red-500 focus:outline-none"
                    required
                />
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Password Field -->
            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-gray-700">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    wire:model="password"
                    placeholder="Ingrese su contraseña"
                    class="w-full rounded-md border border-gray-300 px-4 py-3 focus:border-transparent focus:ring-2 focus:ring-red-500 focus:outline-none"
                    required
                />
            </div>

            <!-- Login Button -->
            <button
                wire:loading.attr="disabled"
                type="submit"
                class="w-full rounded-md bg-red-700 px-4 py-3 font-medium text-white transition duration-200 hover:bg-red-800 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="login">Iniciar Sesión</span>
                <span wire:loading wire:target="login">Cargando...</span>
            </button>

            <!-- Forgot Password Link -->
            <div class="text-center">
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">¿Olvidó su contraseña?</a>
            </div>

            <!-- Register Company Link -->
            <div class="mt-6 border-t pt-6">
                <a
                    href="{{ route('company.create') }}"
                    wire:navigate
                    class="block w-full rounded-md border border-gray-300 px-4 py-3 text-center font-medium text-gray-700 transition duration-200 hover:bg-gray-50"
                >
                    Registrar Empresa
                </a>
            </div>
        </form>
    </div>
</div>
