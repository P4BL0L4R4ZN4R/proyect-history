<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Locality;

class LocalidadesApiController extends Controller
{
    public function index()
    {
        return response()->json(Locality::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'longitude'      => 'required|numeric',
            'latitude'       => 'required|numeric',
            'locality'       => 'required|string|max:255',
            'street'         => 'nullable|string|max:255',
            'postal_code'    => 'nullable|string|max:20',
            'municipality'   => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'country'        => 'required|string|max:100',
            'locality_type'  => 'nullable|string|max:50',
        ]);

        $localidad = Locality::create($validated);

        return response()->json($localidad, 201);
    }
public function update(Request $request, $id)
{
    $localidad = Locality::find($id);

    if (!$localidad) {
        return response()->json(['message' => 'Localidad no encontrada'], 404);
    }

    $validated = $request->validate([
        'longitude'      => 'required|numeric',
        'latitude'       => 'required|numeric',
        'locality'       => 'required|string|max:255',
        'street'         => 'nullable|string|max:255',
        'postal_code'    => 'nullable|string|max:20',
        'municipality'   => 'nullable|string|max:100',
        'state'          => 'nullable|string|max:100',
        'country'        => 'required|string|max:100',
        'locality_type'  => 'nullable|string|max:50',
    ]);

    $localidad->update($validated);

    return response()->json([
        'message' => 'Localidad actualizada correctamente',
        'data'    => $localidad
    ]);
}

    
   public function destroy($id)
{
    $locality = Locality::find($id);

    if (!$locality) {
        return response()->json(['message' => 'Localidad no encontrada'], 404);
    }

    $locality->delete();

    return response()->json(['message' => 'Localidad eliminada correctamente'], 200);
}
}
