<div class="min-h-screen bg-gray-50 px-4 py-12 sm:px-6 lg:px-8 dark:bg-gray-900">
    <div class="mx-auto max-w-4xl">
        @if ($success)
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                <div class="p-6 sm:p-10">
                    <div class="mb-6 flex justify-center">
                        <div
                            class="flex size-20 items-center justify-center rounded-full bg-green-100 dark:bg-green-900"
                        >
                            <svg
                                class="size-10 text-green-600 dark:text-green-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                ></path>
                            </svg>
                        </div>
                    </div>

                    <h1 class="mb-4 text-center text-3xl font-bold text-gray-900 dark:text-white">
                        Documentos Enviados Exitosamente
                    </h1>

                    <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="size-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-800 dark:text-green-300">
                                    Sus documentos para el chassis
                                    <strong>{{ $chassis->license_plate }}</strong>
                                    han sido recibidos correctamente y serán revisados por nuestro equipo. Recibirá una
                                    notificación cuando la revisión esté completa.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Puede cerrar esta ventana de forma segura.
                    </div>
                </div>
            </div>
        @else
            <div class="mb-8 text-center">
                <div class="mb-4 flex justify-center">
                    <div class="flex size-16 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                        <svg
                            class="size-8 text-blue-600 dark:text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            ></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Apelación de Documentos - Chassis</h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                    Placa:
                    <span class="font-semibold">{{ $chassis->license_plate }}</span>
                </p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Empresa: {{ $chassis->company->business_name }}
                </p>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                <div class="p-6 sm:p-10">
                    <div class="mb-6 rounded-lg border-l-4 border-yellow-400 bg-yellow-50 p-4 dark:bg-yellow-900/20">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="size-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                                    Documentos Rechazados o Vencidos
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                                    <p>
                                        Los siguientes documentos requieren ser actualizados. Por favor, cargue los
                                        nuevos archivos y, si corresponde, actualice las fechas de vencimiento.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form wire:submit="submit">
                        {{ $this->form }}

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:w-auto"
                            >
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                                Enviar Documentos
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="size-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Información importante</h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                            <ul class="list-inside list-disc space-y-1">
                                <li>Los archivos deben estar en formato PDF, JPG, JPEG o PNG</li>
                                <li>El tamaño máximo por archivo es de 5MB</li>
                                <li>Las fechas de vencimiento deben ser posteriores a la fecha actual</li>
                                <li>Una vez enviados, los documentos serán revisados por nuestro equipo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
