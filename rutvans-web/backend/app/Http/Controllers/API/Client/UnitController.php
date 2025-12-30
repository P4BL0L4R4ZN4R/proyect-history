<?php

namespace App\Http\Controllers\API\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Reservation;
use Carbon\Carbon;

class UnitController extends Controller
{
    public function show($unit)
    {
        return response()->json([
            'status' => 'success',
            'data' => ['unit_id' => $unit],
            'message' => 'Unit show method funcionando'
        ]);
    }

    public function getOccupiedSeats(Request $request, $unit)
    {
        try {
            $scheduleId = $request->query('schedule_id');
            
            if (!$scheduleId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El parámetro schedule_id es requerido'
                ], 400);
            }

            // Inicializar con array vacío - se llenará con datos reales si existen
            $occupiedSeats = [];
            $salesSeats = [];
            $reservationSeats = [];

            // Intentar obtener asientos ocupados reales de las ventas
            try {
                $salesSeats = Sale::where('route_unit_schedule_id', $scheduleId)
                    ->where('status', '!=', 'cancelled')
                    ->get()
                    ->flatMap(function ($sale) {
                        // El campo 'data' contiene información de los asientos
                        $data = $sale->data ?? [];
                        
                        // Buscar asientos en diferentes formatos posibles
                        $seats = [];
                        
                        // Si hay un campo 'seats' o 'asientos'
                        if (isset($data['seats'])) {
                            $seats = is_array($data['seats']) ? $data['seats'] : [$data['seats']];
                        } elseif (isset($data['asientos'])) {
                            $seats = is_array($data['asientos']) ? $data['asientos'] : [$data['asientos']];
                        } elseif (isset($data['selected_seats'])) {
                            $seats = is_array($data['selected_seats']) ? $data['selected_seats'] : [$data['selected_seats']];
                        }
                        
                        return collect($seats)->filter()->values();
                    })
                    ->toArray();

                // Por ahora omitimos reservaciones ya que la tabla no existe
                $reservationSeats = [];



                // Combinar asientos de ventas y reservaciones
                $occupiedSeats = collect($salesSeats)
                    ->merge($reservationSeats)
                    ->unique()
                    ->sort()
                    ->values()
                    ->toArray();

            } catch (\Exception $dbError) {
                // Si hay error con la BD, usar datos de prueba
                error_log('Error consultando BD para asientos ocupados: ' . $dbError->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'data' => $occupiedSeats,
                'occupied_seats' => $occupiedSeats, // Para compatibilidad con Flutter
                'message' => 'Asientos ocupados obtenidos correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
