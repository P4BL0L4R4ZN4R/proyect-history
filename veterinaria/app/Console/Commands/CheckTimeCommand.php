<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTimeCommand extends Command
{
    protected $signature = 'check:time';
    protected $description = 'Verificar la hora del servidor y configuración';

    public function handle()
    {
        $this->info('🕐 VERIFICACIÓN DE HORA Y CONFIGURACIÓN');
        $this->line('=========================================');

        // Hora del sistema
        $this->info('Hora del servidor (system): ' . shell_exec('date'));

        // Hora de PHP
        $this->info('Hora de PHP: ' . now()->format('Y-m-d H:i:s'));

        // Zona horaria configurada
        $this->info('Zona horaria PHP: ' . config('app.timezone'));

        // Configuración de la base de datos
        $this->info('Zona horaria MySQL: ' . DB::select('SELECT @@global.time_zone, @@session.time_zone')[0]->{'@@global.time_zone'});

        // Verificar configuración de cortes
        $config = DB::table('variables as v')
            ->join('tipo_corte as tc', 'v.tipo_corte', '=', 'tc.id')
            ->select('v.*', 'tc.horarios', 'tc.hora', 'tc.tipo')
            ->first();

        if ($config) {
            $this->info('📋 Configuración de corte:');
            $this->info('   - Tipo: ' . $config->tipo);
            $this->info('   - Horario: ' . $config->horarios);
            $this->info('   - Hora: ' . $config->hora);
        }

        $this->line('=========================================');
    }
}
