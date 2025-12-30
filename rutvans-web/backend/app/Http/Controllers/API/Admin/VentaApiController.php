<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;

class VentaApiController extends Controller
{
    public function index()
{
    return response()->json(sale::all());
}

}
