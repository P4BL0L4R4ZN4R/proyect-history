<?php

namespace App\Http\Controllers\API;

use App\Models\Form;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FormController extends Controller
{
    // Listar todos (solo admin)
    public function index() {
        return response()->json(Form::all());
    }

    // Crear cotización (desde frontend)
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'comments' => 'nullable|string',
        ]);
        $form = Form::create($request->only(['name', 'email', 'phone', 'comments']));
        return response()->json($form, 201);
    }

    // Opcional: ver una cotización
    public function show($id) {
        return response()->json(Form::findOrFail($id));
    }
}