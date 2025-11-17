<?php

declare(strict_types=1);

use App\Enums\DocumentStatusEnum;
use App\Livewire\Truck\AppealForm;
use App\Models\Document;
use App\Models\Truck;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    Storage::fake('s3');
});

it('loads truck data correctly with valid token', function () {
    $token = Str::random(64);

    $truck = Truck::factory()
        ->has(
            Document::factory()
                ->rejected()
                ->state(['rejection_reason' => 'Documento ilegible']),
            'documents'
        )
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->addDays(30),
        ]);

    Livewire::test(AppealForm::class, ['token' => $token])
        ->assertSet('truck.id', $truck->id)
        ->assertSee($truck->license_plate)
        ->assertSee('Documento ilegible')
        ->assertSuccessful();
});

it('aborts with invalid token', function () {
    Livewire::test(AppealForm::class, ['token' => 'invalid-token'])
        ->assertStatus(404);
});

it('aborts with expired token', function () {
    $token = Str::random(64);

    Truck::factory()
        ->has(Document::factory()->rejected(), 'documents')
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->subDay(),
        ]);

    Livewire::test(AppealForm::class, ['token' => $token])
        ->assertStatus(404);
});

it('aborts when no rejected documents exist', function () {
    $token = Str::random(64);

    Truck::factory()
        ->has(Document::factory()->approved(), 'documents')
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->addDays(30),
        ]);

    Livewire::test(AppealForm::class, ['token' => $token])
        ->assertStatus(404);
});

it('submits appeal successfully', function () {
    $token = Str::random(64);

    $truck = Truck::factory()
        ->rejected()
        ->has(
            Document::factory()
                ->rejected()
                ->state(['rejection_reason' => 'Documento ilegible']),
            'documents'
        )
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->addDays(30),
        ]);

    $document = $truck->documents()->first();
    $file = UploadedFile::fake()->create('documento.pdf', 1024, 'application/pdf');

    Livewire::test(AppealForm::class, ['token' => $token])
        ->set("data.document_{$document->id}", $file)
        ->call('submit')
        ->assertSet('success', true)
        ->assertNotified();

    assertDatabaseHas('documents', [
        'id' => $document->id,
        'status' => DocumentStatusEnum::PENDING->value,
        'rejection_reason' => null,
    ]);

    assertDatabaseHas('trucks', [
        'id' => $truck->id,
        'appeal_token' => null,
    ]);
});

it('displays success view after submission', function () {
    $token = Str::random(64);

    $truck = Truck::factory()
        ->rejected()
        ->has(
            Document::factory()
                ->rejected()
                ->state(['rejection_reason' => 'Documento ilegible']),
            'documents'
        )
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->addDays(30),
        ]);

    $document = $truck->documents()->first();
    $file = UploadedFile::fake()->create('documento.pdf', 1024, 'application/pdf');

    Livewire::test(AppealForm::class, ['token' => $token])
        ->set("data.document_{$document->id}", $file)
        ->call('submit')
        ->assertSee('¡Documentos Enviados Exitosamente!')
        ->assertSee('REVISIÓN DE DOCUMENTOS');
});

it('handles expired documents', function () {
    $token = Str::random(64);

    $truck = Truck::factory()
        ->has(
            Document::factory()
                ->expired()
                ->state(['expiration_date' => now()->subDay()]),
            'documents'
        )
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->addDays(30),
        ]);

    $document = $truck->documents()->first();

    Livewire::test(AppealForm::class, ['token' => $token])
        ->assertSet('truck.id', $truck->id)
        ->assertSee('Documento vencido')
        ->assertSuccessful();
});

it('updates expiration date for expired documents', function () {
    $token = Str::random(64);

    $truck = Truck::factory()
        ->has(
            Document::factory()
                ->expired()
                ->state(['expiration_date' => now()->subDay()]),
            'documents'
        )
        ->create([
            'appeal_token' => $token,
            'appeal_token_expires_at' => now()->addDays(30),
        ]);

    $document = $truck->documents()->first();
    $file = UploadedFile::fake()->create('documento.pdf', 1024, 'application/pdf');
    $newExpirationDate = now()->addYear()->format('Y-m-d');

    Livewire::test(AppealForm::class, ['token' => $token])
        ->set("data.document_{$document->id}", $file)
        ->set("data.expiration_date_{$document->id}", $newExpirationDate)
        ->call('submit')
        ->assertSet('success', true)
        ->assertNotified();

    assertDatabaseHas('documents', [
        'id' => $document->id,
        'status' => DocumentStatusEnum::PENDING->value,
        'expiration_date' => $newExpirationDate,
    ]);
});
