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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
