<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apelación Enviada</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            text-align: center;
        }
        .header {
            background-color: #10b981;
            color: white;
            padding: 40px 30px;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .success-icon svg {
            width: 50px;
            height: 50px;
            color: #10b981;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px 30px;
        }
        .content p {
            color: #374151;
            line-height: 1.8;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .info-box {
            background-color: #dbeafe;
            padding: 20px;
            border-radius: 6px;
            margin: 30px 0;
            border-left: 4px solid #3b82f6;
        }
        .info-box p {
            color: #1e40af;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1>¡Documentos Enviados Exitosamente!</h1>
        </div>

        <div class="content">
            <p>Sus documentos corregidos han sido recibidos correctamente.</p>

            <p>Su solicitud ha vuelto a estado <strong>PENDIENTE</strong> y será revisada nuevamente por nuestro equipo.</p>

            <div class="info-box">
                <p><strong>Le notificaremos por correo electrónico</strong> cuando se complete la revisión de sus documentos.</p>
            </div>

            <p>Gracias por su colaboración.</p>

            <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
                Este enlace de apelación ya no está disponible.
            </p>
        </div>
    </div>
</body>
</html>
