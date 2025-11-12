<x-layouts.guest>
    <div class="min-h-screen bg-linear-to-br from-gray-50 to-gray-100 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900 md:text-4xl">Apelar Documentos Rechazados</h1>
                <p class="mt-2 text-base text-gray-600 md:text-lg">
                    Por favor, vuelva a cargar los documentos observados
                </p>
            </div>

            <!-- Información de la empresa -->
            <div class="mb-6 rounded-lg bg-white p-6 shadow-md">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500">RUC</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $company->ruc }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Razón Social</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $company->business_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Representante</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">
                            {{ $company->representative->full_name }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Instrucciones -->
            <div class="mb-6 rounded-lg border-l-4 border-blue-500 bg-blue-50 p-6">
                <p class="mb-3 text-base font-semibold text-blue-900">Instrucciones:</p>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 text-blue-600">•</span>
                        <span>Revise cuidadosamente los motivos de rechazo de cada documento.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 text-blue-600">•</span>
                        <span>Cargue nuevamente los documentos corregidos.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 text-blue-600">•</span>
                        <span>Los archivos deben ser PDF, JPG, JPEG o PNG (máximo 5MB).</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 text-blue-600">•</span>
                        <span>Una vez enviados, su solicitud volverá a estado pendiente para revisión.</span>
                    </li>
                </ul>
            </div>

            <!-- Alert de error -->
            @if (session('error'))
                <div class="mb-6 rounded-lg border-l-4 border-red-500 bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Formulario -->
            <form
                action="{{ route('company.appeal.update', $token) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-6"
            >
                @csrf
                @method('PUT')

                @foreach ($rejectedDocuments as $document)
                    <div class="rounded-lg bg-white p-6 shadow-md transition-shadow hover:shadow-lg">
                        <!-- Título del documento -->
                        <div class="mb-4 border-b border-gray-200 pb-3">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $document->type->getLabel() }}
                            </h3>
                        </div>

                        <!-- Motivo de rechazo -->
                        @if ($document->rejection_reason)
                            <div class="mb-4 rounded-md bg-red-50 p-4">
                                <p class="text-sm font-medium text-red-900">Motivo del rechazo:</p>
                                <p class="mt-1 text-sm text-red-800">{{ $document->rejection_reason }}</p>
                            </div>
                        @endif

                        <!-- Campo de archivo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="document_{{ $document->id }}">
                                Cargar nuevo documento
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="file"
                                id="document_{{ $document->id }}"
                                name="document_{{ $document->id }}"
                                class="mt-2 block w-full rounded-lg border border-gray-300 text-sm text-gray-900 file:mr-4 file:rounded-l-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                accept=".pdf,.jpg,.jpeg,.png"
                                required
                            />
                            <p class="mt-2 text-xs text-gray-500">
                                Formatos permitidos: PDF, JPG, JPEG, PNG (máximo 5MB)
                            </p>
                            @error("document_{$document->id}")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endforeach

                <!-- Botón de envío -->
                <div class="flex justify-center pt-4">
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow-md transition-all hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none active:scale-95"
                    >
                        Enviar Documentos Corregidos
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.guest>
