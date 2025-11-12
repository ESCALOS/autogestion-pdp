<?php

declare(strict_types=1);


use App\Http\Middleware\RedirectAdminUsers;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Drivers\CreateDriver;

Route::middleware(['auth', RedirectAdminUsers::class])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    //Drivers

    Route::prefix('drivers')->group(function () {
        Route::view('/', 'drivers')->name('drivers.index');
        Route::get('drivers/create', CreateDriver::class)
            ->name('drivers.create');
    });
});
