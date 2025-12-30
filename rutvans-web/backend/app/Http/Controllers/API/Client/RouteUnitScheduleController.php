<?php

namespace App\Http\Controllers\API\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\RouteUnit;
use App\Models\RouteUnitSchedule;
use App\Models\Sale;

class RouteUnitScheduleController extends Controller
{
    public function getAvailableDestinations(Request $request)
    {
        try {
            $origin = $request->query('origin');
            
            if (!$origin) {
                return response()->json([
                    'error' => 'El parametro origin es requerido'
                ], 400);
            }

            $destinations = Route::with(['locationEnd:id,locality'])
                ->whereHas('locationStart', function($query) use ($origin) {
                    $query->where('locality', $origin);
                })
                ->get()
                ->pluck('locationEnd.locality')
                ->unique()
                ->values()
                ->toArray();

            return response()->json([
                'status' => 'success',
                'data' => $destinations,
                'message' => 'Destinos disponibles obtenidos correctamente',
                'origin' => $origin
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    public function getRouteUnitSchedules(Request $request)
    {
        try {
            $origin = $request->query('origin');
            $destination = $request->query('destination');

            // Si no se pasa origin ni destination, devolver todos los horarios
            if (!$origin && !$destination) {
                return $this->getAllSchedulesWithTimes();
            }

            // Si uno de los dos parámetros falta
            if (!$origin || !$destination) {
                return response()->json([
                    'error' => 'Los parametros origin y destination son requeridos'
                ], 400);
            }

            return $this->getSchedulesByOriginDestination($origin, $destination);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
private function getAllSchedulesWithTimes()
{
    $allSchedules = [];
    $originsSet = collect();

    // Traer todas las rutas
    $routes = Route::with([
        'locationStart:id,locality',
        'locationEnd:id,locality'
    ])->get();

    foreach ($routes as $route) {
        // Guardar los orígenes únicos
        $originsSet->push($route->locationStart->locality);

        $routeUnits = RouteUnit::with(['driverUnit.unit:id,plate,model,capacity'])
            ->where('route_id', $route->id)
            ->get();

        foreach ($routeUnits as $routeUnit) {
            $schedules = RouteUnitSchedule::where('route_unit_id', $routeUnit->id)
                ->where('status', 'Activo')
                ->where('schedule_date', '>=', now()->format('Y-m-d'))
                ->orderBy('schedule_date')
                ->orderBy('schedule_time')
                ->get();

            foreach ($schedules as $schedule) {
                $occupiedSeats = Sale::where('route_unit_schedule_id', $schedule->id)
                    ->get()
                    ->pluck('data')
                    ->map(function($data) {
                        $decoded = is_string($data) ? json_decode($data, true) : $data;
                        return $decoded['seats'] ?? $decoded['asientos'] ?? [];
                    })
                    ->flatten()
                    ->unique()
                    ->count();

                $unit = $routeUnit->driverUnit->unit ?? null;
                $totalSeats = $unit->capacity ?? 20;
                $availableSeats = $totalSeats - $occupiedSeats;

                $allSchedules[] = [
                    'id' => $schedule->id,
                    'route_unit_id' => $routeUnit->id,
                    'schedule_date' => $schedule->schedule_date,
                    'schedule_time' => $schedule->schedule_time,
                    'price' => $routeUnit->price ?? 250.00,
                    'available_seats' => max(0, $availableSeats),
                    'total_seats' => $totalSeats,
                    'unit_id' => $unit->id ?? null,
                    'capacity' => $totalSeats,
                    'rate_id' => $routeUnit->rate_id ?? 1,
                    'origin' => $route->locationStart->locality,
                    'destination' => $route->locationEnd->locality,
                    'unit_model' => $unit->model ?? 'Vehículo',
                    'license_plate' => $unit->plate ?? 'N/A',
                    'driver_name' => 'Conductor',
                    'estimated_duration_minutes' => 120
                ];
            }
        }
    }

    // Orígenes únicos
    $availableOrigins = $originsSet->unique()->values()->all();

    return response()->json([
        'status' => 'success',
        'data' => [
            'schedules' => $allSchedules,
            'available_origins' => $availableOrigins
        ],
        'message' => 'Horarios y orígenes obtenidos correctamente'
    ]);
}


    private function getSchedulesByOriginDestination($origin, $destination)
    {
        $routes = Route::with([
            'locationStart:id,locality',
            'locationEnd:id,locality'
        ])
        ->whereHas('locationStart', function($query) use ($origin) {
            $query->where('locality', $origin);
        })
        ->whereHas('locationEnd', function($query) use ($destination) {
            $query->where('locality', $destination);
        })
        ->get();

        if ($routes->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'No hay rutas disponibles para la combinación origen-destino seleccionada',
                'params' => ['origin' => $origin, 'destination' => $destination]
            ]);
        }

        $allSchedules = [];

        foreach ($routes as $route) {
            $routeUnits = RouteUnit::with(['driverUnit.unit:id,plate,model,capacity'])
                ->where('route_id', $route->id)
                ->get();

            foreach ($routeUnits as $routeUnit) {
                $schedules = RouteUnitSchedule::where('route_unit_id', $routeUnit->id)
                    ->where('status', 'Activo')
                    ->where('schedule_date', '>=', now()->format('Y-m-d'))
                    ->orderBy('schedule_date')
                    ->orderBy('schedule_time')
                    ->get();

                foreach ($schedules as $schedule) {
                    $occupiedSeats = Sale::where('route_unit_schedule_id', $schedule->id)
                        ->get()
                        ->pluck('data')
                        ->map(function($data) {
                            $decoded = is_string($data) ? json_decode($data, true) : $data;
                            return $decoded['seats'] ?? $decoded['asientos'] ?? [];
                        })
                        ->flatten()
                        ->unique()
                        ->count();

                    $unit = $routeUnit->driverUnit->unit ?? null;
                    $totalSeats = $unit->capacity ?? 20;
                    $availableSeats = $totalSeats - $occupiedSeats;

                    $allSchedules[] = [
                        'id' => $schedule->id,
                        'route_unit_id' => $routeUnit->id,
                        'schedule_date' => $schedule->schedule_date,
                        'schedule_time' => $schedule->schedule_time,
                        'price' => $routeUnit->price ?? 250.00,
                        'available_seats' => max(0, $availableSeats),
                        'total_seats' => $totalSeats,
                        'unit_id' => $unit->id ?? null,
                        'capacity' => $totalSeats,
                        'rate_id' => $routeUnit->rate_id ?? 1,
                        'origin' => $route->locationStart->locality,
                        'destination' => $route->locationEnd->locality,
                        'unit_model' => $unit->model ?? 'Vehículo',
                        'license_plate' => $unit->plate ?? 'N/A',
                        'driver_name' => 'Conductor',
                        'estimated_duration_minutes' => 120
                    ];
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $allSchedules,
            'message' => 'Horarios obtenidos correctamente',
            'params' => ['origin' => $origin, 'destination' => $destination]
        ]);
    }
}
