<?php

namespace App\Http\Controllers\API\Client;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ShipmentController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('Solicitud recibida en /api/shipments:', $request->all());

            $validated = $request->validate([
                'sender_name' => 'required|string|max:100',
                'receiver_name' => 'required|string|max:100',
                'amount' => 'required|numeric',
                'package_image' => 'nullable|string|max:255',
                'package_description' => 'required|string|max:255',
                'id_route_unit' => 'required|exists:route_units,id',
                'id_service' => 'required|exists:services,id',
                'status' => 'nullable|string|max:50',
            ]);

            $shipment = Shipment::create($validated);

            return response()->json([
                'message' => 'Envio registrado correctamente',
                'shipment' => $shipment
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Errores de validacion',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al registrar el envio: '.$e->getMessage(), [
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
