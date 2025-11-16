<?php

declare(strict_types=1);

use App\Livewire\Company\AppealForm;
use Illuminate\Support\Facades\Route;

Route::get('/company/appeal/{token}', AppealForm::class)
    ->name('company.appeal.show');
