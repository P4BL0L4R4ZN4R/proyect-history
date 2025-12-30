<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Locality;

class LocalidadesController extends Controller
{
    /**
     * Mostrar la lista de localidades y pasar conteo al index.
     */
    public function index()
    {
        $localidades = Locality::all();
        $localidadesCount = $localidades->count();

        return view('localidades.index', compact('localidades', 'localidadesCount'));
    }

    /**
     * Vista de debug para ver todas las localidades
     */
    public function debug()
    {
        $localities = Locality::all();
        return view('localidades.debug', compact('localities'));
    }

    /**
     * Guardar una nueva localidad en la base de datos.
     */
    public function store(Request $request)
    {
        // Validamos todos los campos: municipality, state y locality_type ahora son nullable
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

        // Asegurar valores por defecto para campos importantes
        $validated['country'] = $validated['country'] ?: 'México';
        $validated['municipality'] = $validated['municipality'] ?: null;
        $validated['state'] = $validated['state'] ?: null;
        $validated['postal_code'] = $validated['postal_code'] ?: null;
        $validated['locality_type'] = $validated['locality_type'] ?: null;

        Locality::create($validated);

        return redirect()->route('localidades.index')
                         ->with('success', '¡Ubicación guardada correctamente!');
    }

    /**
     * Actualizar una localidad existente.
     */
    public function update(Request $request, $id)
    {
        $localidad = Locality::findOrFail($id);

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

        return redirect()->route('localidades.index')
                         ->with('success', '¡Ubicación actualizada con éxito!');
    }

    /**
     * Eliminar una localidad.
     */
    public function destroy($id)
    {
        $localidad = Locality::findOrFail($id);
        $localidad->delete();

        return redirect()->route('localidades.index')
                         ->with('success', '¡Ubicación eliminada correctamente!');
    }
}
