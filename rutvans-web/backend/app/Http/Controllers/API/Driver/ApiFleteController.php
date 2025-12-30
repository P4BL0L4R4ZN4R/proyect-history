<?php

namespace App\Http\Controllers\API\Driver;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ApiFleteController extends Controller
{


        /**
     * Display the specified driver's performance statistics.
     *
     * @param  string  $driverId
     * @return \Illuminate\Http\JsonResponse
     */


    public function index()
    {
        $flete = DB::table('freights')
            ->get();

        return response()->json(['flete' => $flete], 200);
    }


    public function show($id)
    {
        $flete = DB::table('freights')
            ->where('driver_id', $id)
            ->get();

        if (!$flete) {
            return response()->json(['message' => 'Flete not found'], 404);
        }

        return response()->json(['flete' => $flete], 200);
    }


        public function edit($id)
    {
        $flete = DB::table('freights')
            ->where('id', $id)
            ->get();

        if (!$flete) {
            return response()->json(['message' => 'Flete not found'], 404);
        }

        return response()->json(['flete' => $flete], 200);
    }


    public function update(Request $request, $id)
    {

        $flete = DB::table('freights')
            ->where('id', $id)
            ->update($request->all());

        if (!$flete) {
            return response()->json(['message' => 'Flete not found'], 404);
        }

        return response()->json(['message' => 'Flete updated successfully'], 200);
    }


}