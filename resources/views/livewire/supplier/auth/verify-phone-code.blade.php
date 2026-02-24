<div class="min-h-screen flex items-center justify-center bg-linear-to-br from-primary-50 to-primary-100 dark:from-gray-900 dark:to-gray-800 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <x-filament::icon icon="heroicon-o-check-circle" class="w-16 h-16 text-green-500" />
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Verifica tu Teléfono
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Hemos enviado un código a tu WhatsApp al número {{ $supplier->phone_prefix }} {{ $supplier->phone_number }}
                </p>
            </div>

            <!-- Form -->
            <form wire:submit="verifyCode" class="space-y-6">
                <!-- Code Input -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Código de Verificación
                    </label>
                    <input
                        type="text"
                        id="code"
                        wire:model="verificationCode"
                        maxlength="6"
                        inputmode="numeric"
                        placeholder="000000"
                        pattern="[0-9]*"
                        class="w-full px-4 py-3 text-center text-2xl tracking-widest font-semibold rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900 transition"
                        @input="value = value.replace(/[^0-9]/g, '')"
                        @if ($codeExpired) disabled @endif
                    />
                    @error('verificationCode')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Attempts Left Info -->
                @if (!$codeExpired && $attemptsLeft > 0)
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-center">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Intentos restantes: <span class="font-semibold">{{ $attemptsLeft }}</span>
                        </p>
                    </div>
                @endif

                <!-- Submit Button -->
                @if (!$codeExpired)
                    <button
                        type="submit"
                        class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors duration-200"
                    >
                        Verificar Código
                    </button>
                @else
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-center">
                        <p class="text-red-700 dark:text-red-300 text-sm font-semibold mb-4">
                            El código ha expirado. Por favor solicita uno nuevo.
                        </p>
                        <button
                            type="button"
                            wire:click="resendCode"
                            class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors duration-200"
                        >
                            Enviar Nuevo Código
                        </button>
                    </div>
                @endif

                <!-- Resend Code Link -->
                @if (!$codeExpired)
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            ¿No recibiste el código?
                            <button
                                type="button"
                                wire:click="resendCode"
                                class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-semibold transition"
                            >
                                Reenviar
                            </button>
                        </p>
                    </div>
                @endif
            </form>

            <!-- Help Text -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                    El código tiene una validez de 10 minutos. Si no aparece, revisa tu carpeta de spam.
                </p>
            </div>
        </div>

        <!-- Back to Login Link -->
        <div class="text-center mt-6">
            <a href="{{ route('supplier.login') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-semibold transition">
                Volver al Login
            </a>
        </div>
    </div>
</div>
