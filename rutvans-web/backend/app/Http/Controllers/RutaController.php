<?php

// app/Http/Controllers/RutaController.php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Locality;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    public function index()
    {
      $rutas = Route::with(['locationStart', 'locationEnd'])->get();
    $localities = Locality::all();

    return view('rutas.index', compact('rutas', 'localities'));

    }

    public function store(Request $request)
    {
        $request->validate([
    'id_location_s' => 'required|exists:localities,id',
    'id_location_f' => 'required|exists:localities,id',
]);
        Route::create($request->all());
        return redirect()->back()->with('success', 'Ruta creada correctamente');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
    'id_location_s' => 'required|exists:localities,id',
    'id_location_f' => 'required|exists:localities,id',
]);

        $ruta = Route::findOrFail($id);
        $ruta->update($request->all());
        return redirect()->back()->with('success', 'Ruta actualizada correctamente');
    }

    public function destroy($id)
    {
        Route::destroy($id);
        return redirect()->back()->with('success', 'Ruta eliminada correctamente');
    }
}
