<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->json(Company::orderBy('created_at', 'desc')->get());
    }

    public function show($id)
    {
        return response()->json(Company::findOrFail($id));
    }

    public function stats()
    {
        $total = Company::count();
        $active = Company::where('status', 'Activo')->count();
        $pending = Company::where('status', 'Pendiente')->count();
        $inactive = Company::where('status', 'Inactivo')->count();

        return response()->json([
            'total' => $total,
            'active' => $active,
            'pending' => $pending,
            'inactive' => $inactive
        ]);
    }
}