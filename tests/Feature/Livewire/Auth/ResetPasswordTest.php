<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders successfully', function () {
    $token = 'test-token';

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->assertStatus(200);
});

it('can visit reset password page with token', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    $this->get(route('password.reset', ['token' => $token, 'email' => $email]))
        ->assertSuccessful()
        ->assertSeeLivewire(ResetPassword::class);
});

it('resets password with valid token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', 'test@example.com')
        ->set('password', 'NewPassword123')
        ->set('password_confirmation', 'NewPassword123')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    $user->refresh();
    expect(Hash::check('NewPassword123', $user->password))->toBeTrue();
});

it('validates password is required', function () {
    $token = 'test-token';

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', 'test@example.com')
        ->set('password', '')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'required']);
});

it('validates password minimum length', function () {
    $token = 'test-token';

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', 'test@example.com')
        ->set('password', 'short')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'min']);
});

it('validates password confirmation matches', function () {
    $token = 'test-token';

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', 'test@example.com')
        ->set('password', 'NewPassword123')
        ->set('password_confirmation', 'DifferentPassword123')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'confirmed']);
});

it('shows error for invalid or expired token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    Livewire::test(ResetPassword::class, ['token' => 'invalid-token'])
        ->set('email', 'test@example.com')
        ->set('password', 'NewPassword123')
        ->set('password_confirmation', 'NewPassword123')
        ->call('resetPassword')
        ->assertHasErrors('email');
});
