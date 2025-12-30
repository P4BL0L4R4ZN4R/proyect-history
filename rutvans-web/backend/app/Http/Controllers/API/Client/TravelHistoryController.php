<?php

namespace App\Http\Controllers\API\Client;

use Illuminate\Http\Request;
use App\Models\TravelHistory;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TravelHistoryController extends Controller
{
    public function getRecentTrips(Request $request)
    {
        try {
            $userId = $request->query('user_id');
            if (!$userId) {
                return response()->json(['error' => 'user_id es requerido'], 400);
            }

            if (!User::where('id', $userId)->exists()) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $trips = TravelHistory::query()
                ->whereHas('sale', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->with([
                    'sale' => function ($query) {
                        $query->select('id', 'user_id', 'amount');
                    },
                    'routeUnitSchedule' => function ($query) {
                        $query->select('id', 'route_unit_id', 'schedule_date', 'schedule_time');
                    },
                    'routeUnitSchedule.routeUnit' => function ($query) {
                        $query->select('id', 'route_id', 'driver_unit_id');
                    },
                    'routeUnitSchedule.routeUnit.route' => function ($query) {
                        $query->select('id', 'location_s_id', 'location_f_id');
                    },
                    'routeUnitSchedule.routeUnit.route.locationStart' => function ($query) {
                        $query->select('id', 'locality');
                    },
                    'routeUnitSchedule.routeUnit.route.locationEnd' => function ($query) {
                        $query->select('id', 'locality');
                    },
                    'routeUnitSchedule.routeUnit.driverUnit' => function ($query) {
                        $query->select('id', 'driver_id');
                    },
                    'routeUnitSchedule.routeUnit.driverUnit.driver' => function ($query) {
                        $query->select('id', 'user_id');
                    },
                    'routeUnitSchedule.routeUnit.driverUnit.driver.user' => function ($query) {
                        $query->select('id', 'name');
                    },
                ])
                ->select(
                    'id',
                    'sale_id',
                    'route_unit_schedule_id',
                    'status',
                    'actual_departure',
                    'passenger_rating as rating',
                    'report',  // ← Agregado para consistencia
                    'created_at',
                    'updated_at'
                )
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function ($trip) {
                    $debug = [
                        'routeUnitSchedule_exists' => !is_null($trip->routeUnitSchedule),
                        'routeUnit_exists' => !is_null($trip->routeUnitSchedule?->routeUnit),
                        'route_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->route),
                        'locationStart_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->route?->locationStart),
                        'locationEnd_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->route?->locationEnd),
                        'driverUnit_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->driverUnit),
                        'driver_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->driverUnit?->driver),
                        'user_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->driverUnit?->driver?->user),
                    ];
                    Log::debug('Debug relaciones para travel_history id ' . $trip->id, $debug);

                    return [
                        'id' => $trip->id,
                        'status' => $trip->status,
                        'date' => $trip->actual_departure ?? ($trip->routeUnitSchedule?->schedule_date . ' ' . $trip->routeUnitSchedule?->schedule_time ?? 'Desconocido'),
                        'rating' => $trip->rating,
                        'report' => $trip->report ?? '',  // ← Agregado
                        'created_at' => $trip->created_at,
                        'updated_at' => $trip->updated_at,
                        'amount' => $trip->sale?->amount ?? 0,
                        'origin' => $trip->routeUnitSchedule?->routeUnit?->route?->locationStart?->locality ?? 'Desconocido',
                        'destination' => $trip->routeUnitSchedule?->routeUnit?->route?->locationEnd?->locality ?? 'Desconocido',
                        'driver_name' => $trip->routeUnitSchedule?->routeUnit?->driverUnit?->driver?->user?->name ?? 'Desconocido',
                        'time' => $trip->routeUnitSchedule?->schedule_time ?? 'Desconocido'
                    ];
                });

            return response()->json($trips, 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener los viajes: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los viajes'], 500);
        }
    }

    public function updateTravelRating(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'passenger_rating' => 'nullable|integer|between:1,5',
                'report' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $travel = TravelHistory::with('sale')->find($id);
            if (!$travel) {
                return response()->json(['error' => 'Viaje no encontrado'], 404);
            }

            if ($travel->sale->user_id !== auth()->id()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            // 🔹 con fillable ya puedes hacer esto en una sola línea:
            $travel->update($request->only(['passenger_rating', 'report']));

            return response()->json([
                'message' => 'Viaje actualizado correctamente',
                'travel' => [
                    'id' => $travel->id,
                    'passenger_rating' => $travel->passenger_rating,
                    'report' => $travel->report,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar viaje: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el viaje'], 500);
        }
    }

    public function getAllTravelHistory(Request $request)
    {
        try {
            $userId = $request->query('user_id');
            if (!$userId) {
                return response()->json(['error' => 'user_id es requerido'], 400);
            }

            if (!User::where('id', $userId)->exists()) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $trips = TravelHistory::query()
                ->whereHas('sale', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->with([
                    'sale' => function ($query) {
                        $query->select('id', 'user_id', 'amount');
                    },
                    'routeUnitSchedule' => function ($query) {
                        $query->select('id', 'route_unit_id', 'schedule_date', 'schedule_time');
                    },
                    'routeUnitSchedule.routeUnit' => function ($query) {
                        $query->select('id', 'route_id', 'driver_unit_id');
                    },
                    'routeUnitSchedule.routeUnit.route' => function ($query) {
                        $query->select('id', 'location_s_id', 'location_f_id');
                    },
                    'routeUnitSchedule.routeUnit.route.locationStart' => function ($query) {
                        $query->select('id', 'locality');
                    },
                    'routeUnitSchedule.routeUnit.route.locationEnd' => function ($query) {
                        $query->select('id', 'locality');
                    },
                    'routeUnitSchedule.routeUnit.driverUnit' => function ($query) {
                        $query->select('id', 'driver_id');
                    },
                    'routeUnitSchedule.routeUnit.driverUnit.driver' => function ($query) {
                        $query->select('id', 'user_id');
                    },
                    'routeUnitSchedule.routeUnit.driverUnit.driver.user' => function ($query) {
                        $query->select('id', 'name');
                    },
                ])
                ->select(
                    'id',
                    'sale_id',
                    'route_unit_schedule_id',
                    'status',
                    'actual_departure',
                    'passenger_rating as rating',
                    'report',  // ← Incluye report si no estaba
                    'created_at',
                    'updated_at'
                )
                ->orderBy('created_at', 'desc')  // ← Orden por fecha reciente
                // Sin take(3) – trae todos
                ->get()
                ->map(function ($trip) {
                    $debug = [
                        'routeUnitSchedule_exists' => !is_null($trip->routeUnitSchedule),
                        'routeUnit_exists' => !is_null($trip->routeUnitSchedule?->routeUnit),
                        'route_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->route),
                        'locationStart_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->route?->locationStart),
                        'locationEnd_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->route?->locationEnd),
                        'driverUnit_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->driverUnit),
                        'driver_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->driverUnit?->driver),
                        'user_exists' => !is_null($trip->routeUnitSchedule?->routeUnit?->driverUnit?->driver?->user),
                    ];
                    Log::debug('Debug relaciones para travel_history id ' . $trip->id, $debug);

                    return [
                        'id' => $trip->id,
                        'status' => $trip->status,
                        'date' => $trip->actual_departure ?? ($trip->routeUnitSchedule?->schedule_date . ' ' . $trip->routeUnitSchedule?->schedule_time ?? 'Desconocido'),
                        'rating' => $trip->rating,
                        'report' => $trip->report ?? '',  // ← Agrega report
                        'created_at' => $trip->created_at,
                        'updated_at' => $trip->updated_at,
                        'amount' => $trip->sale?->amount ?? 0,
                        'origin' => $trip->routeUnitSchedule?->routeUnit?->route?->locationStart?->locality ?? 'Desconocido',
                        'destination' => $trip->routeUnitSchedule?->routeUnit?->route?->locationEnd?->locality ?? 'Desconocido',
                        'driver_name' => $trip->routeUnitSchedule?->routeUnit?->driverUnit?->driver?->user?->name ?? 'Desconocido',
                        'time' => $trip->routeUnitSchedule?->schedule_time ?? 'Desconocido'
                    ];
                });

            // ✅ FIX: Log correcto (usa Log::info en lugar de print)
            Log::info('✅ [HistoryController] Obtenidos ' . $trips->count() . ' viajes completos para user_id: ' . $userId);

            return response()->json($trips, 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener historial completo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener el historial'], 500);
        }
    }
}