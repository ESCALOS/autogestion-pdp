<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class CheckAllExpiringDocuments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'documents:check-all-expiring';

    /**
     * The console command description.
     */
    protected $description = 'Check documents expiring in 15 and 7 days and send notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Iniciando verificación de documentos próximos a vencer...');

        // Verificar documentos que vencen en 15 días
        $this->info('📅 Verificando documentos que vencen en 15 días...');
        $result15 = Artisan::call('documents:check-expiring', ['--days' => 15]);

        if ($result15 === 0) {
            $this->info('✅ Verificación de 15 días completada');
        } else {
            $this->error('❌ Error en verificación de 15 días');
        }

        // Verificar documentos que vencen en 7 días
        $this->info('📅 Verificando documentos que vencen en 7 días...');
        $result7 = Artisan::call('documents:check-expiring', ['--days' => 7]);

        if ($result7 === 0) {
            $this->info('✅ Verificación de 7 días completada');
        } else {
            $this->error('❌ Error en verificación de 7 días');
        }

        if ($result15 === 0 && $result7 === 0) {
            $this->info('🎉 Todas las verificaciones completadas exitosamente');

            return Command::SUCCESS;
        }
        $this->error('⚠️  Algunas verificaciones fallaron');

        return Command::FAILURE;

    }
}
