# Sistema de Notificaciones de Documentos Próximos a Vencer

Este sistema notifica automáticamente a los representantes de las empresas cuando los documentos de sus conductores, vehículos o chassis están próximos a vencer.

## Funcionalidades

### 1. Comandos Artisan

#### `documents:check-expiring`

Verifica documentos próximos a vencer y envía notificaciones por correo.

**Uso:**

```bash
# Verificar documentos que vencen en 15 días
php artisan documents:check-expiring --days=15

# Verificar documentos que vencen en 7 días
php artisan documents:check-expiring --days=7

# Con Sail
./vendor/bin/sail artisan documents:check-expiring --days=15
./vendor/bin/sail artisan documents:check-expiring --days=7
```

#### `documents:check-all-expiring`

Ejecuta ambas verificaciones (15 y 7 días) secuencialmente.

**Uso:**

```bash
php artisan documents:check-all-expiring

# Con Sail
./vendor/bin/sail artisan documents:check-all-expiring
```

### 2. Scheduler Automático

El sistema está configurado para ejecutar automáticamente las verificaciones:

- **8:00 AM diariamente**: Verificación de documentos que vencen en 15 días
- **9:00 AM diariamente**: Verificación de documentos que vencen en 7 días

### 3. Notificaciones por Correo

Se envían tres tipos de correos electrónicos:

#### Para Conductores (`DriverDocumentsExpiringMail`)

- Lista conductores con documentos próximos a vencer
- Incluye información del conductor y documentos afectados
- Proporciona enlaces de apelación para actualizar documentos

#### Para Vehículos (`TruckDocumentsExpiringMail`)

- Lista vehículos con documentos próximos a vencer
- Incluye placa, tipo de vehículo y documentos afectados
- Proporciona enlaces de apelación para actualizar documentos

#### Para Chassis (`ChassisDocumentsExpiringMail`)

- Lista chassis con documentos próximos a vencer
- Incluye placa, tipo de vehículo y documentos afectados
- Proporciona enlaces de apelación para actualizar documentos

### 4. Sistema de Apelaciones

Cuando se detectan documentos próximos a vencer:

1. Se genera un token de apelación único para cada entidad
2. El token expira en 30 días
3. Se envía un enlace de actualización por correo
4. Los representantes pueden acceder directamente para actualizar documentos

## Configuración

### Desarrollo (Laravel Sail)

Los comandos están listos para usar con Sail:

```bash
./vendor/bin/sail artisan documents:check-expiring --days=15
./vendor/bin/sail artisan documents:check-expiring --days=7
./vendor/bin/sail artisan documents:check-all-expiring
```

### Producción

1. **Configurar el Scheduler de Laravel:**
   El scheduler ya está configurado en `bootstrap/app.php`

2. **Configurar Cron Job:**
   Agregar al crontab del servidor:

    ```bash
    * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
    ```

3. **Script de configuración automática:**
    ```bash
    ./setup-document-scheduler.sh
    ```

### Variables de Entorno

Asegúrate de tener configuradas las variables de correo en `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME="Sistema PDP"
```

## Criterios de Verificación

### Documentos Incluidos

- Solo documentos con estado `APPROVED` (aprobados)
- Documentos con fecha de vencimiento exacta en X días
- Pertenecientes a entidades activas

### Notificaciones

- Se envían al email del representante de la empresa
- Se agrupan por empresa (un correo por empresa)
- Incluyen todos los conductores/vehículos/chassis afectados de esa empresa

### Tokens de Apelación

- Se generan automáticamente al detectar documentos próximos a vencer
- Permiten acceso directo sin autenticación para actualizar documentos
- Expiran en 30 días desde su generación

## Logs

El sistema registra:

- Notificaciones enviadas exitosamente
- Errores al enviar correos
- Empresas sin representante con email
- Estadísticas de verificación (cantidad de documentos/entidades procesadas)

Los logs se almacenan en:

- **Laravel Log**: `storage/logs/laravel.log`
- **Scheduler Log** (producción): `/var/log/laravel-scheduler.log`

## Pruebas

Para probar el sistema en desarrollo:

```bash
# Crear documentos de prueba que vencen en fechas específicas
# Ejecutar comandos manualmente
./vendor/bin/sail artisan documents:check-expiring --days=15

# Verificar logs
./vendor/bin/sail artisan tail
```

## Estructura de Archivos

```
app/
├── Console/Commands/
│   ├── CheckExpiringDocuments.php       # Comando principal
│   └── CheckAllExpiringDocuments.php    # Comando para ambas verificaciones
├── Mail/
│   ├── DriverDocumentsExpiringMail.php  # Mail para conductores
│   ├── TruckDocumentsExpiringMail.php   # Mail para vehículos
│   └── ChassisDocumentsExpiringMail.php # Mail para chassis
resources/views/emails/
├── drivers-documents-expiring.blade.php  # Vista para conductores
├── trucks-documents-expiring.blade.php   # Vista para vehículos
└── chassis-documents-expiring.blade.php  # Vista para chassis
```

## Mantenimiento

### Verificar Estado del Scheduler

```bash
php artisan schedule:list
```

### Ejecutar Scheduler Manualmente

```bash
php artisan schedule:run
```

### Ver Queue Jobs (si se usa cola)

```bash
php artisan queue:work
```
