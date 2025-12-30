<?php

namespace App\Http\Controllers\API\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
            'message' => 'PaymentController funcionando correctamente'
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Payment store method funcionando'
        ]);
    }
}
