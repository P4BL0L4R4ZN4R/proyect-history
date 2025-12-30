<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoLogsCommand extends Command
{
    protected $signature = 'autologs:run';
    protected $description = 'Generar logs automáticos cada 5 segundos infinitamente';

    public function handle()
    {
        $this->info("🚀 INICIANDO GENERACIÓN AUTOMÁTICA DE LOGS");
        $this->info("⏰ Intervalo: 5 segundos");
        $this->info("🔄 Modo: Infinito (hasta que se detenga manualmente)");
        $this->info("📁 Logs en: storage/logs/laravel.log");
        $this->line(str_repeat('═', 60));

        $counter = 1;

        while (true) {
            $timestamp = Carbon::now()->format('Y-m-d H:i:s');
            $memory = round(memory_get_usage(true) / 1024 / 1024, 2);

            // Mensaje del log
            $logMessage = "🔄 LOG AUTOMÁTICO #{$counter} - {$timestamp} - {$memory}MB";

            // Alternar entre tipos de log
            if ($counter % 10 == 0) {
                Log::error("❌ {$logMessage} - ERROR DE PRUEBA");
                $this->error("[{$timestamp}] ❌ Error #{$counter}");
            } elseif ($counter % 5 == 0) {
                Log::warning("⚠️ {$logMessage} - WARNING DE PRUEBA");
                $this->warn("[{$timestamp}] ⚠️ Warning #{$counter}");
            } else {
                Log::info("✅ {$logMessage} - INFO DE PRUEBA");
                $this->info("[{$timestamp}] ✅ Info #{$counter}");
            }

            // Log detallado cada 3 iteraciones
            if ($counter % 3 == 0) {
                Log::debug("🐛 Log detallado #{$counter}", [
                    'timestamp' => $timestamp,
                    'iteration' => $counter,
                    'memory_usage_mb' => $memory,
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version()
                ]);
            }

            $counter++;
            sleep(5); // Esperar 5 segundos
        }
    }
}
