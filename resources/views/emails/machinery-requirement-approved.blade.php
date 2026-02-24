<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Requerimiento de Maquinaria</title>
    <style>
        .container {
            max-width: 56rem;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            justify-content: center;
            display: flex;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px 30px;
            max-width: 56rem;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            max-width: 56rem;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .footer {
            background-color: #f3f4f6;
            max-width: 56rem;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 5px 5px;
        }
        .requirement-details {
            background-color: white;
            padding: 20px;
            border-left: 4px solid #2563eb;
            margin: 20px 0;
            border-radius: 4px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white !important;
            padding: 14px 28px;
            text-decoration: none !important;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            border: 2px solid #2563eb;
        }
        .button:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Nuevo Requerimiento de Maquinaria!</h1>
        </div>

        <div class="content">
            <p>Hola {{ $supplier->business_name }},</p>

            <p>Le informamos que hay un nuevo requerimiento de maquinaria activo en nuestra plataforma que podría ser de su interés.</p>

            <div class="requirement-details">
                <div class="detail-row">
                    <span class="detail-label">Nave:</span>
                    <span class="detail-value">{{ $requirement->vessel_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tipo de Unidad:</span>
                    <span class="detail-value">{{ $unitType }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Unidades Solicitadas:</span>
                    <span class="detail-value">{{ $requirement->units_quantity }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Hora de Activación:</span>
                    <span class="detail-value">{{ $activationTime }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duración (días):</span>
                    <span class="detail-value">{{ $requirement->days_quantity }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Centro de Costos:</span>
                    <span class="detail-value">{{ $requirement->cost_center }}</span>
                </div>
            </div>

            <p>Si está interesado en participar en este requerimiento, por favor ingrese a la plataforma para más detalles y poder registrar su disponibilidad.</p>

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" style="display: inline-block; background-color: #2563eb; color: white; padding: 14px 28px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px; border: 2px solid #2563eb;">Ingresar a la Plataforma</a>
            </div>

            <p>Gracias por ser parte de nuestra red de proveedores de maquinaria.</p>

            <p>Saludos,<br>
            <strong>{{ config('app.name') }}</strong></p>
        </div>

        <div class="footer">
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
