<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Middleware\RedirectAdminUsers;
use App\Livewire\Company\CreateCompany;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/
Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix').'/livewire/update', $handle)->name('custom-livewire.update');
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix').'/livewire/livewire.js', $handle);
});
/*
/ END
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/registrar-empresa', CreateCompany::class)
        ->name('company.create');
});

Route::middleware(['auth', RedirectAdminUsers::class])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');
});

require __DIR__.'/auth.php';
