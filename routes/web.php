<?php

declare(strict_types=1);

use App\Http\Controllers\CompanyAppealController;
use App\Http\Controllers\DriverAppealController;
use App\Livewire\Company\CreateCompany;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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



Route::middleware(['auth'])->group(function () {
    Route::get('/company/document/{document}', function (App\Models\CompanyDocument $document) {
        // Verificar que el usuario tenga permiso para ver este documento
        if (Auth::user()->hasRole('super_admin') || Auth::user()->company_id === $document->company_id) {
            // Generar URL temporal de S3 válida por 5 minutos
            $temporaryUrl = Storage::disk('s3')->temporaryUrl(
                $document->path,
                now()->addMinutes(5)
            );

            return redirect($temporaryUrl);
        }

        abort(403);
    })
    ->name('company.document.view');

    Route::get('/truck/document/{document}', function(Document $document) {
        // Verificar que el usuario tenga permiso para ver este documento
        if (Auth::user()->hasRole('super_admin') || Auth::user()->company_id === $document->company_id) {
            // Generar URL temporal de S3 válida por 5 minutos
            $temporaryUrl = Storage::disk('s3')->temporaryUrl(
                $document->path,
                now()->addMinutes(5)
            );

            return redirect($temporaryUrl);
        }

        abort(403);
    })
    ->name('truck.document.view');

    Route::get('/driver/document/{document}', function(Document $document) {
        // Verificar que el usuario tenga permiso para ver este documento
        if (Auth::user()->hasRole('super_admin') || Auth::user()->company_id === $document->company_id) {
            // Generar URL temporal de S3 válida por 5 minutos
            $temporaryUrl = Storage::disk('s3')->temporaryUrl(
                $document->path,
                now()->addMinutes(5)
            );

            return redirect($temporaryUrl);
        }

        abort(403);
    })
    ->name('driver.document.view');
});

// Rutas públicas para apelación de empresas rechazadas
Route::get('/company/appeal/{token}', [CompanyAppealController::class, 'show'])
    ->name('company.appeal.show');
Route::put('/company/appeal/{token}', [CompanyAppealController::class, 'update'])
    ->name('company.appeal.update');
Route::get('/company/appeal-success', [CompanyAppealController::class, 'success'])
    ->name('company.appeal.success');

// Rutas públicas para actualización de documentos de conductores
Route::get('/driver/appeal/{token}', [DriverAppealController::class, 'show'])
    ->name('driver.appeal.show');
Route::put('/driver/appeal/{token}', [DriverAppealController::class, 'update'])
    ->name('driver.appeal.update');
Route::get('/driver/appeal-success', [DriverAppealController::class, 'success'])
    ->name('driver.appeal.success');

require __DIR__.'/auth.php';
require __DIR__.'/dashboard.php';
