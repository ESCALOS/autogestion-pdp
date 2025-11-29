#!/bin/bash

# Script para configurar las tareas cron para verificación de documentos próximos a vencer
# Este script debe ejecutarse en el servidor de producción

echo "Configurando tareas cron para verificación de documentos..."

# Crear el archivo de tareas cron
cat > /tmp/laravel_scheduler << 'EOF'
# Laravel Scheduler - Ejecutar cada minuto para procesar tareas programadas
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1

# Tareas específicas para verificación de documentos (como backup)
# Verificar documentos que vencen en 15 días - diariamente a las 8:00 AM
0 8 * * * cd /path/to/your/project && php artisan documents:check-expiring --days=15 >> /var/log/laravel-scheduler.log 2>&1

# Verificar documentos que vencen en 7 días - diariamente a las 9:00 AM
0 9 * * * cd /path/to/your/project && php artisan documents:check-expiring --days=7 >> /var/log/laravel-scheduler.log 2>&1
EOF

echo "Archivo de configuración cron creado en /tmp/laravel_scheduler"
echo ""
echo "Para aplicar la configuración, ejecuta los siguientes comandos como usuario apropiado:"
echo "1. Reemplaza '/path/to/your/project' con la ruta real del proyecto"
echo "2. sed -i 's|/path/to/your/project|$(pwd)|g' /tmp/laravel_scheduler"
echo "3. crontab /tmp/laravel_scheduler"
echo ""
echo "Para verificar que se aplicó correctamente:"
echo "crontab -l"
echo ""
echo "Para desarrollo con Sail, usa:"
echo "./vendor/bin/sail artisan documents:check-expiring --days=15"
echo "./vendor/bin/sail artisan documents:check-expiring --days=7"
echo "./vendor/bin/sail artisan documents:check-all-expiring"
