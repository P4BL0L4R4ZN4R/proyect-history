<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanzasController extends Controller
{
    public function resumen(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $query = DB::table('sales')
            ->select(DB::raw('SUM(amount) as ingresos'), DB::raw('0 as egresos'));

        if ($desde) $query->whereDate('created_at', '>=', $desde);
        if ($hasta) $query->whereDate('created_at', '<=', $hasta);

        $result = $query->first();

        return response()->json([
            'ingresos' => (float) $result->ingresos,
            'egresos' => (float) $result->egresos,
            'balance' => (float) $result->ingresos - $result->egresos,
            'ventasPorDia' => $this->ventasAgrupadasPorDia($desde, $hasta),
            'transacciones' => $this->ventasDetalladas($desde, $hasta),
        ]);
    }

    private function ventasAgrupadasPorDia($desde, $hasta)
    {
        $query = DB::table('sales')
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(amount) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha', 'asc');

        if ($desde) $query->whereDate('created_at', '>=', $desde);
        if ($hasta) $query->whereDate('created_at', '<=', $hasta);

        return $query->get();
    }

    private function ventasDetalladas($desde, $hasta)
    {
        $query = DB::table('sales')
            ->select('id as folio', 'amount', 'created_at')
            ->orderBy('created_at', 'desc');

        if ($desde) $query->whereDate('created_at', '>=', $desde);
        if ($hasta) $query->whereDate('created_at', '<=', $hasta);

        return $query->get();
    }

    public function ventasDetalle(Request $request)
    {
        $fecha = $request->input('fecha');

        $query = DB::table('sales')
            ->select('id as folio', 'amount', 'created_at')
            ->orderBy('created_at', 'desc');

        if ($fecha) {
            $query->whereDate('created_at', '=', $fecha);
        }

        return $query->get();
    }

    public function ventasPeriodo(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $query = DB::table('sales')
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(amount) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha', 'asc');

        if ($desde) $query->whereDate('created_at', '>=', $desde);
        if ($hasta) $query->whereDate('created_at', '<=', $hasta);

        return $query->get();
    }

    public function topRutas(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $query = DB::table('sales as s')
            ->join('route_unit_schedule as rus', 's.route_unit_schedule_id', '=', 'rus.id')
            ->join('route_unit as ru', 'rus.route_unit_id', '=', 'ru.id')
            ->join('routes as r', 'ru.route_id', '=', 'r.id')
            ->select('r.id as ruta', DB::raw('SUM(s.amount) as total'))
            ->groupBy('r.id')
            ->orderByDesc('total');

        if ($desde) $query->whereDate('s.created_at', '>=', $desde);
        if ($hasta) $query->whereDate('s.created_at', '<=', $hasta);

        return $query->limit(5)->get();
    }

    public function balanceHistorico(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $query = DB::table('sales')
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(amount) as ingresos'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha', 'asc');

        if ($desde) $query->whereDate('created_at', '>=', $desde);
        if ($hasta) $query->whereDate('created_at', '<=', $hasta);

        return $query->get();
    }
}
