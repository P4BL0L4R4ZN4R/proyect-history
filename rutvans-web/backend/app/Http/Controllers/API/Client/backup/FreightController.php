<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use App\Models\Freight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FreightController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Validar que se proporcione user_id
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = $request->query('user_id');
            
            // Obtener los fletes del usuario ordenados por fecha de creación
            $freights = Freight::byUser($userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Fletes obtenidos exitosamente',
                'data' => $freights
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener fletes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los fletes'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'user_id' => 'required|integer|exists:users,id',
                'origin' => 'required|json',
                'destination' => 'required|json',
                'number_people' => 'required|integer|min:1|max:22',
                'amount' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar que el JSON de origin y destination sea válido
            $origin = json_decode($request->origin, true);
            $destination = json_decode($request->destination, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato JSON inválido en origin o destination'
                ], 422);
            }

            // Calcular el monto si no se proporciona
            $amount = $request->amount;
            if (!$amount) {
                $amount = $this->calculateAmount($request, $origin, $destination);
            }

            // Crear el flete
            $freight = Freight::create([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'user_id' => $request->user_id,
                'origin' => $request->origin,
                'destination' => $request->destination,
                'number_people' => $request->number_people,
                'status' => 'Pendiente',
                'amount' => $amount,
                'site_id' => 1, // Valor por defecto
                'service_id' => 1, // Valor por defecto
                'driver_id' => null, // Se asignará luego
            ]);

            Log::info('Flete creado exitosamente: ' . $freight->id);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud de flete creada exitosamente',
                'data' => $freight
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear flete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la solicitud de flete'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $freight = Freight::find($id);

            if (!$freight) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud de flete no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Flete obtenido exitosamente',
                'data' => $freight
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener flete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la solicitud de flete'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $freight = Freight::find($id);

            if (!$freight) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud de flete no encontrada'
                ], 404);
            }

            // Solo permitir cancelar fletes pendientes
            if (!$freight->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden cancelar solicitudes con estado Pendiente'
                ], 400);
            }

            $freight->delete();

            Log::info('Flete cancelado: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud de flete cancelada exitosamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al cancelar flete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la solicitud de flete'
            ], 500);
        }
    }

    /**
     * Calcular el monto del flete basado en diferentes factores
     */
    private function calculateAmount(Request $request, array $origin, array $destination)
    {
        // Cálculo simple temporal - luego se implementará con Google Maps Distance Matrix API
        $basePrice = 200.0;
        $pricePerPerson = 20.0;
        $pricePerHour = 50.0;
        
        $numberPeople = $request->number_people;
        
        // Calcular horas estimadas
        $startTime = strtotime($request->start_time);
        $endTime = strtotime($request->end_time);
        $hours = max(1, ($endTime - $startTime) / 3600); // Mínimo 1 hora
        
        $amount = $basePrice + 
                 ($pricePerPerson * $numberPeople) + 
                 ($pricePerHour * $hours);
        
        Log::info('Monto calculado para flete: ' . $amount);
        
        return round($amount, 2);
    }

    /**
     * Actualizar estado de un flete (para pagos)
     */
    public function updateStatus(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Pendiente,En progreso,"En progreso, Pagado",Completado'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estado inválido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $freight = Freight::find($id);

            if (!$freight) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud de flete no encontrada'
                ], 404);
            }

            // Validar transición de estado
            $newStatus = $request->status;
            $currentStatus = $freight->status;

            // Validar que la transición de estado sea válida
            if (!$this->isValidStatusTransition($currentStatus, $newStatus)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transición de estado no válida'
                ], 400);
            }

            $freight->update(['status' => $newStatus]);

            Log::info('Estado de flete actualizado: ' . $id . ' - ' . $currentStatus . ' -> ' . $newStatus);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => $freight
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al actualizar estado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ], 500);
        }
    }

    /**
     * Validar transición de estados
     */
    private function isValidStatusTransition($currentStatus, $newStatus)
    {
        $validTransitions = [
            'Pendiente' => ['En progreso', 'Completado'],
            'En progreso' => ['En progreso, Pagado', 'Completado'],
            'En progreso, Pagado' => ['Completado'],
            'Completado' => [] // No se puede cambiar desde completado
        ];

        return in_array($newStatus, $validTransitions[$currentStatus] ?? []);
    }
}