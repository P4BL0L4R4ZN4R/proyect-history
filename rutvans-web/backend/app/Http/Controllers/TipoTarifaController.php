<?php

namespace App\Http\Controllers;

use App\Models\TipoTarifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TipoTarifaController extends Controller
{
    /**
     * Muestra todos los tipos de tarifa.
     */
    public function index()
    {
        $tarifas = TipoTarifa::all();
        return view('tarifas.index', compact('tarifas'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        return view('tarifas.create');
    }

    /**
     * Guarda un nuevo tipo de tarifa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'porcentaje'  => 'required|numeric|min:0|max:100',
            'descripcion' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            TipoTarifa::create([
                'name'        => $request->nombre,
                'percentage'  => $request->porcentaje,
                'description' => $request->descripcion,
            ]);
            Log::info("Nuevo tipo de tarifa creado: {$request->nombre}");
        });

        return redirect()->route('tarifas.index')->with('success', '¡Tipo de tarifa creado exitosamente!');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(TipoTarifa $tipoTarifa)
    {
        return view('tarifas.edit', compact('tipoTarifa'));
    }

    /**
     * Actualiza un tipo de tarifa existente.
     */
 public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'percentage' => 'required|numeric|min:0|max:100',
        'description' => 'nullable|string',
    ]);

    $tipo = TipoTarifa::findOrFail($id);
    $tipo->update([
        'name' => $request->name,
        'percentage' => $request->percentage,
        'description' => $request->description,
    ]);

    return redirect()->back()->with('success', 'Tipo de tarifa actualizado correctamente.');
}


    /**
     * Elimina un tipo de tarifa.
     */
    public function destroy($id)
    {
        $tipoTarifa = TipoTarifa::findOrFail($id);

        DB::transaction(function () use ($tipoTarifa) {
            $nombre = $tipoTarifa->name;
            $tipoTarifa->delete();
            Log::info("Tipo de tarifa eliminado: {$nombre}");
        });

        return response()->json(['success' => true, 'message' => '¡Tipo de tarifa eliminado correctamente!']);
    }

    /**
     * API: Devuelve todos los tipos de tarifa.
     */
    public function apiIndex()
    {
        return response()->json(TipoTarifa::all());
    }
}

