<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Middleware\RedirectAdminUsers;
use App\Livewire\Chassis\CreateChassis;
use App\Livewire\Driver\CreateDriver;
use App\Livewire\Truck\CreateTruck;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', RedirectAdminUsers::class])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    // Conductores
    Route::prefix('conductores')->group(function () {
        Route::view('/', 'drivers')->name('drivers.index');
        Route::get('/create', CreateDriver::class)
            ->name('drivers.create');
    });

    // Camiones
    Route::prefix('camiones')->group(function () {
        Route::view('/', 'trucks')->name('trucks.index');
        Route::get('/create', CreateTruck::class)->name('trucks.create');
    });

    // Chassis
    Route::prefix('chassis')->group(function () {
        Route::view('/', 'chassis')->name('chassis.index');
        Route::get('/create', CreateChassis::class)->name('chassis.create');
    });

});
