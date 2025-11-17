<?php

declare(strict_types=1);

use App\Livewire\Company\AppealForm as CompanyAppealForm;
use App\Livewire\Driver\AppealForm as DriverAppealForm;
use App\Livewire\Truck\AppealForm as TruckAppealForm;
use Illuminate\Support\Facades\Route;

// Apelaciones de empresas
Route::get('/company/appeal/{token}', CompanyAppealForm::class)
    ->name('company.appeal.show');

// Apelaciones de conductores
Route::get('/driver/appeal/{token}', DriverAppealForm::class)
    ->name('driver.appeal.show');

// Apelaciones de camiones
Route::get('/truck/appeal/{token}', TruckAppealForm::class)
    ->name('truck.appeal.show');
