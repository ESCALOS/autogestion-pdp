<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Enums\CompanyStatusEnum;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Iniciar Sesión')]
final class Login extends Component
{
    #[Validate('required|email')]
    public $email = '';

    #[Validate('required|string')]
    public $password = '';

    public function login()
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        // Verificar primero si el usuario existe y tiene company_id o es admin
        $user = \App\Models\User::where('email', $this->email)->first();

        if ($user && is_null($user->company_id) && ! $user->hasRole(['super-admin', 'admin'])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], true)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Solo validar company status y user active para usuarios con company_id
        if (Auth::user()->company_id) {
            if (! (Auth::user()->companyStatus() === CompanyStatusEnum::APROBADO && Auth::user()->companyIsActive())) {
                Auth::logout();

                throw ValidationException::withMessages([
                    'email' => __('auth.inactive_company'),
                ]);
            }
        }

        if (! Auth::user()->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => __('auth.inactive'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
