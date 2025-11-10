<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Crear roles necesarios
    Role::create(['name' => 'super-admin']);
    Role::create(['name' => 'admin']);
});

it('redirects super-admin users without company_id to filament admin panel', function () {
    $user = User::factory()->create([
        'company_id' => null,
    ]);
    $user->assignRole('super-admin');

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('filament.admin.pages.dashboard'));
});

it('redirects admin users without company_id to filament admin panel', function () {
    $user = User::factory()->create([
        'company_id' => null,
    ]);
    $user->assignRole('admin');

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('filament.admin.pages.dashboard'));
});

it('logs out users without company_id and without admin roles', function () {
    Log::fake();

    $user = User::factory()->create([
        'company_id' => null,
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors(['email']);

    $this->assertGuest();

    Log::assertLogged('warning', function ($message, $context) use ($user) {
        return str_contains($message, 'Usuario sin company_id y sin roles de admin intentó acceder') &&
               $context['user_id'] === $user->id;
    });
});

it('allows users with company_id to continue normally', function () {
    $user = User::factory()->create([
        'company_id' => 1, // Asumiendo que existe una empresa con ID 1
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSuccessful();
});

it('allows guest users to continue normally', function () {
    $this->get('/')
        ->assertSuccessful();
});
