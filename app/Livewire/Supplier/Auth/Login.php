<?php

declare(strict_types=1);

namespace App\Livewire\Supplier\Auth;

use App\Enums\CompanyStatusEnum;
use App\Services\WhatsAppService;
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
#[Title('Iniciar Sesión - Proveedores')]
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

        // Verificar primero si el usuario existe en supplier_users
        $user = \App\Models\SupplierUser::where('email', $this->email)->first();

        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (! Auth::guard('supplier')->attempt(['email' => $this->email, 'password' => $this->password], true)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Validar supplier status y user active
        if (Auth::guard('supplier')->user()->supplier_id) {
            if (! (Auth::guard('supplier')->user()->supplierStatus() === CompanyStatusEnum::APROBADO && Auth::guard('supplier')->user()->supplierIsActive())) {
                Auth::guard('supplier')->logout();

                throw ValidationException::withMessages([
                    'email' => __('auth.inactive_supplier'),
                ]);
            }
        }

        if (! Auth::guard('supplier')->user()->is_active) {
            Auth::guard('supplier')->logout();

            throw ValidationException::withMessages([
                'email' => __('auth.inactive'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Obtener el supplier del usuario
        $supplier = Auth::guard('supplier')->user()->supplier;

        // Si el teléfono no está verificado, enviar código OTP
        if ($supplier && ! $supplier->phone_verified_at) {
            $code = $supplier->generateVerificationCode();
            $whatsApp = new WhatsAppService();
            $whatsApp->sendVerificationCode($supplier->getFullPhoneNumber(), $code);

            $this->redirect(route('supplier.verify-phone', $supplier), navigate: true);

            return;
        }

        $this->redirect(route('supplier.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.supplier.auth.login');
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
