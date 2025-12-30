<?php

namespace App\Http\Controllers\API\SuperAdmin;

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
            'content' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date'
        ]);
        
        $complaint = Complaint::create([
            'content' => $request->content,
            'user_id' => $request->user_id,
            'date' => $request->date
        ]);
        
        return response()->json($complaint, 201);
    }

    public function show($id) {
        return response()->json(Complaint::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $complaint = Complaint::findOrFail($id);
        $request->validate([
            'content' => 'sometimes|required|string',
            'response' => 'sometimes|required|string',
            'response_date' => 'sometimes|required|date'
        ]);
        $complaint->update($request->only(['content', 'response', 'response_date']));
        return response()->json($complaint);
    }

    public function destroy($id) {
        Complaint::destroy($id);
        return response()->json(['message' => 'Queja eliminada'], 204);
    }

    public function userComplaints($userId) {
        $complaints = Complaint::where('user_id', $userId)
                            ->orderBy('created_at', 'desc')
                            ->get();
        return response()->json($complaints);
    }

    public function monthly() {
        $complaints = Complaint::selectRaw('MONTH(created_at) as month_num, MONTHNAME(created_at) as mes, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month_num', 'mes')
            ->orderBy('month_num')
            ->get()
            ->map(function ($item) {
                return [
                    'mes' => $item->mes,
                    'total' => $item->total
                ];
            });

        // Si no hay datos, crear array con todos los meses en cero
        if ($complaints->isEmpty()) {
            $meses = [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ];
            
            $complaints = collect($meses)->map(function($mes) {
                return [
                    'mes' => $mes,
                    'total' => 0
                ];
            });
        }

        return response()->json($complaints);
    }
}