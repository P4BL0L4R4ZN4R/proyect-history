<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RouteUnit;
use App\Models\Route;
use App\Models\DriverUnit;
use App\Models\Locality;

class RutasUnidadesController extends Controller
{
    public function index()
    {
        $rutaUnidades = RouteUnit::with([
            'route.source',
            'route.destination',
            'driverUnit.unit',
            'driverUnit.driver.user'
        ])->get();

        $rutas = Route::with(['source', 'destination'])->get();
        $driverUnits = DriverUnit::with(['unit', 'driver.user'])->get();
        $localidades = Locality::all(); 

        return view('rutaunidad.index', compact('rutaUnidades', 'rutas', 'driverUnits', 'localidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_route' => 'required|exists:routes,id',
            'id_driver_unit' => 'required|exists:driver_unit,id',
            'price' => 'required|numeric|min:0'
        ]);

        RouteUnit::create($request->only(['id_route', 'id_driver_unit', 'price']));

        return redirect()->route('rutas-unidades.index')->with('success', 'Ruta asignada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_route' => 'required|exists:routes,id',
            'id_driver_unit' => 'required|exists:driver_unit,id',
            'price' => 'required|numeric|min:0'
        ]);

        $rutaUnidad = RouteUnit::findOrFail($id);
        $rutaUnidad->update($request->only(['id_route', 'id_driver_unit', 'price']));

        return redirect()->route('rutas-unidades.index')->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $registro = RouteUnit::findOrFail($id);
        $registro->delete();

        return redirect()->route('rutas-unidades.index')->with('success', 'Asignación eliminada.');
    }
}
