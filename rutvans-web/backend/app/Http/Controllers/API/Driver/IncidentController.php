<?php

namespace App\Http\Controllers\API\Driver;

use Illuminate\Http\Request;


use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
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
public function show($id)
{
   
    
    try {
        
        $userId = $id;
        // Validación
        validator(['user_id' => $userId], [
            'user_id' => 'required|integer|exists:users,id',
        ])->validate();

        // Consulta
        $incidencias = DB::table('incidents')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        if($incidencias->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No se encontraron incidencias',
                'data' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $incidencias
        ]);

    } catch (\Exception $e) {
        \Log::error('Error en ApiIncidenciaController: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Error interno del servidor'
        ], 500);
    }
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
        try {
            // Validar que el ID es numérico
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'ID de incidencia no válido'
                ], 400);
            }
    
            // Buscar la incidencia
            $incidencia = DB::table('incidents')->where('id', $id)->first();
    
            // Verificar si existe
            if (!$incidencia) {
                return response()->json([
                    'success' => false,
                    'error' => 'Incidencia no encontrada'
                ], 404);
            }
    
            // Opcional: Verificar que el usuario tiene permisos para eliminar esta incidencia
            // $currentUserId = auth()->id(); // Si usas autenticación
            // if ($incidencia->user_id != $currentUserId) {
            //     return response()->json([
            //         'success' => false,
            //         'error' => 'No autorizado para eliminar esta incidencia'
            //     ], 403);
            // }
    
            // Eliminar la incidencia
            DB::table('incidents')->where('id', $id)->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Incidencia eliminada correctamente',
                'deleted_id' => $id
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Error al eliminar incidencia: '.$e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error interno al eliminar la incidencia',
                'details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    
    
    
}
