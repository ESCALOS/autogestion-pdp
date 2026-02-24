<?php

declare(strict_types=1);

use App\Http\Controllers\SupplierDashboardController;
use App\Livewire\Supplier\Drivers\Create as CreateDriver;
use App\Livewire\Supplier\Drivers\Index as DriversIndex;
use App\Livewire\Supplier\Machinery\Create as CreateMachinery;
use App\Livewire\Supplier\Machinery\Index as MachineryIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:supplier')->group(function () {
    Route::get('/proveedor/dashboard', SupplierDashboardController::class)
        ->name('supplier.dashboard');

    Route::prefix('proveedor/conductores')->group(function () {
        Route::get('/', DriversIndex::class)
            ->name('supplier.drivers.index');

        Route::get('/create', CreateDriver::class)
            ->name('supplier.drivers.create');
    });

    Route::prefix('proveedor/maquinaria')->group(function () {
        Route::get('/', MachineryIndex::class)
            ->name('supplier.machinery.index');

        Route::get('/create', CreateMachinery::class)
            ->name('supplier.machinery.create');
    });

    Route::post('/proveedor/logout', function () {
        Auth::guard('supplier')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('supplier.login');
    })->name('supplier.logout');
});
