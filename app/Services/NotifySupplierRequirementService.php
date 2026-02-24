<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\MachineryRequirementApprovedMail;
use App\Models\MachineryRequirement;
use App\Models\SupplierMachinery;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class NotifySupplierRequirementService
{
    public function __construct(private WhatsAppService $whatsAppService) {}

    public function notifyProvidersForRequirement(MachineryRequirement $requirement): void
    {
        try {
            // Buscar todas las maquinarias aprobadas que coincidan con el tipo de unidad solicitado
            $machineries = SupplierMachinery::query()
                ->where('truck_type', $requirement->unit_type)
                ->where('status', 2) // EntityStatusEnum::ACTIVE value
                ->with('supplier.representative')
                ->get();

            foreach ($machineries as $machinery) {
                $supplier = $machinery->supplier;

                // Enviar WhatsApp si el proveedor tiene teléfono
                if ($supplier->phone_number && $supplier->phone_prefix) {
                    $this->sendWhatsAppNotification($requirement, $supplier);
                }

                // Enviar correo al representante si existe
                $representative = $supplier->representative;
                if ($representative?->email) {
                    $this->sendEmailNotification($requirement, $supplier, $representative);
                }
            }

            Log::info("Notificaciones enviadas para requerimiento #{$requirement->id}", [
                'machineries_notified' => $machineries->count(),
            ]);
        } catch (Exception $e) {
            Log::error("Error al notificar proveedores para requerimiento #{$requirement->id}", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendWhatsAppNotification(MachineryRequirement $requirement, $supplier): void
    {
        $message = $this->buildWhatsAppMessage($requirement, $supplier);

        try {
            $this->whatsAppService->sendCustomMessage(
                $supplier->getFullPhoneNumber(),
                $message
            );

            Log::info("WhatsApp enviado a proveedor {$supplier->id} para requerimiento #{$requirement->id}");
        } catch (Exception $e) {
            Log::error("Error al enviar WhatsApp a proveedor {$supplier->id}", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendEmailNotification(MachineryRequirement $requirement, $supplier, $representative): void
    {
        try {
            Mail::to($representative->email)->send(
                new MachineryRequirementApprovedMail($requirement, $supplier)
            );

            Log::info("Correo enviado a {$representative->email} para requerimiento #{$requirement->id}");
        } catch (Exception $e) {
            Log::error("Error al enviar correo para requerimiento #{$requirement->id}", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildWhatsAppMessage(MachineryRequirement $requirement, $supplier): string
    {
        $vesselName = $requirement->vessel_name;
        $unitType = $requirement->unit_type->getLabel();
        $activationTime = $requirement->activation_time->format('d/m/Y H:i');
        $unitsQuantity = $requirement->units_quantity;

        return <<<MSG
¡Hola! Le informamos que hay un nuevo requerimiento de maquinaria disponible:

*Nave:* {$vesselName}
*Tipo de Unidad:* {$unitType}
*Unidades Solicitadas:* {$unitsQuantity}
*Hora de Activación:* {$activationTime}

Si está interesado, ingrese a la plataforma para más detalles.

¡Gracias!
MSG;
    }
}
