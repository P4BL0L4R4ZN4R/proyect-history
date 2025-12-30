<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RouteUnitSchedule;
use App\Models\User;

class RouteUnitScheduleController extends Controller
{
    // Obtener todo el historial de rutas o filtrado por unidad
    public function index(Request $request)
    {
        $query = RouteUnitSchedule::with([
            'routeUnit.driverUnit.driver.user',
            'routeUnit.route.locationStart',
            'routeUnit.route.locationEnd',
        ]);

        // Si Flutter envía ?route_unit_id=5
        if ($request->has('route_unit_id')) {
            $query->where('id_route_unit', $request->route_unit_id);
        }

        $schedules = $query->orderBy('schedule_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

public function show($id)
{
    //$username = User::find($id)->name;

    $schedules = RouteUnitSchedule::select('route_unit_schedule.*')
        ->join('route_unit', 'route_unit_schedule.route_unit_id', '=', 'route_unit.id')
        ->join('driver_unit', 'route_unit.driver_unit_id', '=', 'driver_unit.id')
        ->join('drivers', 'driver_unit.driver_id', '=', 'drivers.id')
        ->join('users', 'drivers.user_id', '=', 'users.id')
        ->where('users.id', $id)
        ->get();


    if ($schedules->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No se encontraron schedules para este usuario'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $schedules,
       // 'username' => $username
    ]);
}




}
