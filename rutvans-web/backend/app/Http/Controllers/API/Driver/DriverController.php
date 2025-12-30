<?php

namespace App\Http\Controllers\API\Driver;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */


// app/Http/Controllers/AuthController.php



 public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // JOIN para traer el driver_id
        $driverData = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone_number as phone',  // Renombrar phone_nombre → phone
                'drivers.id as driver_id'
            )
            ->join('drivers', 'users.id', '=', 'drivers.user_id')
            ->where('users.id', $user->id)
            ->first();

            return response()->json([
                'user' => [
                    'id' => $driverData->id,
                    'name' => $driverData->name,
                    'email' => $driverData->email,
                    'phone' => $driverData->phone,
                    // 'profile_photo_url' => $driverData->profile_photo_url,
                    // otros campos si los necesitas
                ],
                'driver_id' => $driverData->driver_id,
                'message' => 'Login exitoso'
            ]);
        }

        return response()->json(['error' => 'Credenciales inválidas'], 401);
    }







    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
