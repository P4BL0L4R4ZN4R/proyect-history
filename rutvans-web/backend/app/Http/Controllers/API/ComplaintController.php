<?php
namespace App\Http\Controllers\API;

use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ComplaintController extends Controller
{
    public function index() {
        return response()->json(Complaint::all());
    }

    public function store(Request $request) {
        $request->validate([
            'description' => 'required|string',
            // Otros campos relevantes
        ]);
        $complaint = Complaint::create($request->only(['description']));
        return response()->json($complaint, 201);
    }

    public function show($id) {
        return response()->json(Complaint::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $complaint = Complaint::findOrFail($id);
        $request->validate([
            'description' => 'sometimes|required|string',
            // Otros campos
        ]);
        $complaint->update($request->only(['description']));
        return response()->json($complaint);
    }

    public function destroy($id) {
        Complaint::destroy($id);
        return response()->json(['message' => 'Queja eliminada'], 204);
    }
}