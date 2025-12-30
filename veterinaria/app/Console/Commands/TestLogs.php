<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestLogs extends Command
{
    protected $signature = 'test:logs';
    protected $description = 'Comando de prueba que se ejecuta cada segundo';

    public function handle()
    {
        // Log simple con timestamp preciso
        $message = '✅ Log automático cada segundo - ' . now()->format('H:i:s.v');
        Log::info($message);
        $this->info($message);
    }
}
