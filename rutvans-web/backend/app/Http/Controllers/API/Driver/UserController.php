<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
 public function edit($id)
    {
        try {
            $driver = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'email' => $driver->email,
                    'phone' => $driver->phone_number,
                    'address' => $driver->address,
                    // Agrega más campos según necesites
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo encontrar el conductor'
            ], 404);
        }
    }

    /**
     * Actualiza el perfil del conductor
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */


    public function update(Request $request, $id)
    {
    try {
        $driver = User::findOrFail($id);
        
        // Validación de datos
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20', // Recibimos 'phone' del frontend
            'address' => 'sometimes|nullable|string|max:255',
        ]);
        
        // Mapeo de campos
        if ($request->has('phone')) {
            $driver->phone_number = $validatedData['phone']; // Asignamos a phone_number en BD
        }
        
        if ($request->has('name')) {
            $driver->name = $validatedData['name'];
        }
        
        if ($request->has('address')) {
            $driver->address = $validatedData['address'];
        }
        
        if ($driver->isDirty()) {
            $driver->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'data' => [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'email' => $driver->email,
                    'phone' => $driver->phone_number, // Devolvemos como 'phone'
                    'address' => $driver->address,
                ]
            ], 200);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'No se realizaron cambios',
            'data' => $driver
        ], 200);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        \Log::error('Error al actualizar perfil: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar el perfil',
            'error' => env('APP_DEBUG') ? $e->getMessage() : null
        ], 500);
    }
}






    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
