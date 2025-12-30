<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RouteUnitSchedule;
use Illuminate\Support\Facades\Log;

class HorarioController extends Controller
{
    public function index()
    {
        $horarios = RouteUnitSchedule::all();
        return view('horarios.index', compact('horarios'));
    }

    public function apiIndex()
    {
        return response()->json(RouteUnitSchedule::all());
    }

    public function create()
    {
        return view('hora.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_route_unit' => 'required|exists:route_unit,id',
            'schedule_date' => 'required|date',
            'schedule_time' => 'required|date_format:H:i',
            'status' => 'required|string|max:50',
        ]);

        RouteUnitSchedule::create($request->only(['id_route_unit', 'schedule_date', 'schedule_time', 'status']));
        return redirect()->route('horarios.index')->with('success', '¡Horario creado exitosamente!');
    }

    public function update(Request $request, RouteUnitSchedule $horario)
    {
        $request->validate([
            'id_route_unit' => 'required|exists:route_unit,id',
            'schedule_date' => 'required|date',
            'schedule_time' => 'required|date_format:H:i',
            'status' => 'required|string|max:50',
        ]);

        $horario->update($request->only(['id_route_unit', 'schedule_date', 'schedule_time', 'status']));
        return redirect()->route('horarios.index')->with('success', '¡Horario actualizado correctamente!');
    }

    public function destroy(RouteUnitSchedule $horario)
    {
        try {
            $horario->delete();
            return response()->json(['success' => true, 'message' => '¡Horario eliminado correctamente!']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar horario: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el horario'], 500);
        }
    }
}
