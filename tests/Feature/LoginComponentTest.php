<?php

declare(strict_types=1);

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Crear roles necesarios
    Role::create(['name' => 'super-admin']);
    Role::create(['name' => 'admin']);
});

it('allows users with company_id to login', function () {
    $user = User::factory()->create([
        'email' => 'user@test.com',
        'password' => Hash::make('password'),
        'company_id' => 1,
        'is_active' => true,
    ]);

    Livewire::test(Login::class)
        ->set('email', 'user@test.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('allows super-admin users without company_id to login', function () {
    $user = User::factory()->create([
        'email' => 'admin@test.com',
        'password' => Hash::make('password'),
        'company_id' => null,
        'is_active' => true,
    ]);
    $user->assignRole('super-admin');

    Livewire::test(Login::class)
        ->set('email', 'admin@test.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('allows admin users without company_id to login', function () {
    $user = User::factory()->create([
        'email' => 'admin@test.com',
        'password' => Hash::make('password'),
        'company_id' => null,
        'is_active' => true,
    ]);
    $user->assignRole('admin');

    Livewire::test(Login::class)
        ->set('email', 'admin@test.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('prevents users without company_id and without admin roles from logging in', function () {
    $user = User::factory()->create([
        'email' => 'user@test.com',
        'password' => Hash::make('password'),
        'company_id' => null,
        'is_active' => true,
    ]);

    Livewire::test(Login::class)
        ->set('email', 'user@test.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email' => __('auth.failed')])
        ->assertNotRedirect();

    $this->assertGuest();
});

it('prevents inactive users from logging in', function () {
    $user = User::factory()->create([
        'email' => 'user@test.com',
        'password' => Hash::make('password'),
        'company_id' => 1,
        'is_active' => false,
    ]);

    Livewire::test(Login::class)
        ->set('email', 'user@test.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email' => __('auth.inactive')])
        ->assertNotRedirect();

    $this->assertGuest();
});
