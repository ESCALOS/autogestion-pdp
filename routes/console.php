<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('documents:check-expiring --days=15')
    ->dailyAt('00:00')
    ->onSuccess(function () {
        Log::info('Checked for documents expiring in 15 days.');
    })
    ->onFailure(function () {
        Log::error('Failed to check for documents expiring in 15 days.');
    })
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('documents:check-expiring --days=7')
    ->dailyAt('00:30')
    ->onSuccess(function () {
        Log::info('Checked for documents expiring in 7 days.');
    })
    ->onFailure(function () {
        Log::error('Failed to check for documents expiring in 7 days.');
    })
    ->withoutOverlapping()
    ->runInBackground();
