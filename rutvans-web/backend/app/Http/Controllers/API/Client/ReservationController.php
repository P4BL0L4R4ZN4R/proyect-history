<?php

namespace App\Http\Controllers\API\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Reservation store method funcionando'
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Reservation destroy method funcionando'
        ]);
    }

    public function getOccupiedSeats(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [],
            'message' => 'getOccupiedSeats funcionando'
        ]);
    }

    public function cancelReservation($id)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'cancelReservation funcionando'
        ]);
    }
}
