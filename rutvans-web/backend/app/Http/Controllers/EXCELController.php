<?php

namespace App\Http\Controllers;

use App\Exports\LocalidadesExport; // Importa la clase de exportación de Excel
use Illuminate\Http\Request; // Clase para manejar solicitudes HTTP
use App\Models\Locality; // Modelo que representa las localidades
use Maatwebsite\Excel\Facades\Excel; // Facade para la exportación a Excel
use App\Models\Unit;
use App\Exports\UnitsExportExcel;

class EXCELController extends Controller
{
    public function expLocalidades(Request $request)
    {
        // Crea una consulta base para el modelo Locality
        $query = Locality::query();

        // Aplica filtro por término de búsqueda (si existe en la solicitud)
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('locality', 'like', '%' . $request->search . '%') // Filtra por localidad
                    ->orWhere('street', 'like', '%' . $request->search . '%'); // O filtra por calle
            });
        }

        // Aplica filtro por fecha específica (si existe en la solicitud)
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '=', $request->start_date); // Filtra por fecha exacta en created_at
        }

        // Exporta los resultados de la consulta a un archivo Excel
        return Excel::download(new LocalidadesExport($query), 'localidades.xlsx'); // Descarga el archivo con los datos exportados
    }

    public function expUnidades(Request $request)
    {
        $query = Unit::with(['drivers.user']);

        if ($request->has('search') && !empty($request->search)) {
            $query->where('plate', 'like', '%' . $request->search . '%')
                ->orWhereHas('drivers.user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }

        $units = $query->get();

        return Excel::download(new UnitsExportExcel($units), 'unidades.xlsx');
    }
}
