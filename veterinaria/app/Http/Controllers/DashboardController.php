<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\CorteCaja;
use App\Models\User;
use App\Models\DetalleVenta;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Función principal que devuelve todos los datos (Opción A)
    public function obtenerTodosLosDatos()
    {
        try {
            $hoy = Carbon::today();
            $inicioMes = Carbon::now()->startOfMonth();
            $finMes = Carbon::now()->endOfMonth();

            return response()->json([
                'estadisticas_ventas' => [
                    'total_ventas_mes' => $this->getTotalVentasMes($inicioMes, $finMes),
                    'ventas_hoy' => $this->getVentasHoy($hoy),
                    'promedio_venta_diaria' => $this->getPromedioVentaDiaria($inicioMes, $finMes),
                    'total_ventas_general' => $this->getTotalVentasGeneral(),
                ],
                'estadisticas_inventario' => [
                    'total_productos' => $this->getTotalProductos(),
                    'productos_bajo_stock' => $this->getProductosBajoStock(),
                    'productos_proximos_caducar' => $this->getProductosProximosCaducar(),
                    'productos_activos' => $this->getProductosActivos(),
                ],
                'estadisticas_caja' => [
                    'corte_actual' => $this->getCorteActual(),
                    'total_cortes_mes' => $this->getTotalCortesMes($inicioMes, $finMes),
                ],
                'estadisticas_usuarios' => [
                    'total_usuarios' => $this->getTotalUsuarios(),
                    'ventas_por_usuario' => $this->getVentasPorUsuario(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener datos: ' . $e->getMessage()], 500);
        }
    }

    // Funciones individuales para cada dato (Opción B)

    public function totalVentasMes()
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        return response()->json([
            'dato' => $this->getTotalVentasMes($inicioMes, $finMes)
        ]);
    }

    public function ventasHoy()
    {
        $hoy = Carbon::today('America/Mexico_City');

        // DEBUG: Ver qué fecha está buscando
        \Log::info("Buscando ventas para hoy: " . $hoy->toDateString());

        $ventasHoy = $this->getVentasHoy($hoy);

        // DEBUG: Ver el resultado
        \Log::info("Ventas encontradas hoy: " . $ventasHoy);

        return response()->json([
            'dato' => $ventasHoy
        ]);
    }

    public function totalProductos()
    {
        return response()->json([
            'dato' => $this->getTotalProductos()
        ]);
    }

    public function productosBajoStock()
    {
        return response()->json([
            'dato' => $this->getProductosBajoStock()
        ]);
    }

    public function productosProximosCaducar()
    {
        return response()->json([
            'dato' => $this->getProductosProximosCaducar()
        ]);
    }

    public function corteActual()
    {
        return response()->json([
            'dato' => $this->getCorteActual()
        ]);
    }

    public function promedioVentaDiaria()
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        return response()->json([
            'dato' => $this->getPromedioVentaDiaria($inicioMes, $finMes)
        ]);
    }

    // Métodos privados para los cálculos

    private function getTotalVentasMes($inicioMes, $finMes)
    {
        return Venta::whereBetween('created_at', [$inicioMes, $finMes])
            ->sum('total') ?? 0;
    }

    private function getVentasHoy($hoy)
    {
        return Venta::whereDate('created_at', $hoy)
            ->sum('total') ?? 0;
    }

    private function getPromedioVentaDiaria($inicioMes, $finMes)
    {
        $totalVentas = $this->getTotalVentasMes($inicioMes, $finMes);
        $diasTranscurridos = Carbon::now()->diffInDays($inicioMes) + 1;

        return $diasTranscurridos > 0 ? round($totalVentas / $diasTranscurridos, 2) : 0;
    }

    private function getTotalVentasGeneral()
    {
        return Venta::sum('total') ?? 0;
    }

    private function getTotalProductos()
    {
        return Producto::where('estado', 'activo')->count();
    }

    private function getProductosBajoStock()
    {
        return Producto::where('estado', 'activo')
            ->whereRaw('stock <= minimo_stock')
            ->count();
    }

    private function getProductosProximosCaducar()
    {
        $fechaLimite = Carbon::now()->addDays(30);

        return Producto::where('estado', 'activo')
            ->where('caducidad', '<=', $fechaLimite)
            ->where('caducidad', '>=', Carbon::now())
            ->count();
    }

    private function getProductosActivos()
    {
        return Producto::where('estado', 'activo')->count();
    }

    private function getCorteActual()
    {
        return CorteCaja::where('estado', 'abierto')
            ->latest()
            ->first();
    }

    private function getTotalCortesMes($inicioMes, $finMes)
    {
        return CorteCaja::whereBetween('created_at', [$inicioMes, $finMes])
            ->where('estado', 'cerrado')
            ->count();
    }

    private function getTotalUsuarios()
    {
        return User::count();
    }

    private function getVentasPorUsuario()
    {
        return DB::table('ventas')
            ->join('users', 'ventas.usuario_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(ventas.id) as total_ventas'))
            ->groupBy('users.id', 'users.name')
            ->get();
    }


        public function ticketsHoy()
    {
        $hoy = Carbon::today('America/Mexico_City');

        $cantidadVentas = Venta::whereDate('created_at', $hoy)->count();

        return response()->json([
            'dato' => $cantidadVentas
        ]);
    }
}
