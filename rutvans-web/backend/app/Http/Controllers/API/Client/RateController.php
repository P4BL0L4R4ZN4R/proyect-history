<?php

namespace App\Http\Controllers\API\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RateController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
            'message' => 'RateController funcionando correctamente'
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Rate store method funcionando'
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => ['id' => $id],
            'message' => 'Rate show method funcionando'
        ]);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Rate update method funcionando'
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Rate destroy method funcionando'
        ]);
    }
}
