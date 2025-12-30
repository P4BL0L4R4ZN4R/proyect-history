<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Hora del servidor: ' . shell_exec('date'));
        $this->info('Hora de PHP: ' . now()->format('Y-m-d H:i:s'));
        $this->info('Zona horaria PHP: ' . config('app.timezone'));
    }
}
