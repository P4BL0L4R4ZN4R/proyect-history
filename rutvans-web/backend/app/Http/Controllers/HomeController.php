<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB as FacadesDB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)
            : now()->startOfMonth();
        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)
            : now()->endOfMonth();

        // ==========================
        // 📊 Ventas por día (gráfico de barras)
        // ==========================
        $salesData = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                FacadesDB::raw('DATE(created_at) as date'),
                FacadesDB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ==========================
        // 🥧 Ventas por chofer (gráfico de pastel)
        // ==========================
        $pieData = FacadesDB::table('sales')
            ->join('route_unit_schedule', 'sales.route_unit_schedule_id', '=', 'route_unit_schedule.id')
            ->join('route_unit', 'route_unit_schedule.route_unit_id', '=', 'route_unit.id')
            ->join('driver_unit', 'route_unit.driver_unit_id', '=', 'driver_unit.id')
            ->join('drivers', 'driver_unit.driver_id', '=', 'drivers.id')
            ->join('users', 'drivers.user_id', '=', 'users.id')
            ->where('driver_unit.status', 'activo')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select('users.name as driver', FacadesDB::raw('SUM(sales.amount) as total'))
            ->groupBy('users.name')
            ->get();

        // ==========================
        // 💳 Tarjeta: total ingresos de hoy
        // ==========================
        $totalSales = Sale::whereDate('created_at', today())->sum('amount');

        // ==========================
        // 👨‍✈️ Tarjeta: choferes activos
        // ==========================
        $activeDrivers = FacadesDB::table('driver_unit')
            ->where('status', 'activo')
            ->distinct('driver_id')
            ->count('driver_id');

        // ==========================
        // 🚐 Tarjeta: unidades registradas
        // ==========================
        $activeUnits = Unit::count();

        // ==========================
        // 📦 Tarjeta: viajes realizados hoy
        // ==========================
        $todayTrips = Sale::whereDate('created_at', today())->count();

        return view('dashboard', compact(
            'salesData',
            'pieData',
            'totalSales',
            'activeDrivers',
            'activeUnits',
            'todayTrips',
            'startDate',
            'endDate'
        ));
    }
}
