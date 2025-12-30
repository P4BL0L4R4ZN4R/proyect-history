<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    /**
     * Retorna todos los sitios que pertenecen a las compañías activas del usuario logueado
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no autenticado.'
            ], 401);
        }

        // Obtener IDs de las compañías activas del usuario
        $companyIds = $user->activeCompanies()->pluck('companies.id')->toArray();

        if (empty($companyIds)) {
            return response()->json([
                'message' => 'No se encontró compañía activa asociada al usuario.'
            ], 404);
        }

        // Obtener todos los sitios que pertenezcan a cualquiera de esas compañías
        $sites = Site::with('company', 'locality')
            ->whereIn('company_id', $companyIds)
            ->get();

        return response()->json($sites);
    }
}
