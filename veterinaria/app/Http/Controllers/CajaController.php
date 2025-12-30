<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CajaController extends Controller
{
    /**
     * Mostrar el formulario para abrir la caja
     */
    public function create()
    {
        return view('caja.create');
    }



    public function store(Request $request)
    {
        Log::info('CajaController@store llamado', ['request' => $request->all()]);

        // $request->validate([
        //     'saldo_inicial' => 'required|numeric|min:0',
        // ]);

        $userId = auth()->id();

        // Revisar si ya hay una caja abierta
        $cajaAbierta = DB::table('corte_caja')
            ->where('user_id', $userId)
            ->where('estado', 'abierto')
            ->first();

        if ($cajaAbierta) {
            Log::info('Caja ya abierta', ['user_id' => $userId]);
            return redirect()->route('home')
                ->with('info', 'Ya existe una caja abierta.');
        }

        // Insertar nuevo registro de corte de caja
        DB::table('corte_caja')->insert([
            'user_id' => $userId,
            'saldo_inicio' => $request->saldo_inicial,
            'estado' => 'abierto',
            'created_at' => Carbon::now('America/Mexico_City'),
            'updated_at' => Carbon::now('America/Mexico_City'),
        ]);

        Log::info('Caja abierta correctamente', [
            'user_id' => $userId,
            'saldo_inicial' => $request->saldo_inicial,
        ]);

        return redirect()->route('home')
            ->with('success', 'Caja abierta correctamente.');
    }


    public function index()
    {

        return view('caja.index');

    }

    public function data()
    {
        $cajas = DB::table('corte_caja')
            ->join('users', 'corte_caja.user_id', '=', 'users.id')
            ->select(
                'corte_caja.id',
                'corte_caja.saldo_inicio',
                'corte_caja.saldo_fin',
                'corte_caja.estado',
                'corte_caja.created_at',
                'corte_caja.updated_at',
                'users.name as user_name' // Cambié 'usuario' por 'user_name'
            )
            ->orderBy('corte_caja.id', 'desc')
            ->get();

            log::info('Datos de caja obtenidos', ['cajas' => $cajas]);

        return response()->json(['data' => $cajas]);
    }

    public function show()
    {
        $userId = auth()->id();

        // Obtener la caja abierta del usuario
        $cajaAbierta = DB::table('corte_caja')
            ->where('user_id', $userId)
            ->where('estado', 'abierto')
            ->first();

        if (!$cajaAbierta) {
            return redirect()->route('caja.create')
                ->with('info', 'No tienes una caja abierta. Por favor, abre una caja primero.');
        }

        return view('caja.show', ['caja' => $cajaAbierta]);
    }

}
