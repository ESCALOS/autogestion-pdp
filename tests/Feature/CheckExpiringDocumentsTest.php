<?php

declare(strict_types=1);

use App\Enums\DocumentStatusEnum;
use App\Enums\EntityStatusEnum;
use App\Mail\ChassisDocumentsExpiringMail;
use App\Mail\DriverDocumentsExpiringMail;
use App\Mail\TruckDocumentsExpiringMail;
use App\Models\Chassis;
use App\Models\Company;
use App\Models\Document;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

it('sends notification when driver documents are expiring in 15 days', function () {
    $company = Company::factory()->create();
    $representative = User::factory()->create([
        'company_id' => $company->id,
        'is_company_representative' => true,
    ]);

    $driver = Driver::factory()->create([
        'company_id' => $company->id,
        'status' => EntityStatusEnum::ACTIVE,
    ]);

    Document::factory()->create([
        'documentable_type' => Driver::class,
        'documentable_id' => $driver->id,
        'status' => DocumentStatusEnum::APPROVED,
        'expiration_date' => now()->addDays(10)->toDateString(), // Dentro de los próximos 15 días
    ]);

    $this->artisan('documents:check-expiring', ['--days' => 15])
        ->assertSuccessful();

    Mail::assertQueued(DriverDocumentsExpiringMail::class, function ($mail) use ($representative) {
        return $mail->hasTo($representative->email);
    });
});

it('sends notification when truck documents are expiring in 7 days', function () {
    $company = Company::factory()->create();
    $representative = User::factory()->create([
        'company_id' => $company->id,
        'is_company_representative' => true,
    ]);

    $truck = Truck::factory()->create([
        'company_id' => $company->id,
        'status' => EntityStatusEnum::ACTIVE,
    ]);

    Document::factory()->create([
        'documentable_type' => Truck::class,
        'documentable_id' => $truck->id,
        'status' => DocumentStatusEnum::APPROVED,
        'expiration_date' => now()->addDays(5)->toDateString(), // Dentro de los próximos 7 días
    ]);

    $this->artisan('documents:check-expiring', ['--days' => 7])
        ->assertSuccessful();

    Mail::assertQueued(TruckDocumentsExpiringMail::class, function ($mail) use ($representative) {
        return $mail->hasTo($representative->email);
    });
});

it('sends notification when chassis documents are expiring', function () {
    $company = Company::factory()->create();
    $representative = User::factory()->create([
        'company_id' => $company->id,
        'is_company_representative' => true,
    ]);

    $chassis = Chassis::factory()->create([
        'company_id' => $company->id,
        'status' => EntityStatusEnum::ACTIVE,
    ]);

    Document::factory()->create([
        'documentable_type' => Chassis::class,
        'documentable_id' => $chassis->id,
        'status' => DocumentStatusEnum::APPROVED,
        'expiration_date' => now()->addDays(10)->toDateString(), // Dentro de los próximos 15 días
    ]);

    $this->artisan('documents:check-expiring', ['--days' => 15])
        ->assertSuccessful();

    Mail::assertQueued(ChassisDocumentsExpiringMail::class, function ($mail) use ($representative) {
        return $mail->hasTo($representative->email);
    });
});

it('does not send notification when documents expire after the specified days', function () {
    $company = Company::factory()->create();
    User::factory()->create([
        'company_id' => $company->id,
        'is_company_representative' => true,
    ]);

    $driver = Driver::factory()->create([
        'company_id' => $company->id,
        'status' => EntityStatusEnum::ACTIVE,
    ]);

    // Documento que vence en 30 días (fuera del rango de 15 días)
    Document::factory()->create([
        'documentable_type' => Driver::class,
        'documentable_id' => $driver->id,
        'status' => DocumentStatusEnum::APPROVED,
        'expiration_date' => now()->addDays(30)->toDateString(),
    ]);

    $this->artisan('documents:check-expiring', ['--days' => 15])
        ->assertSuccessful();

    Mail::assertNothingQueued();
});

it('does not send notification for rejected documents', function () {
    $company = Company::factory()->create();
    User::factory()->create([
        'company_id' => $company->id,
        'is_company_representative' => true,
    ]);

    $driver = Driver::factory()->create([
        'company_id' => $company->id,
        'status' => EntityStatusEnum::ACTIVE,
    ]);

    // Documento rechazado que vence en 15 días
    Document::factory()->create([
        'documentable_type' => Driver::class,
        'documentable_id' => $driver->id,
        'status' => DocumentStatusEnum::REJECTED,
        'expiration_date' => now()->addDays(15)->toDateString(),
    ]);

    $this->artisan('documents:check-expiring', ['--days' => 15])
        ->assertSuccessful();

    Mail::assertNothingQueued();
});

it('generates appeal token when sending notification', function () {
    $company = Company::factory()->create();
    User::factory()->create([
        'company_id' => $company->id,
        'is_company_representative' => true,
    ]);

    $driver = Driver::factory()->create([
        'company_id' => $company->id,
        'status' => EntityStatusEnum::ACTIVE,
        'appeal_token' => null,
    ]);

    Document::factory()->create([
        'documentable_type' => Driver::class,
        'documentable_id' => $driver->id,
        'status' => DocumentStatusEnum::APPROVED,
        'expiration_date' => now()->addDays(15)->toDateString(),
    ]);

    $this->artisan('documents:check-expiring', ['--days' => 15])
        ->assertSuccessful();

    $driver->refresh();

    expect($driver->appeal_token)->not->toBeNull();
    expect($driver->appeal_token_expires_at)->not->toBeNull();
});

it('runs check-all-expiring command successfully', function () {
    $this->artisan('documents:check-all-expiring')
        ->assertSuccessful();
});
