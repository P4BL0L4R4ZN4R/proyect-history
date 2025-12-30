<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashCoordinateController extends Controller
{
    /**
     * Dashboard para Coordinate
     */
    public function index()
    {
        $user = Auth::user();
        $siteId = $user->site_id;

        // Carga los datos del coordinador
        $coordinateData = \App\Models\Coordinate::where('user_id', $user->id)->first();

        // Pásalos a la vista
        return view('dashboards.coordinate', [
            'totalUsers' => User::whereHas('sites', fn($q) => $q->where('site_id', $siteId))->count(),
            'welcomeMessage' => '¡Bienvenido Coordinador!',
            'userRole' => 'Coordinador',
            'coordinate' => $coordinateData,
        ]);
    }
}
