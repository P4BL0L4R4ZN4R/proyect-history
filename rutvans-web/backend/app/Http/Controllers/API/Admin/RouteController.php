<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    // Listar todas las rutas con localidades de inicio, fin y sitio
public function index(Request $request)
{
    $user = $request->user();

    // Obtener IDs de compañías activas del usuario
    $companyIds = $user->companies
        ->filter(fn($company) => $company->pivot->status === 'active')
        ->pluck('id')
        ->toArray();

    // Filtrar rutas cuyo sitio pertenece a las compañías activas del usuario
    $routes = Route::with(['locationStart', 'locationEnd', 'site'])
        ->whereHas('site', function ($query) use ($companyIds) {
            $query->whereIn('company_id', $companyIds);
        })
        ->get();

    return response()->json($routes);
}


    // Crear nueva ruta
    public function store(Request $request)
    {
        $data = $request->validate([
            'location_s_id' => 'required|exists:localities,id',
            'location_f_id' => 'required|exists:localities,id|different:location_s_id',
            'site_id' => 'nullable|exists:sites,id',
        ]);

        $route = Route::create($data);

        return response()->json(
            Route::with(['locationStart', 'locationEnd', 'site'])->find($route->id),
            201
        );
    }

    // Mostrar una ruta específica
    public function show($id)
    {
        $route = Route::with(['locationStart', 'locationEnd', 'site'])->find($id);

        if (!$route) {
            return response()->json(['message' => 'Ruta no encontrada'], 404);
        }

        return response()->json($route);
    }

    // Actualizar una ruta
    public function update(Request $request, $id)
    {
        $route = Route::find($id);

        if (!$route) {
            return response()->json(['message' => 'Ruta no encontrada'], 404);
        }

        $data = $request->validate([
            'location_s_id' => 'sometimes|required|exists:localities,id',
            'location_f_id' => 'sometimes|required|exists:localities,id|different:location_s_id',
            'site_id' => 'nullable|exists:sites,id',
        ]);

        $route->update($data);

        return response()->json(
            Route::with(['locationStart', 'locationEnd', 'site'])->find($route->id)
        );
    }

    // Eliminar una ruta
    public function destroy($id)
    {
        $route = Route::find($id);

        if (!$route) {
            return response()->json(['message' => 'Ruta no encontrada'], 404);
        }

        $route->delete();

        return response()->json(['message' => 'Ruta eliminada correctamente']);
    }
}
