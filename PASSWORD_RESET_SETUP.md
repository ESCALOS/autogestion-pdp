# Sistema de Restablecimiento de Contraseña

Este documento describe el sistema de restablecimiento de contraseña implementado en la aplicación.

## Características

✅ **Componentes Livewire**

- `ForgotPassword`: Solicitar enlace de restablecimiento
- `ResetPassword`: Restablecer contraseña con token

✅ **Validación Completa**

- Email válido y existente
- Contraseña mínima de 8 caracteres
- Confirmación de contraseña

✅ **Seguridad**

- Tokens únicos con expiración (60 minutos por defecto)
- Throttling de solicitudes (60 segundos entre intentos)
- Hash de contraseñas con bcrypt

✅ **Pruebas**

- Tests completos para ambos componentes
- Cobertura de casos exitosos y de error

## Rutas

```php
// Solicitar enlace de restablecimiento
GET /password/forgot → password.request

// Restablecer contraseña con token
GET /password/reset/{token}?email={email} → password.reset
```

## Uso

### 1. Solicitar Restablecimiento

El usuario ingresa a `/password/forgot` y proporciona su email. Si es válido, se envía un correo con el enlace.

### 2. Restablecer Contraseña

El usuario hace clic en el enlace del correo, ingresa su nueva contraseña y confirma el cambio.

### 3. Login

Después de restablecer exitosamente, se redirige al login para autenticarse con la nueva contraseña.

## Configuración de Correo

Para producción, configura las variables de entorno en `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicación
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

Para desarrollo, el mailer está configurado en `log` por defecto. Los correos se guardan en `storage/logs/laravel.log`.

## Personalización

### Cambiar tiempo de expiración del token

En `config/auth.php`:

```php
'passwords' => [
    'users' => [
        'expire' => 60, // minutos
        'throttle' => 60, // segundos
    ],
],
```

### Personalizar el email

Para personalizar el correo de restablecimiento, puedes usar:

```php
// En AppServiceProvider.php
use Illuminate\Auth\Notifications\ResetPassword;

ResetPassword::createUrlUsing(function ($user, string $token) {
    return url(route('password.reset', [
        'token' => $token,
        'email' => $user->email,
    ], false));
});
```

## Testing

Ejecutar las pruebas:

```bash
./vendor/bin/sail artisan test --filter=ForgotPasswordTest
./vendor/bin/sail artisan test --filter=ResetPasswordTest
```

## Integración con Login

El enlace "¿Olvidó su contraseña?" ya está agregado en la página de login y redirige a `/password/forgot`.
