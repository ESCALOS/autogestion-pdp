<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Recuperar Contraseña')]
final class ForgotPassword extends Component
{
    public string $email = '';

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ser un correo electrónico válido.',
            'email.exists' => 'No existe un usuario con este correo electrónico.',
        ];
    }

    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->dispatch('notify',
                message: 'Te hemos enviado un enlace para restablecer tu contraseña por correo electrónico.',
                type: 'success'
            );
            $this->email = '';
        } else {
            $this->addError('email', 'No pudimos enviar el enlace. Inténtalo de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
