<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DocumentStatusEnum;
use App\Enums\EntityStatusEnum;
use App\Mail\ChassisDocumentsExpiringMail;
use App\Mail\DriverDocumentsExpiringMail;
use App\Mail\TruckDocumentsExpiringMail;
use App\Models\Chassis;
use App\Models\Driver;
use App\Models\Truck;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class CheckExpiringDocuments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'documents:check-expiring {--days=15 : Days before expiration to check}';

    /**
     * The console command description.
     */
    protected $description = 'Check for documents expiring in the specified number of days, expired documents, and send notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $targetDate = now()->addDays($days)->format('Y-m-d');

        $this->info("Verificando documentos que vencen en {$days} días (fecha: {$targetDate}) y documentos ya vencidos");

        try {
            // Primero verificar documentos ya vencidos (inhabilita entidades)
            $this->checkExpiredDocuments();

            // Luego verificar documentos próximos a vencer (solo alerta)
            $this->checkDriverDocuments($days);
            $this->checkTruckDocuments($days);
            $this->checkChassisDocuments($days);

            $this->info('Verificación completada exitosamente');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error durante la verificación: '.$e->getMessage());
            Log::error('Error en CheckExpiringDocuments: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Check for already expired documents and update entity status to NEEDS_UPDATE.
     */
    private function checkExpiredDocuments(): void
    {
        $this->info('Verificando documentos vencidos...');

        // Drivers con documentos vencidos
        $driversWithExpired = Driver::whereHas('documents', function ($query) {
            $query->where('expiration_date', '<', now())
                ->whereIn('status', [DocumentStatusEnum::APPROVED, DocumentStatusEnum::EXPIRING_SOON]);
        })
            ->with(['documents' => function ($query) {
                $query->where('expiration_date', '<', now())
                    ->whereIn('status', [DocumentStatusEnum::APPROVED, DocumentStatusEnum::EXPIRING_SOON]);
            }])
            ->get();

        foreach ($driversWithExpired as $driver) {
            foreach ($driver->documents as $document) {
                $document->update(['status' => DocumentStatusEnum::NEEDS_UPDATE]);
            }
            $driver->update(['status' => EntityStatusEnum::NEEDS_UPDATE]);
            $this->info("Driver {$driver->full_name} inhabilitado por documentos vencidos");
        }

        // Trucks con documentos vencidos
        $trucksWithExpired = Truck::whereHas('documents', function ($query) {
            $query->where('expiration_date', '<', now())
                ->whereIn('status', [DocumentStatusEnum::APPROVED, DocumentStatusEnum::EXPIRING_SOON]);
        })
            ->with(['documents' => function ($query) {
                $query->where('expiration_date', '<', now())
                    ->whereIn('status', [DocumentStatusEnum::APPROVED, DocumentStatusEnum::EXPIRING_SOON]);
            }])
            ->get();

        foreach ($trucksWithExpired as $truck) {
            foreach ($truck->documents as $document) {
                $document->update(['status' => DocumentStatusEnum::NEEDS_UPDATE]);
            }
            $truck->update(['status' => EntityStatusEnum::NEEDS_UPDATE]);
            $this->info("Truck {$truck->license_plate} inhabilitado por documentos vencidos");
        }

        // Chassis con documentos vencidos
        $chassisWithExpired = Chassis::whereHas('documents', function ($query) {
            $query->where('expiration_date', '<', now())
                ->whereIn('status', [DocumentStatusEnum::APPROVED, DocumentStatusEnum::EXPIRING_SOON]);
        })
            ->with(['documents' => function ($query) {
                $query->where('expiration_date', '<', now())
                    ->whereIn('status', [DocumentStatusEnum::APPROVED, DocumentStatusEnum::EXPIRING_SOON]);
            }])
            ->get();

        foreach ($chassisWithExpired as $chassis) {
            foreach ($chassis->documents as $document) {
                $document->update(['status' => DocumentStatusEnum::NEEDS_UPDATE]);
            }
            $chassis->update(['status' => EntityStatusEnum::NEEDS_UPDATE]);
            $this->info("Chassis {$chassis->license_plate} inhabilitado por documentos vencidos");
        }
    }

    private function checkDriverDocuments(int $days): void
    {
        $expiringDocuments = $this->getExpiringDocuments(Driver::class, $days);

        foreach ($expiringDocuments as $companyId => $drivers) {
            $company = $drivers->first()->company;
            $representativeEmail = $company->representative?->email;

            if (! $representativeEmail) {
                $this->warn("Empresa {$company->business_name} no tiene representante con email");

                continue;
            }

            Log::info('Enviando notificación de documentos de conductores próximos a vencer', [
                'company_id' => $company->id,
                'email' => $representativeEmail,
                'days' => $days,
                'drivers_count' => count($drivers),
                'drivers' => $drivers->toArray(),
            ]);

            $this->sendDriverExpirationNotification($company, $drivers->toArray(), $representativeEmail, $days);
        }
    }

    private function checkTruckDocuments(int $days): void
    {
        $expiringDocuments = $this->getExpiringDocuments(Truck::class, $days);

        foreach ($expiringDocuments as $companyId => $trucks) {
            $company = $trucks->first()->company;
            $representativeEmail = $company->representative?->email;

            if (! $representativeEmail) {
                $this->warn("Empresa {$company->business_name} no tiene representante con email");

                continue;
            }

            $this->sendTruckExpirationNotification($company, $trucks->toArray(), $representativeEmail, $days);
        }
    }

    private function checkChassisDocuments(int $days): void
    {
        $expiringDocuments = $this->getExpiringDocuments(Chassis::class, $days);

        foreach ($expiringDocuments as $companyId => $chassis) {
            $company = $chassis->first()->company;
            $representativeEmail = $company->representative?->email;

            if (! $representativeEmail) {
                $this->warn("Empresa {$company->business_name} no tiene representante con email");

                continue;
            }

            $this->sendChassisExpirationNotification($company, $chassis->toArray(), $representativeEmail, $days);
        }
    }

    private function getExpiringDocuments(string $modelClass, int $days): \Illuminate\Support\Collection
    {
        $targetDate = now()->addDays($days)->format('Y-m-d');

        $entities = $modelClass::whereHas('documents', function ($query) use ($targetDate) {
            $query->where('expiration_date', $targetDate)
                ->where('status', DocumentStatusEnum::APPROVED);
        })
            ->with(['documents' => function ($query) use ($targetDate) {
                $query->where('expiration_date', $targetDate)
                    ->where('status', DocumentStatusEnum::APPROVED);
            }, 'company.representative'])
            ->get();

        // Actualizar el estado de los documentos a EXPIRING_SOON (próximo a vencer, pero aún válido)
        foreach ($entities as $entity) {
            foreach ($entity->documents as $document) {
                $document->update([
                    'status' => DocumentStatusEnum::EXPIRING_SOON,
                ]);
            }
        }

        return $entities->groupBy('company_id');
    }

    private function sendDriverExpirationNotification($company, array $drivers, string $email, int $days): void
    {
        try {
            // Generar token de apelación para cada conductor
            foreach ($drivers as &$driver) {
                $appealToken = Str::random(64);
                $expiresAt = now()->addDays(30);

                Driver::where('id', $driver['id'])->update([
                    'appeal_token' => $appealToken,
                    'appeal_token_expires_at' => $expiresAt,
                ]);

                $driver['appeal_token'] = $appealToken;
            }
            unset($driver);

            Mail::to($email)->queue(new DriverDocumentsExpiringMail($company, $drivers, $days));

            $this->info("Notificación de conductores enviada a {$email} para empresa {$company->business_name}");
            Log::info('Notificación de documentos de conductores próximos a vencer enviada', [
                'company_id' => $company->id,
                'email' => $email,
                'days' => $days,
                'drivers_count' => count($drivers),
            ]);
        } catch (Exception $e) {
            $this->error("Error enviando notificación de conductores a {$email}: ".$e->getMessage());
            Log::error('Error enviando notificación de conductores', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendTruckExpirationNotification($company, array $trucks, string $email, int $days): void
    {
        try {
            // Generar token de apelación para cada vehículo
            foreach ($trucks as &$truck) {
                $appealToken = Str::random(64);
                $expiresAt = now()->addDays(30);

                Truck::where('id', $truck['id'])->update([
                    'appeal_token' => $appealToken,
                    'appeal_token_expires_at' => $expiresAt,
                ]);

                $truck['appeal_token'] = $appealToken;
            }
            unset($truck);

            Mail::to($email)->queue(new TruckDocumentsExpiringMail($company, $trucks, $days));

            $this->info("Notificación de vehículos enviada a {$email} para empresa {$company->business_name}");
            Log::info('Notificación de documentos de vehículos próximos a vencer enviada', [
                'company_id' => $company->id,
                'email' => $email,
                'days' => $days,
                'trucks_count' => count($trucks),
            ]);
        } catch (Exception $e) {
            $this->error("Error enviando notificación de vehículos a {$email}: ".$e->getMessage());
            Log::error('Error enviando notificación de vehículos', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendChassisExpirationNotification($company, array $chassis, string $email, int $days): void
    {
        try {
            // Generar token de apelación para cada chassis
            foreach ($chassis as &$chassisItem) {
                $appealToken = Str::random(64);
                $expiresAt = now()->addDays(30);

                Chassis::where('id', $chassisItem['id'])->update([
                    'appeal_token' => $appealToken,
                    'appeal_token_expires_at' => $expiresAt,
                ]);

                $chassisItem['appeal_token'] = $appealToken;
            }
            unset($chassisItem);

            Mail::to($email)->queue(new ChassisDocumentsExpiringMail($company, $chassis, $days));

            $this->info("Notificación de chassis enviada a {$email} para empresa {$company->business_name}");
            Log::info('Notificación de documentos de chassis próximos a vencer enviada', [
                'company_id' => $company->id,
                'email' => $email,
                'days' => $days,
                'chassis_count' => count($chassis),
            ]);
        } catch (Exception $e) {
            $this->error("Error enviando notificación de chassis a {$email}: ".$e->getMessage());
            Log::error('Error enviando notificación de chassis', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
