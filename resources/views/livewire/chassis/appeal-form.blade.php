<div class="py-6 sm:py-12">
    @if ($success)
        {{-- Vista de éxito --}}
        <div class="mx-auto max-w-2xl">
            <div class="overflow-hidden rounded-2xl bg-white shadow-lg dark:bg-gray-800">
                <div class="bg-linear-to-r from-green-500 to-emerald-600 px-4 py-8 text-center sm:px-6 sm:py-12">
                    <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-white shadow-lg sm:mb-6 sm:size-20">
                        <svg
                            class="size-10 text-green-600 sm:size-12"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">¡Documentos Enviados Exitosamente!</h1>
                </div>

                <div class="px-4 py-6 sm:px-6 sm:py-8">
                    <p class="mb-4 text-gray-600 dark:text-gray-300">
                        Los documentos del chassis han sido recibidos correctamente.
                    </p>

                    <p class="mb-6 text-gray-600 dark:text-gray-300">
                        El chassis ha vuelto a estado
                        <strong class="text-blue-600 dark:text-blue-400">REVISIÓN DE DOCUMENTOS</strong>
                        y será revisado nuevamente por nuestro equipo.
                    </p>

                    <div class="rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4 dark:border-blue-400 dark:bg-blue-900/20">
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-300">
                            <strong>Le notificaremos por correo electrónico</strong>
                            cuando se complete la revisión de los documentos.
                        </p>
                    </div>

                    <p class="mt-6 text-gray-600 dark:text-gray-300">Gracias por su colaboración.</p>

                    <p class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Este enlace de apelación ya no está disponible.
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- Formulario de apelación --}}
        <div class="mx-auto max-w-5xl">
            {{-- Header --}}
            <div class="mb-6 text-center sm:mb-10">
                <h1 class="text-2xl font-bold text-gray-800 sm:text-4xl md:text-5xl dark:text-white">
                    Actualizar Documentos del Chassis
                </h1>
                <p class="mt-2 text-base text-gray-600 sm:mt-3 sm:text-lg dark:text-gray-300">
                    Por favor, vuelva a cargar los documentos observados o vencidos
                </p>
            </div>

            {{-- Información del chassis (tarjetas superiores) --}}
            <div class="mb-6 grid gap-3 sm:mb-8 sm:gap-5 md:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 transition-all hover:border-primary-300 hover:shadow-md sm:p-6 dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-3 flex size-10 items-center justify-center rounded-lg sm:mb-4 sm:size-12" style="background-color: #8b2d20;">
                        <svg class="size-5 text-white sm:size-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                        </svg>
                    </div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 sm:text-sm dark:text-gray-400">Placa</p>
                    <p class="mt-1 text-lg font-bold text-gray-900 sm:text-xl dark:text-white">{{ $chassis->license_plate }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 transition-all hover:border-primary-300 hover:shadow-md sm:p-6 dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-3 flex size-10 items-center justify-center rounded-lg sm:mb-4 sm:size-12" style="background-color: #8b2d20;">
                        <svg class="size-5 text-white sm:size-6" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 sm:text-sm dark:text-gray-400">Empresa</p>
                    <p class="mt-1 text-lg font-bold text-gray-900 sm:text-xl dark:text-white">
                        {{ $chassis->company->business_name }}
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 transition-all hover:border-primary-300 hover:shadow-md sm:p-6 dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-3 flex size-10 items-center justify-center rounded-lg sm:mb-4 sm:size-12" style="background-color: #8b2d20;">
                        <svg class="size-5 text-white sm:size-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 sm:text-sm dark:text-gray-400">Tipo de Vehículo</p>
                    <p class="mt-1 text-lg font-bold text-gray-900 sm:text-xl dark:text-white">
                        {{ $chassis->vehicle_type ?? 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- Instrucciones --}}
            <div class="mb-6 rounded-xl border-l-4 border-amber-500 bg-amber-50/80 p-4 sm:mb-8 sm:p-6 dark:border-amber-400 dark:bg-amber-900/20">
                <div class="flex items-start gap-3 sm:gap-4">
                    <div class="hidden mt-0.5 md:flex size-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 sm:size-10 dark:bg-amber-900/40">
                        <svg
                            class="size-5 text-amber-600 sm:size-6 dark:text-amber-400"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="mb-2 text-sm font-bold text-amber-900 sm:mb-3 sm:text-base dark:text-amber-200">Instrucciones:</p>
                        <ul class="space-y-2 text-xs sm:space-y-2.5 sm:text-sm text-amber-800 dark:text-amber-300">
                            <li class="flex items-start gap-2">
                                <span class="mt-1 text-amber-600 dark:text-amber-400">•</span>
                                <span>Revise cuidadosamente los motivos de rechazo o las fechas de vencimiento.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 text-amber-600 dark:text-amber-400">•</span>
                                <span>Cargue nuevamente los documentos corregidos o actualizados.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 text-amber-600 dark:text-amber-400">•</span>
                                <span>Los archivos deben ser PDF, JPG, JPEG o PNG (máximo 5MB).</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 text-amber-600 dark:text-amber-400">•</span>
                                <span>
                                    Si el documento tiene fecha de vencimiento, deberá proporcionar la nueva fecha.
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 sm:p-8 dark:border-gray-700 dark:bg-gray-800">

                <form wire:submit="submit" class="space-y-6">
                    {{ $this->form }}

                    <div class="flex justify-center pt-4 sm:pt-6">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center rounded-lg px-6 py-3 text-sm font-semibold text-white transition-all disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto sm:text-base"
                            style="background-color: #8b2d20;"
                            onmouseover="this.style.backgroundColor='#6b2218'" 
                            onmouseout="this.style.backgroundColor='#8b2d20'"
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
