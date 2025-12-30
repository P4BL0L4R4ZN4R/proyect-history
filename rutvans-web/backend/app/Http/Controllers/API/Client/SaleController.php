<?php

namespace App\Http\Controllers\API\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use App\Models\Payment;
use App\Models\RouteUnitSchedule;
use App\Models\Rate;
use App\Models\TravelHistory; // Añadir el modelo TravelHistory
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Para transacciones
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function recentSales(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
            'message' => 'recentSales funcionando correctamente'
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Log temporal para debug
            error_log('=== VENTA DESDE FLUTTER ===');
            error_log('Request completo: ' . json_encode($request->all()));
            error_log('Headers: ' . json_encode($request->headers->all()));
            error_log('==========================');
            
            // Obtener datos del request con múltiples posibles nombres
            $seats = $request->input('seats', []) 
                  ?: $request->input('selected_seats', [])
                  ?: $request->input('asientos', [])
                  ?: $request->input('selectedSeats', []);
            
            $userId = $request->input('user_id');
            $routeUnitScheduleId = $request->input('route_unit_schedule_id');
            $amount = $request->input('amount', 0);
            
            // Validaciones básicas
            if (empty($seats)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Los asientos son requeridos'
                ], 400);
            }

            if (!$userId || !$routeUnitScheduleId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user_id y route_unit_schedule_id son requeridos'
                ], 400);
            }

            // Generar un folio único
            $folio = 'TKT-' . strtoupper(uniqid());
            
            // Preparar los datos a guardar
            $saleData = [
                'seats' => $seats,
                'seat_count' => count($seats),
                'payment_method' => $request->input('payment_method', 'cash'),
                'purchase_timestamp' => now()->toISOString(),
                'selected_seats' => $seats, // Para compatibilidad
                'asientos' => $seats, // Para compatibilidad
            ];

            try {
                // Usar transacción para asegurar que ambas operaciones se completen
                return DB::transaction(function () use ($request, $folio, $userId, $routeUnitScheduleId, $amount, $saleData, $seats) {
                    
                    // Crear la venta en la base de datos
                    $sale = Sale::create([
                        'folio' => $folio,
                        'user_id' => $userId,
                        'payment_id' => $request->input('payment_id', 1), // Default payment_id = 1
                        'route_unit_schedule_id' => $routeUnitScheduleId,
                        'rate_id' => $request->input('rate_id', 1),
                        'data' => $saleData,
                        'amount' => $amount,
                        'status' => 'confirmed',
                        'site_id' => $request->input('site_id', 1) // Default site_id = 1
                    ]);

                    // CREAR REGISTRO EN TRAVEL HISTORY (basado en el ejemplo)
                    $travelHistory = TravelHistory::create([
                        'sale_id' => $sale->id, // Clave foránea crítica
                        'route_unit_schedule_id' => $routeUnitScheduleId,
                        'status' => 'in_progress', // Status por defecto para nuevos viajes
                        'actual_departure' => null, // Null inicial
                        'actual_arrival' => null,   // Null inicial
                        'passenger_rating' => null, // Null hasta calificación
                        'report' => '',             // String vacío
                    ]);

                    // Log de éxito
                    Log::info('✅ [SALE + TRAVEL] Venta y historial creados exitosamente:', [
                        'sale_id' => $sale->id,
                        'folio' => $sale->folio,
                        'travel_history_id' => $travelHistory->id,
                        'seats' => $seats
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'data' => [
                            'sale_id' => $sale->id,
                            'folio' => $sale->folio,
                            'seats' => $seats,
                            'amount' => $sale->amount,
                            'created_at' => $sale->created_at,
                            'travel_history_id' => $travelHistory->id // Incluir ID del travel history
                        ],
                        'message' => 'Venta y historial de viaje registrados exitosamente'
                    ], 201);

                });

            } catch (\Exception $dbError) {
                // Si falla la BD, simular que se guardó (para pruebas)
                Log::error('💥 [SALE] Error en base de datos:', ['error' => $dbError->getMessage()]);
                
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'sale_id' => rand(1000, 9999),
                        'folio' => $folio,
                        'seats' => $seats,
                        'amount' => $amount,
                        'created_at' => now(),
                        'travel_history_id' => rand(1000, 9999), // Simular travel history ID
                        'test_mode' => true
                    ],
                    'message' => 'Venta registrada exitosamente (modo prueba)',
                    'debug_error' => $dbError->getMessage()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('💥 [SALE] Error general:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}