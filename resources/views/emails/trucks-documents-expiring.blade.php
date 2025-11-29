<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Documentos de Vehículos Próximos a Vencer</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .header {
                background-color: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
            }
            .alert {
                background-color: #fff3cd;
                border: 1px solid #ffeaa7;
                color: #856404;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .truck-item {
                background-color: #f8f9fa;
                padding: 15px;
                margin-bottom: 15px;
                border-radius: 5px;
                border-left: 4px solid #ffc107;
            }
            .document-list {
                margin-top: 10px;
            }
            .document-item {
                background-color: #fff;
                padding: 8px;
                margin: 5px 0;
                border-radius: 3px;
                border-left: 3px solid #dc3545;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background-color: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin: 10px 5px;
            }
            .btn-warning {
                background-color: #ffc107;
                color: #212529;
            }
            .footer {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #dee2e6;
                font-size: 12px;
                color: #6c757d;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>🚛 Documentos de Vehículos Próximos a Vencer</h1>
            <p>
                <strong>Empresa:</strong>
                {{ $company->business_name }}
            </p>
            <p>
                <strong>RUC:</strong>
                {{ $company->ruc }}
            </p>
        </div>

        <div class="alert">
            <strong>⚠️ Atención:</strong>
            Los siguientes vehículos tienen documentos que vencen en {{ $daysToExpiration }} días. Es necesario renovar
            estos documentos para evitar interrupciones en las operaciones.
        </div>

        @foreach ($trucks as $truck)
            <div class="truck-item">
                <h3>🚛 Placa: {{ $truck['license_plate'] }}</h3>
                <p>
                    <strong>Tipo:</strong>
                    {{ App\Enums\TruckTypeEnum::tryFrom($truck['truck_type'])->getLabel() }}
                </p>
                <p>
                    <strong>Nacionalidad:</strong>
                    {{ $truck['nationality'] }}
                </p>
                <p>
                    <strong>Interno:</strong>
                    {{ $truck['is_internal'] ? 'Sí' : 'No' }}
                </p>

                <div class="document-list">
                    <h4>Documentos que vencen:</h4>
                    @foreach ($truck['documents'] as $document)
                        <div class="document-item">
                            <strong>{{ App\Enums\DocumentTypeEnum::tryFrom($document['type'])->getLabel() }}</strong>
                            <br />
                            <small>
                                Vence el: {{ \Carbon\Carbon::parse($document['expiration_date'])->format('d/m/Y') }}
                            </small>
                        </div>
                    @endforeach
                </div>

                @if (isset($truck['appeal_token']))
                    <p style="margin-top: 15px">
                        <a href="{{ route('truck.appeal.show', $truck['appeal_token']) }}" class="btn btn-warning">
                            🔄 Actualizar Documentos del Vehículo
                        </a>
                    </p>
                @endif
            </div>
        @endforeach

        <div style="background-color: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0">
            <h3>📝 Acciones Requeridas:</h3>
            <ul>
                <li>Revisar los documentos que están próximos a vencer</li>
                <li>Preparar la documentación actualizada</li>
                <li>Hacer clic en los enlaces de actualización para cada vehículo</li>
                <li>Subir los nuevos documentos antes de la fecha de vencimiento</li>
            </ul>
        </div>

        <div class="footer">
            <p>Este es un mensaje automático del sistema de gestión de documentos.</p>
            <p>Si tiene alguna consulta, póngase en contacto con el administrador del sistema.</p>
            <p><small>Fecha de envío: {{ now()->format('d/m/Y H:i') }}</small></p>
        </div>
    </body>
</html>
