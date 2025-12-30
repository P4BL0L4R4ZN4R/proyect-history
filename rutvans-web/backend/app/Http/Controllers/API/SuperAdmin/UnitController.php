<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        return response()->json(Unit::orderBy('created_at', 'desc')->get());
    }

    public function show($id)
    {
        return response()->json(Unit::findOrFail($id));
    }
}