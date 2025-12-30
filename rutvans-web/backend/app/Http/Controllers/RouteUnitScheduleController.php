<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RouteUnitSchedule;
use App\Models\RouteUnit;
use Illuminate\Support\Facades\Auth;

class RouteUnitScheduleController extends Controller
{
    public function index()
    {
        // Get current user's sites
        $userSites = Auth::user()->sites->pluck('id');
        
        // Filter route units by sites the user has access to through drivers
        $units = RouteUnit::with('driverUnit.driver.user')
            ->whereHas('driverUnit.driver', function($query) use ($userSites) {
                $query->whereIn('site_id', $userSites);
            })
            ->get();
            
        return view('route_unit_schedule.index', compact('units'));
    }

    public function getEvents()
    {
        // Get current user's sites
        $userSites = Auth::user()->sites->pluck('id');
        
        return RouteUnitSchedule::with([
            'routeUnit.driverUnit.driver.user',
            'routeUnit.route.locationStart',
            'routeUnit.route.locationEnd',
        ])->whereHas('routeUnit.driverUnit.driver', function($query) use ($userSites) {
            $query->whereIn('site_id', $userSites);
        })->get()->map(function ($schedule) {
            $driverName = $schedule->routeUnit->driverUnit->driver->user->name ?? 'Sin conductor';

            $route = $schedule->routeUnit->route ?? null;
            $locationStart = $route?->locationStart?->locality ?? 'Origen desconocido';
            $locationEnd = $route?->locationEnd?->locality ?? 'Destino desconocido';

            return [
                'id' => $schedule->id,
                'title' => "Ruta: $locationStart → $locationEnd | Unidad {$schedule->id_route_unit} - Conductor: $driverName ({$schedule->status})",
                'start' => $schedule->schedule_date . 'T' . $schedule->schedule_time,
                'route_unit_id' => $schedule->id_route_unit,
                'status' => $schedule->status,
            ];
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_route_unit' => 'required|exists:route_unit,id',
            'schedule_date' => 'required|date',
            'schedule_time' => 'required',
            'status' => 'required|string'
        ]);

        $event = RouteUnitSchedule::create($validated);

        return response()->json($event, 201);
    }

    public function update(Request $request, $id)
    {
        $event = RouteUnitSchedule::findOrFail($id);

        $event->update($request->only(['id_route_unit', 'schedule_date', 'schedule_time', 'status']));

        return response()->json($event);
    }

    public function destroy($id)
    {
        $event = RouteUnitSchedule::findOrFail($id);
        $event->delete();

        return response()->json(['success' => true]);
    }
}
