<?php

declare(strict_types=1);

use App\Enums\CompanyDocumentStatusEnum;
use App\Livewire\Company\AppealForm;
use App\Models\Company;
use App\Models\CompanyDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    Storage::fake('local');
});

it('loads company data correctly with valid token', function () {
    $token = Str::random(64);

    $company = Company::factory()
        ->has(
            CompanyDocument::factory()
                ->rejected()
                ->state(['rejection_reason' => 'Documento ilegible']),
            'documents'
        )
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->addDays(30),
        ]);

    Livewire::test(AppealForm::class, ['token' => $token])
        ->assertSet('company.id', $company->id)
        ->assertSee($company->business_name)
        ->assertSee('Documento ilegible')
        ->assertSuccessful();
});

it('aborts with invalid token', function () {
    Livewire::test(AppealForm::class, ['token' => 'invalid-token'])
        ->assertStatus(404);
});

it('aborts with expired token', function () {
    $token = Str::random(64);

    Company::factory()
        ->has(CompanyDocument::factory()->rejected(), 'documents')
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->subDay(),
        ]);

    Livewire::test(AppealForm::class, ['token' => $token])
        ->assertStatus(404);
});

it('aborts when no rejected documents exist', function () {
    $token = Str::random(64);

    Company::factory()
        ->has(CompanyDocument::factory()->approved(), 'documents')
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->addDays(30),
        ]);

    Livewire::test(AppealForm::class, ['token' => $token])
        ->assertStatus(404);
});

it('submits appeal successfully', function () {
    $token = Str::random(64);

    $company = Company::factory()
        ->rejected()
        ->has(
            CompanyDocument::factory()
                ->rejected()
                ->state(['rejection_reason' => 'Documento ilegible']),
            'documents'
        )
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->addDays(30),
        ]);

    $document = $company->documents()->first();
    $file = UploadedFile::fake()->create('documento.pdf', 1024, 'application/pdf');

    Livewire::test(AppealForm::class, ['token' => $token])
        ->set("data.document_{$document->id}", $file)
        ->call('submit')
        ->assertSet('success', true)
        ->assertNotified();

    assertDatabaseHas('company_documents', [
        'id' => $document->id,
        'status' => CompanyDocumentStatusEnum::PENDIENTE->value,
        'rejection_reason' => null,
    ]);

    assertDatabaseHas('companies', [
        'id' => $company->id,
        'status' => 1,
        'appeal_token' => null,
    ]);
});

it('displays success view after submission', function () {
    $token = Str::random(64);

    $company = Company::factory()
        ->rejected()
        ->has(
            CompanyDocument::factory()
                ->rejected()
                ->state(['rejection_reason' => 'Documento ilegible']),
            'documents'
        )
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->addDays(30),
        ]);

    $document = $company->documents()->first();
    $file = UploadedFile::fake()->create('documento.pdf', 1024, 'application/pdf');

    Livewire::test(AppealForm::class, ['token' => $token])
        ->set("data.document_{$document->id}", $file)
        ->call('submit')
        ->assertSee('¡Documentos Enviados Exitosamente!')
        ->assertSee('PENDIENTE');
});
