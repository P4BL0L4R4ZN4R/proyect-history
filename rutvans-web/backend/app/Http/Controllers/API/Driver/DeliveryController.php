<?php

namespace App\Http\Controllers\API\Driver;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{


public function show($id)
{
    $shipments = DB::table('shipments')
        ->where('shipments.id', $id)
        ->leftJoin('route_unit', 'shipments.route_unit_id', '=', 'route_unit.id')
        ->leftJoin('localities', 'route_unit.intermediate_location_id', '=', 'localities.id')
        ->select(
            // Campos de shipments (excluyendo user_id)
            'shipments.*',
            // Campo de localities (solo locality)
            'localities.locality as locality'
        )
        ->first();

    if (!$shipments) {
        return response()->json(['message' => 'Envío no encontrado'], 404);
    }

    return response()->json(['shipments' => $shipments], 200);
}


    public function index()
    {
        $shipments = DB::table('shipments')
            ->leftJoin('route_unit', 'shipments.route_unit_id', '=', 'route_unit.id')
            ->leftJoin('localities', 'route_unit.intermediate_location_id', '=', 'localities.id')
            ->select(
                'shipments.*',
                'localities.locality as locality'
            )
            ->get(); // Devuelve todos los registros

        if ($shipments->isEmpty()) {
            return response()->json(['message' => 'No hay envíos registrados'], 404);
        }

        return response()->json(['shipments' => $shipments], 200);
    }



        public function update(Request $request, $id)
    {

        $shipments = DB::table('shipments')
            ->where('id', $id)
            ->update($request->all());

        if (!$shipments) {
            return response()->json(['message' => 'shipments not found'], 404);
        }

        return response()->json(['message' => 'shipments updated successfully'], 200);
    }

}
