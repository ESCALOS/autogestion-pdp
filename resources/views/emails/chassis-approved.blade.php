<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Carreta Aprobada</title>
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
                background-color: #10b981;
                color: white;
                padding: 20px 30px;
                max-width: 56rem;
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
            .chassis-info {
                background-color: white;
                padding: 15px;
                margin: 20px 0;
                border-left: 4px solid #10b981;
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
                <h1>¡Felicitaciones!</h1>
            </div>
            <div class="content">
                <p>Estimado/a usuario,</p>

                <p>
                    Nos complace informarle que su registro de carreta ha sido
                    <strong>aprobado</strong>
                    exitosamente.
                </p>

                <div class="chassis-info">
                    <strong>Datos de la carreta:</strong>
                    <br />
                    <strong>Placa:</strong>
                    {{ $chassis->license_plate }}
                    <br />
                    <strong>Tipo de Vehículo:</strong>
                    {{ $chassis->vehicle_type ?? 'N/A' }}
                    <br />
                    <strong>Cantidad de Ejes:</strong>
                    {{ $chassis->axle_count ?? 'N/A' }}
                    <br />
                    <strong>Empresa:</strong>
                    {{ $chassis->company->business_name ?? 'N/A' }}
                </div>

                <p>
                    Todos los documentos han sido validados correctamente y la carreta ahora está activa en nuestro
                    sistema.
                </p>

                <p>Puede proceder a realizar sus actividades de transporte.</p>

                <p>
                    Saludos cordiales,
                    <br />
                    <strong>Equipo de Registro de Transportes</strong>
                </p>

                <div style="text-align: center; margin-top: 30px">
                    <img
                        src="https://images.iimg.live/images/ultimate-snap-9626.webp"
                        alt="Firma PDP Paracas"
                        style="max-width: 100%; height: auto"
                    />
                </div>
            </div>
            <div class="footer">
                <p>Este es un correo automático, por favor no responder.</p>
            </div>
        </div>
    </body>
</html>
