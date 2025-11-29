<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withSchedule(function ($schedule): void {
        // Verificar documentos que vencen en 15 días - ejecutar diariamente a las 8:00 AM
        $schedule->command('documents:check-expiring --days=15')
            ->dailyAt('06:26')
            ->name('check-expiring-documents-15-days')
            ->description('Verificar documentos que vencen en 15 días');

        // Verificar documentos que vencen en 7 días - ejecutar diariamente a las 9:00 AM
        $schedule->command('documents:check-expiring --days=7')
            ->dailyAt('06:26')
            ->name('check-expiring-documents-7-days')
            ->description('Verificar documentos que vencen en 7 días');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
