<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UnitController extends Controller
{
    public function index()
    {
        // Get current user's sites
        $userSites = Auth::user()->sites->pluck('id');
        
        // Filter units by sites the user has access to
        $units = Unit::with(['drivers.user'])
            ->whereIn('site_id', $userSites)
            ->get();
            
        // Filter drivers by sites the user has access to
        $drivers = Driver::with('user')
            ->whereIn('site_id', $userSites)
            ->get();
            
        return view('units.index', compact('units', 'drivers'));
    }

    public function store(Request $request)
        {
            $request->validate([
                'plate' => 'required|unique:units,plate|max:20',
                'capacity' => 'required|integer|min:1',
                'photo' => 'nullable|image|max:2048'
            ]);

            $data = $request->only(['plate', 'capacity']);
            
            // Assign to current user's first site
            $userSites = Auth::user()->sites;
            if ($userSites->count() > 0) {
                $data['site_id'] = $userSites->first()->id;
            }

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('units', 'public');
            }

            Unit::create($data);

            return redirect()->route('units.index')->with('success', 'Unidad registrada correctamente.');
        }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'plate' => 'required|unique:units,plate,' . $unit->id . '|max:20',
            'capacity' => 'required|integer|min:1',
            'photo' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['plate', 'capacity']);

        if ($request->hasFile('photo')) {
            // Eliminar foto anterior si existe
            if ($unit->photo) {
                Storage::disk('public')->delete($unit->photo);
            }
            $data['photo'] = $request->file('photo')->store('units', 'public');
        }

        $unit->update($data);

        return redirect()->route('units.index')->with('success', 'Unidad actualizada.');
    }

    public function destroy(Unit $unit)
    {
        // Eliminar foto si existe
        if ($unit->photo) {
            Storage::disk('public')->delete($unit->photo);
        }

        // Eliminar relaciones con conductores
        $unit->drivers()->detach();

        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unidad eliminada.');
    }

    public function assignDriver(Request $request, Unit $unit)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'status' => 'required|string|max:50'
        ]);

        // CORRECCIÓN: Usar 'id_driver' en lugar de 'driver_id'
        if (!$unit->drivers()->where('id_driver', $request->driver_id)->exists()) {
            $unit->drivers()->attach($request->driver_id, ['status' => $request->status]);
            return redirect()->back()->with('success', 'Conductor asignado correctamente.');
        }

        return redirect()->back()->with('error', 'Este conductor ya está asignado a la unidad.');
    }

    public function removeDriver(Unit $unit, Driver $driver)
    {
        $unit->drivers()->detach($driver->id);
        return redirect()->back()->with('success', 'Conductor desvinculado de la unidad.');
    }
}