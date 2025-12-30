<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Comando de cortes de caja - cada minuto
        $schedule->command('cashregister:close')->everyMinute();

        // Comando de prueba - CADA SEGUNDO (¡sí existe!)
        $schedule->command('test:logs')->everySecond();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
