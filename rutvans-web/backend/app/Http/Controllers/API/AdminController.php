<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Site;
use App\Models\Unit;
use App\Models\Complaint;
use App\Models\Comment;
use App\Models\Form;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function stats()
    {
        try {
            return response()->json([
                'total_users' => User::count(),
                'total_sites' => Site::count(),
                'total_complaints' => Complaint::count(),
                'total_comments' => Comment::count(),
                'total_forms' => Form::count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function formsMonthly()
    {
        $data = Form::selectRaw("DATE_FORMAT(created_at, '%b') as mes, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("YEAR(created_at), MONTH(created_at)"))
            ->orderByRaw("YEAR(created_at), MONTH(created_at)")
            ->get();

        return response()->json($data);
    }
    public function dashboard()
    {
        // Armado de datos de ejemplo, puedes completar con consultas avanzadas.
        return response()->json([
            'stats' => $this->stats()->getData(),
            'recent_activity' => [], // Puedes agregar aquí tu lógica.
            'generated_at' => now()->toIso8601String()
        ]);
    }
    public function sitesCount()
    {
        return response()->json(['count' => Site::count()]);
    }
    public function unitsCount()
    {
        return response()->json(['count' => Unit::count()]);
    }
    public function sites()
    {
        $sites = Site::orderBy('created_at', 'desc')->get();
        return response()->json($sites);
    }
}