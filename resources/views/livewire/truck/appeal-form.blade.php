<div
    class="min-h-screen bg-linear-to-br from-gray-50 to-gray-100 px-4 py-8 sm:px-6 lg:px-8 dark:from-gray-900 dark:to-gray-800"
>
    @if ($success)
        {{-- Vista de éxito --}}
        <div class="mx-auto max-w-2xl">
            <div class="overflow-hidden rounded-lg bg-white shadow-xl dark:bg-gray-800">
                <div class="bg-green-600 px-6 py-12 text-center dark:bg-green-700">
                    <div class="mx-auto mb-6 flex size-20 items-center justify-center rounded-full bg-white">
                        <svg
                            class="size-12 text-green-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white">¡Documentos Enviados Exitosamente!</h1>
                </div>

                <div class="px-6 py-8">
                    <p class="mb-4 text-gray-700 dark:text-gray-300">
                        Los documentos del camión han sido recibidos correctamente.
                    </p>

                    <p class="mb-6 text-gray-700 dark:text-gray-300">
                        El camión ha vuelto a estado
                        <strong class="text-blue-600 dark:text-blue-400">REVISIÓN DE DOCUMENTOS</strong>
                        y será revisado nuevamente por nuestro equipo.
                    </p>

                    <div class="rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4 dark:bg-blue-900/20">
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-300">
                            <strong>Le notificaremos por correo electrónico</strong>
                            cuando se complete la revisión de los documentos.
                        </p>
                    </div>

                    <p class="mt-6 text-gray-700 dark:text-gray-300">Gracias por su colaboración.</p>

                    <p class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Este enlace de apelación ya no está disponible.
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- Formulario de apelación --}}
        <div class="mx-auto max-w-4xl">
            {{-- Header --}}
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900 md:text-4xl dark:text-white">
                    Apelar Documentos Rechazados - Camión
                </h1>
                <p class="mt-2 text-base text-gray-600 md:text-lg dark:text-gray-400">
                    Por favor, vuelva a cargar los documentos observados
                </p>
            </div>

            {{-- Información del camión --}}
            <div class="mb-6 rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Placa</p>
                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                            {{ $truck->license_plate }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Empresa</p>
                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                            {{ $truck->company->business_name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo</p>
                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                            {{ $truck->is_internal ? 'Interno' : 'Externo' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Instrucciones --}}
            <div class="mb-6 rounded-lg border-l-4 border-blue-500 bg-blue-50 p-6 dark:bg-blue-900/20">
                <p class="mb-3 text-base font-semibold text-blue-900 dark:text-blue-300">Instrucciones:</p>
                <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                        <span>Revise cuidadosamente los motivos de rechazo de cada documento.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                        <span>Cargue nuevamente los documentos corregidos.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                        <span>Para documentos vencidos, actualice también la fecha de vencimiento.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 text-blue-600 dark:text-blue-400">•</span>
                        <span>Los archivos deben ser PDF, JPG, JPEG o PNG (máximo 5MB).</span>
                    </li>
                </ul>
            </div>

            {{-- Formulario --}}
            <div class="rounded-lg bg-white p-6 shadow-lg dark:bg-gray-800">
                <form wire:submit="submit" class="space-y-6">
                    {{ $this->form }}

                    <div class="flex justify-center pt-4">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-md transition-all hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-blue-700 dark:hover:bg-blue-600"
                        >
                            <span wire:loading.remove wire:target="submit">Enviar Documentos Corregidos</span>
                            <span wire:loading wire:target="submit">Procesando...</span>
                        </button>
                    </div>
                </form>
            </div>

            <x-filament-actions::modals />
        </div>
    @endif
</div>
