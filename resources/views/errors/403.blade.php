<!DOCTYPE html>
<html lang="es" class="h-full">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>403 - Acceso Denegado</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="h-full bg-linear-to-br from-red-50 via-orange-50 to-amber-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-800"
    >
        <div class="flex min-h-full items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-2xl">
                <div class="overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800">
                    <div class="px-6 py-12 sm:px-12">
                        <div
                            class="mx-auto mb-8 flex size-28 items-center justify-center rounded-full bg-linear-to-br from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30"
                        >
                            <svg
                                class="size-16 text-red-600 dark:text-red-400"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>

                        <div class="mb-6 text-center">
                            <h1
                                class="bg-linear-to-r from-red-600 to-orange-600 bg-clip-text text-9xl font-black tracking-tight text-transparent dark:from-red-400 dark:to-orange-400"
                            >
                                403
                            </h1>
                        </div>

                        <h2 class="mb-4 text-center text-4xl font-bold text-gray-900 dark:text-white">
                            Acceso Denegado
                        </h2>

                        <p class="mb-8 text-center text-lg leading-relaxed text-gray-600 dark:text-gray-300">
                            Lo sentimos, no tienes los permisos necesarios para acceder a este recurso.
                        </p>

                        <div
                            class="mb-8 rounded-xl border border-red-200 bg-red-50/50 p-6 dark:border-red-800/50 dark:bg-red-900/10"
                        >
                            <p class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Posibles razones:</p>
                            <ul class="space-y-2.5">
                                <li class="flex items-start gap-3">
                                    <svg
                                        class="mt-0.5 size-5 shrink-0 text-red-500 dark:text-red-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        No tienes los permisos necesarios para ver este contenido
                                    </span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg
                                        class="mt-0.5 size-5 shrink-0 text-red-500 dark:text-red-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        Tu rol de usuario no tiene acceso a esta sección
                                    </span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg
                                        class="mt-0.5 size-5 shrink-0 text-red-500 dark:text-red-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        Este recurso pertenece a otra empresa u organización
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
                            <button
                                onclick="window.history.back()"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border-2 border-gray-300 bg-white px-6 py-3.5 text-base font-semibold text-gray-700 shadow-sm transition-all hover:border-gray-400 hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-500 dark:hover:bg-gray-700"
                            >
                                <svg
                                    class="size-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                    />
                                </svg>
                                Volver atrás
                            </button>

                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-linear-to-r from-blue-600 to-blue-700 px-6 py-3.5 text-base font-semibold text-white shadow-lg transition-all hover:from-blue-700 hover:to-blue-800 hover:shadow-xl focus:ring-4 focus:ring-blue-300 focus:outline-none dark:from-blue-700 dark:to-blue-800 dark:hover:from-blue-600 dark:hover:to-blue-700"
                            >
                                <svg
                                    class="size-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                    />
                                </svg>
                                Ir al inicio
                            </a>
                        </div>

                        <div class="mt-8 text-center">
                            <p class="text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                                Si crees que esto es un error, por favor contacta al administrador del sistema.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Error 403: Forbidden | {{ config('app.name') }}
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
