<?php

declare(strict_types=1);

namespace App\Livewire\Supplier\Auth;

use App\Models\Supplier;
use App\Services\WhatsAppService;
use Filament\Notifications\Notification;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.guest')]
final class VerifyPhoneCode extends Component
{
    public ?Supplier $supplier = null;

    public string $verificationCode = '';

    public bool $codeExpired = false;

    public int $attemptsLeft = 3;

    public function mount(Supplier $supplier): void
    {
        $this->supplier = $supplier;

        if ($supplier->phone_verified_at) {
            $this->redirect(route('supplier.dashboard'));
        }

        if (! $supplier->verification_code_expires_at || now()->isAfter($supplier->verification_code_expires_at)) {
            $this->codeExpired = true;
        }
    }

    public function verifyCode(): void
    {
        $this->validate([
            'verificationCode' => 'required|string|size:6',
        ], [
            'verificationCode.required' => 'El código de verificación es requerido.',
            'verificationCode.size' => 'El código debe tener 6 dígitos.',
        ]);

        if ($this->supplier->verifyPhoneCode($this->verificationCode)) {
            Notification::make()
                ->title('Teléfono verificado exitosamente')
                ->success()
                ->send();

            $this->redirect(route('supplier.dashboard'), navigate: true);
        } else {
            $this->attemptsLeft--;

            if ($this->attemptsLeft <= 0) {
                Notification::make()
                    ->title('Demasiados intentos fallidos')
                    ->body('Por favor solicita un nuevo código.')
                    ->danger()
                    ->send();

                $this->supplier->update([
                    'verification_code' => null,
                    'verification_code_expires_at' => null,
                ]);

                $this->codeExpired = true;
            } else {
                Notification::make()
                    ->title('Código incorrecto')
                    ->body("Intentos restantes: {$this->attemptsLeft}")
                    ->warning()
                    ->send();
            }

            $this->verificationCode = '';
        }
    }

    public function resendCode(): void
    {
        $code = $this->supplier->generateVerificationCode();
        $whatsApp = new WhatsAppService();

        if ($whatsApp->sendVerificationCode($this->supplier->getFullPhoneNumber(), $code)) {
            Notification::make()
                ->title('Código reenviado')
                ->body('Hemos enviado un nuevo código a tu WhatsApp.')
                ->success()
                ->send();

            $this->codeExpired = false;
            $this->attemptsLeft = 3;
        } else {
            Notification::make()
                ->title('Error al enviar el código')
                ->body('Por favor intenta nuevamente.')
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.supplier.auth.verify-phone-code');
    }
}
