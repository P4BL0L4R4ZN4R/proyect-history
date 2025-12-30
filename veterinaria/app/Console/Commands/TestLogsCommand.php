<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TestLogsCommand extends Command
{
    protected $signature = 'test:logs
                            {--seconds=5 : Segundos entre logs}
                            {--count=10 : Número de logs a generar}
                            {--message= : Mensaje personalizado}';

    protected $description = 'Generar logs de prueba cada X segundos';

    public function handle()
    {
        $seconds = (int) $this->option('seconds');
        $count = (int) $this->option('count');
        $customMessage = $this->option('message') ?? 'Log de prueba automático';

        $this->info("🚀 Iniciando generación de logs cada {$seconds} segundos");
        $this->info("📊 Total de logs a generar: {$count}");
        $this->info("💬 Mensaje: {$customMessage}");
        $this->line(str_repeat('═', 60));

        for ($i = 1; $i <= $count; $i++) {
            // Generar log con información detallada
            $logMessage = "{$customMessage} - Log #{$i} - " . Carbon::now()->format('H:i:s');

            // Log en diferentes niveles
            if ($i % 5 == 0) {
                Log::error("❌ {$logMessage} - ERROR TEST");
                $this->error("❌ Log #{$i} - ERROR");
            } elseif ($i % 3 == 0) {
                Log::warning("⚠️ {$logMessage} - WARNING TEST");
                $this->warn("⚠️ Log #{$i} - WARNING");
            } else {
                Log::info("✅ {$logMessage} - INFO TEST");
                $this->info("✅ Log #{$i} - INFO");
            }

            // Log detallado con contexto
            Log::debug("🐛 Log detallado #{$i}", [
                'timestamp' => now()->toDateTimeString(),
                'iteration' => $i,
                'total_iterations' => $count,
                'seconds_interval' => $seconds,
                'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB'
            ]);

            // Esperar si no es la última iteración
            if ($i < $count) {
                sleep($seconds);
            }
        }

        $this->line(str_repeat('═', 60));
        $this->info("🎉 Generación de logs completada");
        $this->info("📁 Logs guardados en: storage/logs/laravel.log");
        $this->info("🕐 Hora de finalización: " . now()->format('Y-m-d H:i:s'));

        // Log final
        Log::info("🏁 TEST COMPLETADO: {$count} logs generados cada {$seconds} segundos");
    }
}
