<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Locality;

class LocExpController extends Controller
{
    // Función index para mostrar la tabla de localidades
    public function index()
    {
        // Obtén todas las localidades
        $localidades = Locality::paginate(10); // Paginación de 10 localidades por página

        // Retorna la vista junto con las localidades
        return view('exports.localidades.index', compact('localidades'));
    }
}
