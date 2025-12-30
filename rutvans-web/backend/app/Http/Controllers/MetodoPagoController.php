<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class MetodoPagoController extends Controller
{
    public function index()
    {
        $metodos = Payment::latest()->get();
        return view('metodoPago.index', compact('metodos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_type' => 'required',
            'description' => 'nullable'
        ]);

        Payment::create($request->only(['payment_type', 'description']));

        return redirect()->route('metodoPago.index')->with('success', 'Método de pago registrado.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_type' => 'required',
            'description' => 'nullable'
        ]);

        $metodo = Payment::findOrFail($id);
        $metodo->update($request->only(['payment_type', 'description']));

        return redirect()->route('metodoPago.index')->with('updated', 'Método de pago actualizado.');
    }

    public function destroy($id)
    {
        $metodo = Payment::findOrFail($id);
        $metodo->delete();

        return redirect()->route('metodoPago.index')->with('deleted', 'Método de pago eliminado.');
    }
}
