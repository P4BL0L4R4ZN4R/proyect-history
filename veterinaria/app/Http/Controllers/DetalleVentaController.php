<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentasExport;
use PDF;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DetalleVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */




    public function index()
    {
        return view('ventas.index');


    }


    public function data(Request $request)
{
    try {
        $query = DetalleVenta::with(['venta', 'producto']);

        // Validar fechas
        if ($request->has('fecha_inicio') && $request->fecha_inicio) {
            if (!strtotime($request->fecha_inicio)) {
                throw new \Exception('Formato de fecha inicio inválido');
            }
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->has('fecha_fin') && $request->fecha_fin) {
            if (!strtotime($request->fecha_fin)) {
                throw new \Exception('Formato de fecha fin inválido');
            }
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $detalles = $query->get();

        log::info($detalles);

        return response()->json([
            'data' => $detalles->map(function ($item) {
                return [
                    'folio' => $item->venta->folio ?? '---',
                    'producto_nombre' => $item->producto->nombre ?? '---',
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => (float)$item->precio_unitario,
                    'subtotal' => (float)$item->subtotal,
                    'tipo_pago' => $this->getTipoPago($item->venta->tipo_pago ?? null),
                    'total' => (float)($item->venta->total ?? 0),
                    'created_at' => $item->created_at->toDateTimeString()
                ];
            })
        ]);

    } catch (\Exception $e) {
        \Log::error('Error en detalleventa/data: ' . $e->getMessage());
        return response()->json([
            'error' => 'Error en el servidor',
            'message' => $e->getMessage()
        ], 500);
    }
}

public function getTipoPago($tipo)
{
    return match((int)$tipo) {
        1 => 'Efectivo',
        2 => 'Tarjeta',
        3 => 'Transferencia',
        default => '---'
    };
}




             /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('detalleventas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'venta_id' => 'nullable|integer|exists:ventas,id',
            'producto_id' => 'nullable|integer|exists:productos,id',
            'cantidad' => 'nullable|numeric|min:0',
            'precio_unitario' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
        ]);

        DetalleVenta::create($validated);

        return redirect()->route('detalleventas.index')->with('success', 'Detalle de venta creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detalle = DetalleVenta::findOrFail($id);
        return view('detalleventas.show', compact('detalle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $detalle = DetalleVenta::findOrFail($id);
        return view('detalleventas.edit', compact('detalle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $detalle = DetalleVenta::findOrFail($id);

        $validated = $request->validate([
            'venta_id' => 'nullable|integer|exists:ventas,id',
            'producto_id' => 'nullable|integer|exists:productos,id',
            'cantidad' => 'nullable|numeric|min:0',
            'precio_unitario' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
        ]);

        $detalle->update($validated);

        return redirect()->route('detalleventas.index')->with('success', 'Detalle de venta actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $detalle = DetalleVenta::findOrFail($id);
        $detalle->delete();

        return redirect()->route('detalleventas.index')->with('success', 'Detalle de venta eliminado correctamente.');
    }










public function exportarExcel(Request $request)
{
    try {
        // Validación de fechas
        $validated = $request->validate([
            'fecha_inicio' => 'nullable|date|before_or_equal:fecha_fin',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
        ]);

        // Procesar fechas
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio)->startOfDay() : null;
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin)->endOfDay() : null;

        // Nombre del archivo con fechas
        $fileName = 'ventas_' . ($fechaInicio ? $fechaInicio->format('Ymd') : 'inicio') .
                   '_' . ($fechaFin ? $fechaFin->format('Ymd') : 'hoy') . '.xlsx';

        return Excel::download(new VentasExport($fechaInicio, $fechaFin), $fileName, \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

    } catch (\Exception $e) {
        Log::error('Error al exportar Excel: ' . $e->getMessage());
        return back()->with('error', 'Error al generar el archivo Excel: ' . $e->getMessage());
    }
}

    public function exportarPdf(Request $request)
    {
        // Configuración para manejar errores como JSON
        $request->headers->set('Accept', 'application/json');

        try {
            // Validación de fechas mejorada
            $validator = Validator::make($request->all(), [
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first()
                ], 400);
            }

            // Configuración de memoria y tiempo
            ini_set('memory_limit', '512M');
            set_time_limit(300);

            // Procesamiento de fechas
            $fechaInicio = $request->fecha_inicio
                ? Carbon::parse($request->fecha_inicio)->startOfDay()
                : null;
            $fechaFin = $request->fecha_fin
                ? Carbon::parse($request->fecha_fin)->endOfDay()
                : null;

            // Consulta optimizada
            $ventas = DetalleVenta::with(['venta', 'producto'])
                ->when($fechaInicio && $fechaFin, function($query) use ($fechaInicio, $fechaFin) {
                    $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            if ($ventas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'No se encontraron ventas en el rango de fechas seleccionado'
                ], 404);
            }

            // Generar PDF
            $pdf = PDF::loadView('exports.ventas_pdf', [
                'ventas' => $ventas,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin
            ]);

            return $pdf->download('ventas.pdf');

        } catch (\Exception $e) {
            \Log::error('Error generando PDF: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al generar el PDF: ' . $e->getMessage(),
                'details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

}
