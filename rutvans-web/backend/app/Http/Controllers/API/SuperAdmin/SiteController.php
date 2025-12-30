<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Site;

class SiteController extends Controller
{
    public function index()
    {
        return response()->json(Site::orderBy('created_at', 'desc')->get());
    }

    public function show($id)
    {
        return response()->json(Site::findOrFail($id));
    }
}