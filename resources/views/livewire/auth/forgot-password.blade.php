<div class="flex min-h-screen items-center justify-center py-12">
    <div class="w-full max-w-md">
        <div class="rounded-lg bg-white p-8 shadow-lg">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Recuperar Contraseña</h2>
                <p class="mt-2 text-sm text-slate-600">
                    Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                </p>
            </div>

            <form wire:submit="sendResetLink">
                <div class="mb-4">
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-900">Correo Electrónico</label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        class="focus:border-primary-500 focus:ring-primary-500 block w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 placeholder-slate-400 focus:ring-1 focus:outline-none"
                        placeholder="tu@email.com"
                        required
                    />
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <button
                        type="submit"
                        class="bg-primary-500 hover:bg-primary-600 focus:ring-primary-500 w-full rounded-md px-4 py-2 font-semibold text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:outline-none"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Enviar Enlace</span>
                        <span wire:loading>Enviando...</span>
                    </button>
                </div>

                <div class="text-center">
                    <a
                        href="{{ route('login') }}"
                        wire:navigate
                        class="text-primary-500 hover:text-primary-600 text-sm font-medium"
                    >
                        ← Volver al inicio de sesión
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
