<?php
namespace App\Jobs;

use App\Models\Producto;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class RevisarProductosCaducados
{
    use Dispatchable, Queueable;

    public function handle()
    {
        Log::info('--- Inicio del Job RevisarProductosCaducados ---');

        try {
            $fechaLimite = now()->addDays(30);
            Log::info('Fecha límite (30 días): ' . $fechaLimite->toDateTimeString());

            $productosCaducados = Producto::whereDate('caducidad', '<=', $fechaLimite)->get();

            $array = $productosCaducados->toArray();

            Log::info("Productos caducados encontrados: " . count($array));
            Log::info('Detalle de productos: ' . print_r($array, true));

            return $array;
        } catch (\Exception $e) {
            Log::error('Error en RevisarProductosCaducados: ' . $e->getMessage());
            return [];
        }
    }
}
