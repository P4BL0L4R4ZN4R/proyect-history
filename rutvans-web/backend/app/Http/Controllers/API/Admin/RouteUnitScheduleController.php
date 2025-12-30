<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RouteUnitSchedule; // Asegúrate de tener este modelo creado

class RouteUnitScheduleController extends Controller
{
    // Listar todos los horarios
    public function index()
    {
        $schedules = RouteUnitSchedule::all();
        return response()->json($schedules);
    }

    // Crear nuevo horario
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route_unit_id' => 'required|integer',
            'schedule_date' => 'required|date',
            'schedule_time' => 'required|string', // o 'date_format:H:i' si quieres validar hora
            'status' => 'sometimes|required|string|in:activo,inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedule = RouteUnitSchedule::create($request->all());

        return response()->json($schedule, 201);
    }

    // Mostrar un horario específico
    public function show($id)
    {
        $schedule = RouteUnitSchedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Horario no encontrado'], 404);
        }

        return response()->json($schedule);
    }

    // Actualizar horario
    public function update(Request $request, $id)
    {
        $schedule = RouteUnitSchedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Horario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'route_unit_id' => 'sometimes|required|integer',
            'schedule_date' => 'sometimes|required|date',
            'schedule_time' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|in:activo,inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedule->update($request->all());

        return response()->json($schedule);
    }

    // Eliminar horario
    public function destroy($id)
    {
        $schedule = RouteUnitSchedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Horario no encontrado'], 404);
        }

        $schedule->delete();

        return response()->json(null, 204);
    }
}
