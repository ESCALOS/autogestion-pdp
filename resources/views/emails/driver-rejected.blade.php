<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos de Conductor Rechazados</title>
    <style>
        .container {     
            max-width: 56rem;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #ef4444;
            color: white;
            padding: 20px 30px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .driver-info {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #ef4444;
        }
        .rejection-list {
            background-color: #fee;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .rejection-item {
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border-left: 3px solid #ef4444;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Documentos Requieren Corrección</h1>
        </div>
        <div class="content">
            <p>Estimado/a <strong>{{ $driver->full_name }}</strong>,</p>
            
            <p>Le informamos que su solicitud de registro como conductor requiere correcciones en los siguientes documentos:</p>
            
            <div class="driver-info">
                <strong>Datos del conductor:</strong><br>
                <strong>Nombre:</strong> {{ $driver->full_name }}<br>
                <strong>{{ $driver->document_type->getLabel() }}:</strong> {{ $driver->document_number }}<br>
                <strong>Empresa:</strong> {{ $driver->company->business_name ?? 'N/A' }}
            </div>
            
            <div class="rejection-list">
                <strong>Documentos rechazados:</strong>
                @foreach($rejectedDocuments as $document)
                <div class="rejection-item">
                    <strong>{{ $document['type'] }}</strong><br>
                    <em>Motivo:</em> {{ $document['reason'] ?? 'No especificado' }}
                </div>
                @endforeach
            </div>
            
            @if($appealUrl)
            <p>Para corregir y volver a enviar sus documentos, haga clic en el siguiente botón:</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $appealUrl }}" style="display: inline-block; background-color: #3b82f6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Enviar Documentos Corregidos
                </a>
            </div>
            
            <p style="font-size: 14px; color: #666;">
                <strong>Nota:</strong> Este enlace estará disponible por 30 días. Si tiene alguna duda, no dude en contactarnos.
            </p>
            @else
            <p>Por favor, ingrese al sistema para corregir y volver a enviar sus documentos.</p>
            @endif
            
            <p>Saludos cordiales,<br>
            <strong>Equipo de Registro de Transportes</strong></p>
            
            <div style="text-align: center; margin-top: 30px;">
                <img src="https://i.postimg.cc/nrx8v5gk/autogestion-paracas1.jpg" alt="Firma PDP Paracas" style="max-width: 100%; height: auto;">
            </div>
        </div>
        <div class="footer">
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
