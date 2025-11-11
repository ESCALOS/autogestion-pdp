<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Documentos - {{ $driver->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #ef4444;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .driver-info {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #ef4444;
        }
        .driver-info p {
            margin: 5px 0;
        }
        .instructions {
            background-color: #fef3c7;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #f59e0b;
        }
        .instructions p {
            color: #92400e;
            line-height: 1.6;
        }
        .document-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .rejection-reason {
            background-color: #fee2e2;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            color: #991b1b;
        }
        .rejection-reason strong {
            display: block;
            margin-bottom: 5px;
        }
        .expiration-warning {
            background-color: #fef9c3;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            color: #854d0e;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #374151;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
        }
        .file-info {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }
        .submit-btn {
            background-color: #3b82f6;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #2563eb;
        }
        .error-message {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Actualizar Documentos</h1>
            <p>{{ $driver->full_name }}</p>
        </div>
        <div class="content">
            @if(session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            <div class="driver-info">
                <p><strong>Nombre Completo:</strong> {{ $driver->full_name }}</p>
                <p><strong>{{ $driver->document_type->getLabel() }}:</strong> {{ $driver->document_number }}</p>
                <p><strong>Empresa:</strong> {{ $driver->company->business_name }}</p>
                @if($driver->license_number)
                <p><strong>Número de Licencia:</strong> {{ $driver->license_number }}</p>
                @endif
            </div>

            <div class="instructions">
                <p><strong>Instrucciones:</strong> Por favor, cargue los documentos corregidos o actualizados. Asegúrese de que cumplan con los requisitos especificados. Una vez enviados, su solicitud será revisada nuevamente por nuestro equipo.</p>
            </div>

            <form action="{{ route('driver.appeal.update', $token) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @foreach($rejectedDocuments as $document)
                <div class="document-card">
                    <div class="document-title">
                        {{ $document->type->getLabel() }}
                    </div>

                    @if($document->status == 3)
                    <div class="rejection-reason">
                        <strong>Motivo del rechazo:</strong>
                        {{ $document->rejection_reason ?? 'No especificado' }}
                    </div>
                    @endif

                    @if($document->expiration_date && $document->expiration_date < now())
                    <div class="expiration-warning">
                        <strong>Documento vencido:</strong>
                        Venció el {{ $document->expiration_date->format('d/m/Y') }}. Por favor, cargue un documento actualizado.
                    </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label" for="document_{{ $document->id }}">
                            Cargar nuevo documento *
                        </label>
                        <input 
                            type="file" 
                            id="document_{{ $document->id }}" 
                            name="document_{{ $document->id }}" 
                            class="form-input"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                        >
                        <div class="file-info">
                            Formatos permitidos: PDF, JPG, JPEG, PNG (Máximo 5MB)
                        </div>
                        @error("document_{$document->id}")
                            <div style="color: #dc2626; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($document->expiration_date)
                    <div class="form-group">
                        <label class="form-label" for="expiration_date_{{ $document->id }}">
                            Nueva fecha de vencimiento *
                        </label>
                        <input 
                            type="date" 
                            id="expiration_date_{{ $document->id }}" 
                            name="expiration_date_{{ $document->id }}" 
                            class="form-input"
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                            required
                        >
                        @error("expiration_date_{$document->id}")
                            <div style="color: #dc2626; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                </div>
                @endforeach

                <button type="submit" class="submit-btn">
                    Enviar Documentos Actualizados
                </button>
            </form>

            <div style="text-align: center; margin-top: 30px;">
                <img src="https://i.postimg.cc/nrx8v5gk/autogestion-paracas1.jpg" alt="Logo PDP Paracas" style="width: 100%; max-width: 600px; height: auto;">
            </div>
        </div>
    </div>
</body>
</html>
