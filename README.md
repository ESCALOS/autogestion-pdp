# PDP AUTOGESTION

## Deployment

- Configure Project.
    - Update Composer Packages
    - Add Database Credentials
    - Add ASSET_PREFIX if deployed application in sub-folder
    - Link Storage

        ```fish
        php artisan storage:link
        ```

- Initialize Project

    ```fish
    php artisan project:init
    ```

- Update Permissions and Migrations
    - Whenever new Resource , Page or migration is Added Run update command to migrate and create permissions.
        ```fish
        php artisan project:update
        ```

- build vite assets

    ```fish
    bun install && bun run build
    ```

- Clear/Generate Cache

    ```fish
    php artisan project:cache
    ```

- Configure [Laravel Boost](https://github.com/laravel/boost)

    ```fish
    php artisan boost:install
    ```

## Docker (archivo `docker-compose.prod.yaml`)

El repositorio incluye un archivo de despliegue para producción: `docker-compose.prod.yaml`.
A continuación se resumen los servicios y puntos importantes:

- Servicios principales definidos:
    - `app`: contenedor PHP construido con el Dockerfile en `docker/php/Dockerfile`. Monta el proyecto en `/var/www/html`.
    - `pgsql`: PostgreSQL 17 (alpine). Usa la variable de entorno para configurar `POSTGRES_DB`, `POSTGRES_USER` y `POSTGRES_PASSWORD`. Persiste datos en el volumen `autogestion-pgsql`.
    - `nginx`: servidor web (imagen `nginx:stable`). Expone el puerto 8090 en el host (mapeo `8090:8090`) y monta `docker/nginx/default.conf` como configuración. También monta `docker/nginx/ssl` para certificados.
    - `certbot`: contenedor de Certbot para gestionar certificados; monta `docker/nginx/ssl` y `docker/nginx/www`.

- Variables de entorno importantes (defínalas en tu `.env`):
    - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (se usan para PostgreSQL).

- Volúmenes persistentes:
    - `autogestion-pgsql` → datos de PostgreSQL

- Puerto público por defecto para nginx en este archivo: 8090

- Archivos de configuración requeridos:
    - `docker/nginx/default.conf` (configuración de Nginx)
    - `docker/nginx/ssl/` (certificados TLS - manejados por `certbot`)

- Comando para desplegar usando específicamente este archivo (nota la opción `-f docker-compose.prod.yaml`):

    ```bash
    # usando la CLI moderna
    docker compose -f docker-compose.prod.yaml up -d --build

    # o, con la sintaxis legacy
    docker-compose -f docker-compose.prod.yaml up -d --build
    ```

    Usar `-f docker-compose.prod.yaml` asegura que Docker utilice el archivo de configuración de producción incluido en este repositorio.

- Recomendaciones:
    - Asegúrate de tener definidas las variables de entorno en tu archivo `.env` antes de levantar los servicios.
    - Verifica que `docker/nginx/default.conf` y los certificados en `docker/nginx/ssl` estén presentes y correctamente configurados.
    - Si no ves cambios de frontend en la UI, entra al contenedor con `docker exec -it app-prod bash` y ejecutar el comando `node --run build`.
    - Actualizar cache y rutas con `php artisan optimize:clear` y `php artisan route:clear`.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
