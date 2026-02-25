<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class WhatsAppService
{
    public function __construct(
        private string $baseUrl = '',
        private string $bearerToken = '',
    ) {
        $this->baseUrl = $baseUrl ?: (string) config('services.whatsapp.base_url');
        $this->bearerToken = $bearerToken ?: (string) config('services.whatsapp.token');
    }


    public function sendVerificationCode(string $phoneNumber, string $code): bool
    {
        try {
            $message = "Estimado, este es su código de verificación: $code. Válido por 10 minutos.";

            $response = Http::withToken($this->bearerToken)
                ->post($this->baseUrl, [
                    'number' => $phoneNumber,
                    'text' => $message,
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $phoneNumber,
                    'code' => $code,
                ]);

                return true;
            }

            Log::error('WhatsApp API error', [
                'phone' => $phoneNumber,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('WhatsApp service exception', [
                'message' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);

            return false;
        }
    }

    public function sendCustomMessage(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::withToken($this->bearerToken)
                ->post($this->baseUrl, [
                    'number' => $phoneNumber,
                    'text' => $message,
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $phoneNumber,
                ]);

                return true;
            }

            Log::error('WhatsApp API error', [
                'phone' => $phoneNumber,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('WhatsApp service exception', [
                'message' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);

            return false;
        }
    }
}
