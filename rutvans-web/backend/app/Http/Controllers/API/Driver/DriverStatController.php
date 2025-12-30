<?php

namespace App\Http\Controllers\API\Driver;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DriverStatController extends Controller
{
    /**
     * Display the specified driver's performance statistics.
     *
     * @param  string  $driverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($driverId)
    {
        try {
            // Validation
            $validator = Validator::make(['driver_id' => $driverId], [
                'driver_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid driver ID.',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Query
            // CAMBIO: Se corrigió el nombre de la tabla a 'driver_performance'
            $driverStats = DB::table('driver_performance')
                ->where('driver_id', $driverId)
                ->first();

            if (!$driverStats) {
                return response()->json([
                    'success' => true,
                    'message' => 'No se encontraron estadísticas para el chofer.',
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $driverStats
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in ChoferEstadisticasDesempenoController: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * This method is a placeholder and not implemented.
     */
    public function store(Request $request)
    {
        // Not implemented
    }

    /**
     * Update the specified resource in storage.
     * This method is a placeholder and not implemented.
     */
    public function update(Request $request, string $id)
    {
        // Not implemented
    }

    /**
     * Remove the specified resource from storage.
     * This method is a placeholder and not implemented.
     */
    public function destroy(string $id)
    {
        // Not implemented
    }
}