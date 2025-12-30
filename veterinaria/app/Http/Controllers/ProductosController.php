<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class ProductosController extends Controller
{

    private function getProductosActivosQuery()
    {
        // Subconsulta para obtener el último estado de cada producto
        $latestEstado = DB::table('productos_estado')
            ->select('producto_id', DB::raw('MAX(created_at) as max_created_at'))
            ->groupBy('producto_id');

        // Construye la consulta base para productos activos
        return Producto::with(['categoria', 'subcategoria'])
            ->join('productos_estado', function($join) use ($latestEstado) {
                $join->on('productos.id', '=', 'productos_estado.producto_id')
                    ->joinSub($latestEstado, 'latest_estado', function($join) {
                        $join->on('productos_estado.producto_id', '=', 'latest_estado.producto_id')
                            ->on('productos_estado.created_at', '=', 'latest_estado.max_created_at');
                    });
            })
            ->where('productos_estado.estado', 'activo')
            ->select('productos.*');
    }

    public function data(Request $request)
    {
        // Usa la función auxiliar para obtener la consulta base
        $query = $this->getProductosActivosQuery();

        // Aplica todos los filtros
        if ($request->filled('categoriaFiltro')) {
            $query->whereHas('categoria', function($q) use ($request) {
                $q->where('nombre', $request->categoriaFiltro);
            });
        }

        if ($request->filled('estadoFiltro')) {
            $query->where('productos.estado', $request->estadoFiltro);
        }

        if ($request->filled('precioFiltro')) {
            $rango = $request->precioFiltro;
            if ($rango == '0-50') $query->whereBetween('precio_venta', [0, 50]);
            elseif ($rango == '50-100') $query->whereBetween('precio_venta', [50, 100]);
            elseif ($rango == '100-500') $query->whereBetween('precio_venta', [100, 500]);
            elseif ($rango == '500+') $query->where('precio_venta', '>', 500);
        }

        if ($request->filled('caducidadFiltro')) {
            $hoy = Carbon::now();
            $fechaLimite = $hoy->copy()->addDays(30);

            if ($request->caducidadFiltro == 'proximas') {
                $query->whereDate('caducidad', '>=', $hoy)
                    ->whereDate('caducidad', '<=', $fechaLimite);
            } elseif ($request->caducidadFiltro == 'vencidas') {
                $query->whereDate('caducidad', '<', $hoy);
            } elseif ($request->caducidadFiltro == 'vigentes') {
                $query->whereDate('caducidad', '>', $fechaLimite);
            }
        }

        // Obtén la verificación de caducidad
        $response = $this->verificarCaducidad();
        $caducidad = $response->getData(true)['productos'] ?? [];

        // Obtén los productos filtrados
        $productos = $query->get();

        $data = $productos->map(function($producto) use ($caducidad) {
            $cad = collect($caducidad)->firstWhere('id', $producto->id);

            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
                'lote' => $producto->lote,
                'precio_venta' => $producto->precio_venta,
                'categoria' => $producto->categoria?->nombre,
                'subcategoria' => $producto->subcategoria?->nombre,
                'stock' => $producto->stock . '|' . ($producto->stock <= $producto->minimo_stock ? 1 : 0),
                'caducidad' => $cad
                    ? $cad['fecha_caducidad'] . '|' . ($cad['vencido'] ? 'vencido' : ($cad['urgente'] ? 'proximo' : 'vigente'))
                    : ($producto->caducidad
                        ? Carbon::parse($producto->caducidad)->format('d/m/Y') . '|vigente'
                        : 'N/A|vigente'),
                'updated_at' => $producto->updated_at,
            ];
        });

        return DataTables::collection($data)->make(true);
    }

    public function verificarCaducidad(): JsonResponse
    {
        try {
            $hoy = Carbon::now();
            $fechaLimite = $hoy->copy()->addDays(30);

            // Usa la función auxiliar y aplica filtros específicos de caducidad
            $productos = $this->getProductosActivosQuery()
                ->whereDate('caducidad', '<=', $fechaLimite)
                ->orderBy('caducidad', 'asc')
                ->get()
                ->map(function ($producto) use ($hoy) {
                    $caducidad = Carbon::parse($producto->caducidad);
                    $diasRestantes = (int) $hoy->diffInDays($caducidad, false);

                    return [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'codigo' => $producto->codigo,
                        'fecha_caducidad' => $caducidad->format('d/m/Y'),
                        'dias_restantes' => $diasRestantes,
                        'stock' => $producto->stock,
                        'urgente' => $diasRestantes >= 0 && $diasRestantes <= 30,
                        'vencido' => $diasRestantes < 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'count' => $productos->count(),
                'productos' => $productos,
                'actualizado' => $hoy->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            Log::error("Error en verificarCaducidad: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar productos caducados',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function verificarStock(): JsonResponse
    {
        try {
            // Usa la función auxiliar y aplica filtros específicos de stock
            $productos = $this->getProductosActivosQuery()
                ->whereColumn('stock', '<=', 'minimo_stock')
                ->orderBy('stock', 'asc')
                ->get()
                ->map(function ($producto) {
                    return [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'codigo' => $producto->codigo,
                        'stock' => $producto->stock,
                        'minimo_stock' => $producto->minimo_stock,
                        'alerta' => $producto->stock <= $producto->minimo_stock,
                    ];
                });

            return response()->json([
                'success' => true,
                'count' => $productos->count(),
                'productos' => $productos,
                'actualizado' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            Log::error("Error en verificarStock: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar stock mínimo',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    public function getNotificationsData(Request $request)
    {
        // 🔴 Caducidad
        $caducidadResponse = $this->verificarCaducidad();
        $caducidadData = $caducidadResponse->getData(true);
        $caducidadCount = (!empty($caducidadData['success']) && !empty($caducidadData['count']))
            ? $caducidadData['count']
            : 0;

        // 🟠 Stock
        $stockResponse = $this->verificarStock();
        $stockData = $stockResponse->getData(true);
        $stockCount = (!empty($stockData['success']) && !empty($stockData['count']))
            ? $stockData['count']
            : 0;

        // Total de alertas
        $totalAlerts = $caducidadCount + $stockCount;

        // Construir dropdown HTML con estilo
        $dropdownHtml = "";

        if ($totalAlerts > 0) {
            // Caducidad
            if ($caducidadCount > 0) {
                $dropdownHtml .= "<a href='#' class='dropdown-item'>
                                    <i class='fas fa-fw fa-clock text-danger mr-2'></i>
                                    Productos por caducar: <strong>{$caducidadCount}</strong>
                                </a>";
            }

            // Stock
            if ($stockCount > 0) {
                $dropdownHtml .= "<a href='#' class='dropdown-item'>
                                    <i class='fas fa-fw fa-boxes text-warning mr-2'></i>
                                    Productos con stock bajo: <strong>{$stockCount}</strong>
                                </a>";
            }

            $dropdownHtml .= "<div class='dropdown-divider'></div>";


        } else {
            // Todo en orden
            $dropdownHtml .= "<a href='#' class='dropdown-item text-success'>
                                <i class='fas fa-fw fa-check mr-2'></i>
                                Todo en orden
                            </a>";
        }

        return [
            'label' => $totalAlerts,
            'label_color' => $totalAlerts > 0 ? 'danger' : 'success',
            'icon_color' => 'dark',
            'dropdown' => $dropdownHtml,
        ];
    }


    public function alertaStock()
    {
        $config = DB::table('variables')
            ->where('alertas', 'on')
            ->first();

        if ($config) {
            return $this->verificarStock();
        }

        // Retorna un JSON con estructura consistente, incluso cuando está desactivado
        return response()->json([
            'success' => true,
            'count' => 0,
            'productos' => [],
            'message' => 'Alertas desactivadas'
        ]);
    }

    public function alertaCaducidad()
    {
        $config = DB::table('variables')
            ->where('alertas', 'on')
            ->first();

        if ($config) {
            return $this->verificarCaducidad();
        }

        // Retorna un JSON con estructura consistente, incluso cuando está desactivado
        return response()->json([
            'success' => true,
            'count' => 0,
            'productos' => [],
            'message' => 'Alertas desactivadas'
        ]);
    }


    public function index()
    {
        $productos = Producto::with(['categoria', 'subcategoria'])->get();
        $categorias = Categoria::all();
        $subcategorias = Subcategoria::all();

        return view('productos.index', compact('productos', 'categorias', 'subcategorias'));
    }

    public function databajas(Request $request)
    {
        // Subconsulta para obtener el último estado de cada producto
        $latestEstado = DB::table('productos_estado')
            ->select('producto_id', DB::raw('MAX(created_at) as max_created_at'))
            ->groupBy('producto_id');

        // Construye la consulta principal
        $query = Producto::with(['categoria', 'subcategoria'])
            ->join('productos_estado', function($join) use ($latestEstado) {
                $join->on('productos.id', '=', 'productos_estado.producto_id')
                    ->joinSub($latestEstado, 'latest_estado', function($join) {
                        $join->on('productos_estado.producto_id', '=', 'latest_estado.producto_id')
                            ->on('productos_estado.created_at', '=', 'latest_estado.max_created_at');
                    });
            })
            ->where('productos_estado.estado', 'baja')
            ->select('productos.*');

        // Aplica todos los filtros
        if ($request->filled('categoriaFiltro')) {
            $query->whereHas('categoria', function($q) use ($request) {
                $q->where('nombre', $request->categoriaFiltro);
            });
        }

        if ($request->filled('estadoFiltro')) {
            $query->where('productos.estado', $request->estadoFiltro);
        }

        if ($request->filled('precioFiltro')) {
            $rango = $request->precioFiltro;
            if ($rango == '0-50') $query->whereBetween('precio_venta', [0, 50]);
            elseif ($rango == '50-100') $query->whereBetween('precio_venta', [50, 100]);
            elseif ($rango == '100-500') $query->whereBetween('precio_venta', [100, 500]);
            elseif ($rango == '500+') $query->where('precio_venta', '>', 500);
        }

        if ($request->filled('caducidadFiltro')) {
            $hoy = Carbon::now();
            $fechaLimite = $hoy->copy()->addDays(30);

            if ($request->caducidadFiltro == 'proximas') {
                $query->whereDate('caducidad', '>=', $hoy)
                    ->whereDate('caducidad', '<=', $fechaLimite);
            } elseif ($request->caducidadFiltro == 'vencidas') {
                $query->whereDate('caducidad', '<', $hoy);
            } elseif ($request->caducidadFiltro == 'vigentes') {
                $query->whereDate('caducidad', '>', $fechaLimite);
            }
        }

        // Obtén la verificación de caducidad
        $response = $this->verificarCaducidad();
        $caducidad = $response->getData(true)['productos'] ?? [];

        // Obtén los productos filtrados
        $productos = $query->get();

        $data = $productos->map(function($producto) use ($caducidad) {
            $cad = collect($caducidad)->firstWhere('id', $producto->id);

            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
                'lote' => $producto->lote,
                'precio_venta' => $producto->precio_venta,
                'categoria' => $producto->categoria?->nombre,
                'subcategoria' => $producto->subcategoria?->nombre,
                'stock' => $producto->stock . '|' . ($producto->stock <= $producto->minimo_stock ? 1 : 0),
                'caducidad' => $cad
                    ? $cad['fecha_caducidad'] . '|' . ($cad['vencido'] ? 'vencido' : ($cad['urgente'] ? 'proximo' : 'vigente'))
                    : ($producto->caducidad
                        ? Carbon::parse($producto->caducidad)->format('d/m/Y') . '|vigente'
                        : 'N/A|vigente'),
                'updated_at' => $producto->updated_at,
            ];
        });

        return DataTables::collection($data)->make(true);
    }

    public function Bajasindex()
    {
        $productos = Producto::with(['categoria', 'subcategoria'])->get();
        $categorias = Categoria::all();
        $subcategorias = Subcategoria::all();

        return view('productos.bajas', compact('productos', 'categorias', 'subcategorias'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        $subcategorias = Subcategoria::all();

        return view('productos.create', compact('categorias', 'subcategorias'));
    }

    public function store(Request $request)
    {
        if ($request->filled('nueva_categoria')) {
            $categoria = Categoria::firstOrCreate(['nombre' => $request->nueva_categoria]);
            $request->merge(['categoria_id' => $categoria->id]);
        }

        if ($request->filled('nueva_subcategoria')) {
            $subcategoria = Subcategoria::firstOrCreate(['nombre' => $request->nueva_subcategoria]);
            $request->merge(['subcategoria_id' => $subcategoria->id]);
        }

        $request->validate([
            'nombre' => 'required|max:50',
            'stock' => 'nullable|integer|min:0',
            'minimo_stock' => 'nullable|integer|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'codigo' => 'nullable|max:50',
            'descripcion' => 'nullable|max:50',
            'categoria_id' => 'required|exists:categorias,id',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'lote' => 'nullable|string|max:50',
            'caducidad' => 'nullable|date',
        ]);

        // Usar transacción para asegurar que ambas inserciones se completen
        DB::beginTransaction();

        try {
            $producto = Producto::create($request->only([
                'nombre', 'stock', 'minimo_stock', 'precio_compra', 'precio_venta', 'codigo',
                'descripcion', 'categoria_id', 'subcategoria_id', 'lote', 'caducidad'
            ]));

            // Insertar en productos_estado con estado "baja"
            DB::table('productos_estado')->insert([
                'producto_id' => $producto->id,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Retornar JSON para AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto creado correctamente.',
                    'producto' => $producto
                ]);
            }

            return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Retornar error para AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el producto: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Producto $producto)
    {
            if ($request->filled('nueva_categoria')) {
                $categoria = Categoria::firstOrCreate(['nombre' => $request->nueva_categoria]);
                $request->merge(['categoria_id' => $categoria->id]);
            }

            if ($request->filled('nueva_subcategoria')) {
                $subcategoria = Subcategoria::firstOrCreate(['nombre' => $request->nueva_subcategoria]);
                $request->merge(['subcategoria_id' => $subcategoria->id]);
            }

            $request->validate([
                'nombre' => 'required|max:50',
                'stock' => 'nullable|integer|min:0',
                'minimo_stock' => 'nullable|integer|min:0',
                'precio_compra' => 'nullable|numeric|min:0',
                'precio_venta' => 'nullable|numeric|min:0',
                'codigo' => 'nullable|max:50',
                'descripcion' => 'nullable|max:50',
                'categoria_id' => 'required|exists:categorias,id',
                'subcategoria_id' => 'required|exists:subcategorias,id',
                'lote' => 'nullable|string|max:50',
                'caducidad' => 'nullable|date',
            ]);

            $producto->update($request->only([
                'nombre', 'stock', 'minimo_stock', 'precio_compra', 'precio_venta', 'codigo',
                'descripcion', 'categoria_id', 'subcategoria_id', 'lote', 'caducidad'
            ]));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'producto' => $producto
                ]);
            }

            return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function edit($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        return response()->json([
            'producto' => array_merge(
                $producto->toArray(),
                ['caducidad' => $producto->caducidad
                    ? \Carbon\Carbon::parse($producto->caducidad)->format('Y-m-d')
                    : null
                ]
            )
        ]);
    }

    public function destroy($id)
    {
        // No se usa, la eliminación se maneja con el método eliminar()
    }

    public function eliminar($id)
    {
        try {
            DB::statement('CALL marcar_producto_eliminado_sp(?)', [$id]);
            return true; // Éxito
        } catch (\Exception $e) {
            // Manejar el error (por ejemplo, loguearlo o devolver un mensaje)
            \Log::error('Error al ejecutar marcar_producto_eliminado_sp: ' . $e->getMessage());
            return false; // Fallo
        }
    }

    public function baja($id)
    {


        $estado = DB::table('productos_estado')
            ->where('producto_id', $id)
            ->latest()
            ->first();

        if ($estado && $estado->estado === 'baja') {
            return response()->json([
                'success' => false,
                'message' => 'El producto ya ha sido baja previamente.'
            ], 409); // 409 Conflict
        }

        DB::beginTransaction();

        try {
            // Registrar el nuevo estado
            DB::table('productos_estado')->insert([
                'producto_id' => $id,
                'estado' => 'baja',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // $producto = Producto::find($id);
            // if (Schema::hasColumn('productos', 'estado')) {
            //     $producto->update(['estado' => 'baja']);
            // }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto baja correctamente.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error al eliminar producto: ' . $e->getMessage(), [
                'producto_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto.'
            ], 500);
        }
    }

    public function show($id)
    {
            $producto = Producto::find($id);

            if (!$producto) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            return response()->json($producto);
    }

}
