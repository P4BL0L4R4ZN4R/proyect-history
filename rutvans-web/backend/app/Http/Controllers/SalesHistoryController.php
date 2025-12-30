<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SalesHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::query();

        // Si llega una fecha, filtra por esa fecha
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        } else {
            // Si no, usa la fecha actual por defecto
            $query->whereDate('created_at', now()->toDateString());
        }

        $sales = $query->orderByDesc('created_at')->get();

        // Agrupar por fecha
        $salesGrouped = $sales->groupBy(function ($sale) {
            return $sale->created_at->toDateString();
        });

        return view('sales.history', compact('salesGrouped'));
    }

}
