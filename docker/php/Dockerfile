FROM ubuntu:24.04

LABEL maintainer="Carlos Escate"

ENV DEBIAN_FRONTEND=noninteractive

# -----------------------------------------
# 1. Actualizar base
# -----------------------------------------
RUN apt update && apt upgrade -y

# -----------------------------------------
# 2. Instalar dependencias del sistema
# -----------------------------------------
RUN apt install -y \
    software-properties-common \
    curl \
    zip \
    unzip \
    git \
    supervisor \
    cron \
    ca-certificates \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libssl-dev \
    pkg-config

# -----------------------------------------
# 3. Instalar PHP 8.4 (PPA)
# -----------------------------------------
RUN add-apt-repository ppa:ondrej/php -y && \
    apt update && \
    apt install -y \
        php8.4 \
        php8.4-fpm \
        php8.4-cli \
        php8.4-common \
        php8.4-mbstring \
        php8.4-xml \
        php8.4-pgsql \
        php8.4-curl \
        php8.4-zip \
        php8.4-gd \
        php8.4-intl \
        php8.4-bcmath \
        php8.4-redis

# Eliminar xdebug si aparece en el PPA
RUN apt purge -y php8.4-xdebug || true

# -----------------------------------------
# 4. Instalar Composer
# -----------------------------------------
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

# -----------------------------------------
# 5. Crear directorios
# -----------------------------------------
RUN mkdir -p /var/www/html \
    && mkdir -p /run/php \
    && mkdir -p /var/log/supervisor

WORKDIR /var/www/html

# -----------------------------------------
# 6. Copiar código de Laravel
# -----------------------------------------
COPY . /var/www/html

# -----------------------------------------
# 7. Instalar dependencias Laravel
# -----------------------------------------
RUN composer install --optimize-autoloader --no-dev

# -----------------------------------------
# 8. Permisos
# -----------------------------------------
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage/app/private/filament_exports

# -----------------------------------------
# 8.1 Instalar Cron Job para Schedule
# -----------------------------------------

# Crear cronjob para el schedule
RUN echo "* * * * * www-data /usr/bin/php /var/www/html/artisan schedule:run >> /dev/null 2>&1" \
    > /etc/cron.d/laravel

# Permisos correctos y registrar crontab
RUN chmod 0644 /etc/cron.d/laravel && crontab /etc/cron.d/laravel

# -----------------------------------------
# 9. Copiar supervisor config
# -----------------------------------------
COPY docker/php/supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# -----------------------------------------
# 10. Copiar configuraciones php.ini & opcache
# -----------------------------------------
COPY docker/php/php.ini /etc/php/8.4/fpm/php.ini
COPY docker/php/opcache.ini /etc/php/8.4/mods-available/opcache.ini

# -----------------------------------------
# 10.1 Configurar PHP-FPM para escuchar en 9000
# -----------------------------------------
COPY docker/php/www.conf /etc/php/8.4/fpm/pool.d/www.conf

# -----------------------------------------
# 11. Exponer PHP-FPM
# -----------------------------------------
EXPOSE 9000

# -----------------------------------------
# 12. Comando final
# -----------------------------------------
CMD ["/usr/bin/supervisord", "-n"]
