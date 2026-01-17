<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DocumentStatusEnum;
use App\Enums\EntityStatusEnum;
use App\Models\Chassis;
use App\Models\Driver;
use App\Models\Truck;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class CheckExpiredDocuments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'documents:check-expired';

    /**
     * The console command description.
     */
    protected $description = 'Check for expired documents and update entity status to NEEDS_UPDATE';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Verificando documentos vencidos...');

        try {
            $this->updateExpiredDocumentsForEntity(Driver::class, 'full_name', 'Driver');
            $this->updateExpiredDocumentsForEntity(Truck::class, 'license_plate', 'Truck');
            $this->updateExpiredDocumentsForEntity(Chassis::class, 'license_plate', 'Chassis');

            $this->info('Verificación de documentos vencidos completada exitosamente');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error durante la verificación de documentos vencidos: '.$e->getMessage());
            Log::error('Error en CheckExpiredDocuments: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Update expired documents for a specific entity type.
     */
    private function updateExpiredDocumentsForEntity(string $modelClass, string $identifierAttribute, string $entityType): void
    {
        $entitiesWithExpired = $modelClass::whereHas('documents', function ($query) {
            $query->expiredAndNeedsUpdate();
        })
            ->with(['documents' => function ($query) {
                $query->expiredAndNeedsUpdate();
            }])
            ->get();

        foreach ($entitiesWithExpired as $entity) {
            $expiredDocumentIds = [];

            foreach ($entity->documents as $document) {
                $document->update(['status' => DocumentStatusEnum::NEEDS_UPDATE]);
                $expiredDocumentIds[] = $document->id;
            }

            $entity->update(['status' => EntityStatusEnum::NEEDS_UPDATE]);

            $message = "{$entityType} {$entity->{$identifierAttribute}} inhabilitado por documentos vencidos";
            $this->info($message);

            Log::info("{$entityType} inhabilitado por documentos vencidos", [
                'entity_type' => $entityType,
                'entity_id' => $entity->id,
                'identifier' => $entity->{$identifierAttribute},
                'expired_documents_count' => count($expiredDocumentIds),
                'expired_document_ids' => $expiredDocumentIds,
            ]);
        }
    }
}
