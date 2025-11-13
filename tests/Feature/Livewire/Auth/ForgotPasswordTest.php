<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\ForgotPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders successfully', function () {
    Livewire::test(ForgotPassword::class)
        ->assertStatus(200);
});

it('can visit forgot password page', function () {
    $this->get(route('password.request'))
        ->assertSuccessful()
        ->assertSeeLivewire(ForgotPassword::class);
});

it('sends password reset link to valid email', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    Livewire::test(ForgotPassword::class)
        ->set('email', 'test@example.com')
        ->call('sendResetLink')
        ->assertHasNoErrors()
        ->assertDispatched('notify');
});

it('validates email is required', function () {
    Livewire::test(ForgotPassword::class)
        ->set('email', '')
        ->call('sendResetLink')
        ->assertHasErrors(['email' => 'required']);
});

it('validates email format', function () {
    Livewire::test(ForgotPassword::class)
        ->set('email', 'invalid-email')
        ->call('sendResetLink')
        ->assertHasErrors(['email' => 'email']);
});

it('validates email exists in database', function () {
    Livewire::test(ForgotPassword::class)
        ->set('email', 'nonexistent@example.com')
        ->call('sendResetLink')
        ->assertHasErrors(['email' => 'exists']);
});

it('clears email after successful submission', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    Livewire::test(ForgotPassword::class)
        ->set('email', 'test@example.com')
        ->call('sendResetLink')
        ->assertSet('email', '');
});
