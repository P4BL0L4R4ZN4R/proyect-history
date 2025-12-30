<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Comando de cortes de caja - cada minuto
Schedule::command('cashregister:close')->everyMinute();

// Comando de prueba - cada segundo
// Schedule::command('test:logs')->everySecond();
