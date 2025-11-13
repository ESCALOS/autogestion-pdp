<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
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
            background-color: #2563eb;
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
        .user-info {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #2563eb;
        }
        .warning-box {
            background-color: #fef3c7;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #f59e0b;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #1d4ed8;
            color: white !important;
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
            <h1>Solicitud de Restablecimiento de Contraseña</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $user->name }} {{ $user->last_name }}</strong>,</p>
            
            <p>Hemos recibido una solicitud para restablecer la contraseña de su cuenta en el Sistema de AutoGestión Paracas.</p>
            
            <div class="user-info">
                <strong>Correo electrónico:</strong> {{ $user->email }}
            </div>
            
            <p>Para restablecer su contraseña, haga clic en el siguiente botón:</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $resetUrl }}" style="display: inline-block; background-color: #2563eb; color: #ffffff; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Restablecer Contraseña
                </a>
            </div>
            
            <div class="warning-box">
                <strong>⚠️ Importante:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Este enlace estará disponible por <strong>60 minutos</strong>.</li>
                    <li>Si no solicitó este cambio, ignore este correo.</li>
                    <li>Por seguridad, no comparta este enlace con nadie.</li>
                </ul>
            </div>
            
            <p style="font-size: 14px; color: #666; margin-top: 20px;">
                Si el botón no funciona, copie y pegue el siguiente enlace en su navegador:
            </p>
            <p style="font-size: 12px; color: #2563eb; word-break: break-all;">
                {{ $resetUrl }}
            </p>
            
            <p style="margin-top: 30px;">Saludos cordiales,<br>
            <strong>Sistema de AutoGestión Paracas</strong></p>
        </div>
        
        <div class="footer">
            <p>Este es un correo automático, por favor no responda a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Sistema de AutoGestión Paracas. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
