<?php

declare(strict_types=1);

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Company\CreateCompany;
use App\Livewire\Supplier\Auth\Create as CreateSupplier;
use App\Livewire\Supplier\Auth\Login as SupplierLogin;
use App\Livewire\Supplier\Auth\VerifyPhoneCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    // Company routes
    Route::get('login', Login::class)
        ->name('login');

    Route::get('password/forgot', ForgotPassword::class)
        ->name('password.request');

    Route::get('password/reset/{token}', ResetPassword::class)
        ->name('password.reset');

    Route::get('/registrar-empresa', CreateCompany::class)
        ->name('company.create');

    // Supplier routes
    Route::get('/proveedores/login', SupplierLogin::class)
        ->name('supplier.login');

    Route::get('/registrar-proveedor', CreateSupplier::class)
        ->name('supplier.create');

    Route::get('/proveedores/verificar-telefono/{supplier}', VerifyPhoneCode::class)
        ->name('supplier.verify-phone');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
