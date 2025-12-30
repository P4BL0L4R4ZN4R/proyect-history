<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Models\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function index() {
        return response()->json(Faq::all());
    }

    public function store(Request $request) {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);
        $faq = Faq::create($request->only(['question', 'answer']));
        return response()->json($faq, 201);
    }

    public function show($id) {
        return response()->json(Faq::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $faq = Faq::findOrFail($id);
        $request->validate([
            'question' => 'sometimes|required|string',
            'answer' => 'sometimes|required|string',
        ]);
        $faq->update($request->only(['question', 'answer']));
        return response()->json($faq);
    }

    public function destroy($id) {
        Faq::destroy($id);
        return response()->json(['message' => 'FAQ eliminado'], 200); // <-- cambia 204 por 200
    }
}