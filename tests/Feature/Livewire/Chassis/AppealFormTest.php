<?php

declare(strict_types=1);

use App\Enums\DocumentStatusEnum;
use App\Enums\EntityStatusEnum;
use App\Livewire\Chassis\AppealForm;
use App\Models\Chassis;
use App\Models\Company;
use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    Storage::fake('s3');
});

it('displays chassis appeal form with rejected documents', function () {
    $company = Company::factory()->approved()->create(['ruc' => '20123456789']);
    $chassis = Chassis::factory()->rejected()->create(['company_id' => $company->id, 'license_plate' => 'ABC-123']);

    Document::factory()->rejected()->create([
        'documentable_type' => Chassis::class,
        'documentable_id' => $chassis->id,
        'path' => 'EMPRESAS/20123456789/CHASSIS/ABC-123/TARJETA_DE_PROPIEDAD.pdf',
        'rejection_reason' => 'Documento ilegible',
    ]);

    Livewire::test(AppealForm::class, ['token' => $chassis->appeal_token])
        ->assertSee('Apelación de Documentos - Chassis')
        ->assertSee($chassis->license_plate)
        ->assertSee($company->business_name)
        ->assertSee('Documentos Rechazados o Vencidos')
        ->assertSee('Motivo de rechazo: Documento ilegible');
});

it('displays expired documents in chassis appeal form', function () {
    $company = Company::factory()->approved()->create(['ruc' => '20123456789']);
    $chassis = Chassis::factory()->rejected()->create(['company_id' => $company->id, 'license_plate' => 'ABC-123']);

    Document::factory()->expired()->create([
        'documentable_type' => Chassis::class,
        'documentable_id' => $chassis->id,
        'path' => 'EMPRESAS/20123456789/CHASSIS/ABC-123/SOAT.pdf',
        'expiration_date' => now()->subDays(5),
    ]);

    Livewire::test(AppealForm::class, ['token' => $chassis->appeal_token])
        ->assertSee('Apelación de Documentos - Chassis')
        ->assertSee('Documento vencido el:');
});

it('allows uploading new document for rejected chassis document', function () {
    $company = Company::factory()->approved()->create(['ruc' => '20123456789']);
    $chassis = Chassis::factory()->rejected()->create(['company_id' => $company->id, 'license_plate' => 'ABC-123']);

    $document = Document::factory()->rejected()->create([
        'documentable_type' => Chassis::class,
        'documentable_id' => $chassis->id,
        'path' => 'EMPRESAS/20123456789/CHASSIS/ABC-123/TARJETA_DE_PROPIEDAD.pdf',
        'rejection_reason' => 'Documento ilegible',
    ]);

    $newFile = UploadedFile::fake()->create('tarjeta_nueva.pdf', 1024);

    Livewire::test(AppealForm::class, ['token' => $chassis->appeal_token])
        ->fillForm([
            "document_{$document->id}" => [$newFile],
        ])
        ->call('submit')
        ->assertNotified();

    assertDatabaseHas('documents', [
        'id' => $document->id,
        'status' => DocumentStatusEnum::PENDING->value,
        'rejection_reason' => null,
    ]);

    assertDatabaseHas('chassis', [
        'id' => $chassis->id,
        'status' => EntityStatusEnum::DOCUMENT_REVIEW->value,
        'appeal_token' => null,
        'appeal_token_expires_at' => null,
    ]);
});

it('allows updating expiration date for expired chassis document', function () {
    $company = Company::factory()->approved()->create(['ruc' => '20123456789']);
    $chassis = Chassis::factory()->rejected()->create(['company_id' => $company->id, 'license_plate' => 'ABC-123']);

    $document = Document::factory()->expired()->create([
        'documentable_type' => Chassis::class,
        'documentable_id' => $chassis->id,
        'path' => 'EMPRESAS/20123456789/CHASSIS/ABC-123/SOAT.pdf',
        'expiration_date' => now()->subDays(5),
    ]);

    $newFile = UploadedFile::fake()->create('soat_nuevo.pdf', 1024);
    $newExpirationDate = now()->addMonths(6);

    Livewire::test(AppealForm::class, ['token' => $chassis->appeal_token])
        ->fillForm([
            "document_{$document->id}" => [$newFile],
            "expiration_date_{$document->id}" => $newExpirationDate->format('Y-m-d'),
        ])
        ->call('submit')
        ->assertNotified();

    $document->refresh();

    expect($document->status)->toBe(DocumentStatusEnum::PENDING)
        ->and($document->expiration_date->format('Y-m-d'))->toBe($newExpirationDate->format('Y-m-d'))
        ->and($document->rejection_reason)->toBeNull();
});

it('rejects invalid token for chassis appeal', function () {
    Livewire::test(AppealForm::class, ['token' => 'invalid-token'])
        ->assertNotFound();
});

it('rejects expired token for chassis appeal', function () {
    $chassis = Chassis::factory()->create([
        'status' => EntityStatusEnum::REJECTED,
        'appeal_token' => 'expired-token',
        'appeal_token_expires_at' => now()->subDay(),
    ]);

    Livewire::test(AppealForm::class, ['token' => $chassis->appeal_token])
        ->assertNotFound();
});

it('rejects chassis without rejected or expired documents', function () {
    $chassis = Chassis::factory()->rejected()->create();

    Document::factory()->approved()->create([
        'documentable_type' => Chassis::class,
        'documentable_id' => $chassis->id,
    ]);

    Livewire::test(AppealForm::class, ['token' => $chassis->appeal_token])
        ->assertNotFound();
});

it('displays success message after submitting chassis appeal', function () {
    $company = Company::factory()->approved()->create(['ruc' => '20123456789']);
    $chassis = Chassis::factory()->rejected()->create(['company_id' => $company->id, 'license_plate' => 'ABC-123']);

    $document = Document::factory()->rejected()->create([
        'documentable_type' => Chassis::class,
        'documentable_id' => $chassis->id,
        'path' => 'EMPRESAS/20123456789/CHASSIS/ABC-123/TARJETA_DE_PROPIEDAD.pdf',
        'rejection_reason' => 'Documento ilegible',
    ]);

    $newFile = UploadedFile::fake()->create('tarjeta_nueva.pdf', 1024);

    Livewire::test(AppealForm::class, ['token' => $chassis->appeal_token])
        ->fillForm([
            "document_{$document->id}" => [$newFile],
        ])
        ->call('submit')
        ->assertSet('success', true)
        ->assertSee('Documentos Enviados Exitosamente')
        ->assertSee($chassis->license_plate);
});
