<?php

declare(strict_types=1);

use App\Http\Controllers\DocumentController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/company/document/{document}', [DocumentController::class, 'company'])
        ->name('company.document.view');

    Route::get('/entity/document/{document}', [DocumentController::class, 'entity'])
        ->name('entity.document.view');
});

require __DIR__.'/auth.php';
require __DIR__.'/dashboard.php';
require __DIR__.'/appeals.php';
