<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Venta;
use App\Models\User;
use App\Models\DetalleVenta;
use App\Models\Producto;

use Illuminate\Http\Request;

class VentasController extends Controller
{

        public function index(Request $request)
        {
            if ($request->ajax()) {
                $ventas = Venta::with('usuario')->latest()->get();
                // Log::info($ventas);
                Log::info(json_encode($ventas));

                return response()->json(['data' => $ventas]); // <- 'data' es clave obligatoria
            }

            return view('ventas.index');
        }



    public function create()
    {
        $productos = Producto::all();
        $usuarios = User::all();

        // Variable por defecto
        $imprimirTicket = false;

        // Obtener configuración de la base de datos
        $config = DB::table('variables')
            ->select('impresion_ticket')
            ->first();

        // CORRECCIÓN: Usar == o === para comparación
        if ($config && $config->impresion_ticket === 'on') {
            $imprimirTicket = true;
        }

        return view('ventas.create', compact('productos', 'usuarios', 'imprimirTicket'));
    }


    public function store(Request $request)
    {

            // DEBUG: Log de lo que llega
                Log::info('Datos recibidos en venta:', [
                    'usuario_id' => $request->usuario_id,
                    'tipo_pago' => $request->tipo_pago,
                    'productos_json' => $request->productos_json,
                    'all_data' => $request->all()
                ]);

        try {
            $request->validate([
                'usuario_id' => 'required|exists:users,id',
                'tipo_pago' => 'required|string',
                'productos_json' => 'required|json',
            ]);

            $productos = json_decode($request->productos_json, true);
            if (empty($productos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe agregar al menos un producto.'
                ], 400);
            }

            foreach ($productos as $producto) {
                $productoModel = Producto::find($producto['id']);
                if (!$productoModel) {
                    return response()->json([
                        'success' => false,
                        'message' => "Producto ID {$producto['id']} no encontrado."
                    ], 404);
                }
                if ($productoModel->stock < $producto['cantidad']) {
                    return response()->json([
                        'success' => false,
                        'message' => "No hay suficiente stock para el producto {$productoModel->nombre}."
                    ], 400);
                }
            }

            DB::beginTransaction();

            $total = collect($productos)->sum(fn($p) => $p['precio_venta'] * $p['cantidad']);

            $venta = new Venta();
            $venta->usuario_id = $request->usuario_id;
            $venta->tipo_pago = $request->tipo_pago;
            $venta->folio = strtoupper(uniqid('F-'));
            $venta->total = $total;
            $venta->save();

            foreach ($productos as $producto) {
                $detalle = new DetalleVenta();
                $detalle->venta_id = $venta->id;
                $detalle->producto_id = $producto['id'];
                $detalle->cantidad = $producto['cantidad'];
                $detalle->precio_unitario = $producto['precio_venta'];
                $detalle->subtotal = $producto['cantidad'] * $producto['precio_venta'];
                $detalle->save();

                $productoModel = Producto::find($producto['id']);
                $productoModel->stock -= $producto['cantidad'];
                $productoModel->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente.',
                'venta_id' => $venta->id
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la venta: ' . $e->getMessage()
            ], 500);
        }
    }




    public function show($id)
    {
        $venta = Venta::with('detalle_venta.producto')->findOrFail($id);

        Log::info($venta);

        return response()->json($venta);
    }

}
