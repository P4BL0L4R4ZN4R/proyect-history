<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use App\Models\Site;
use App\Models\User;
use App\Models\RouteUnitSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Log::info('=== INICIO ShippingController@index ===');
        Log::info('Request parameters:', $request->all());
        Log::info('User ID:', ['user_id' => auth()->id() ?? 'guest']);

        // Obtener número de filas por página
        $perPage = $request->get('per_page', 15);
        Log::info('Per page setting:', ['per_page' => $perPage]);

        // Obtener envíos PAGINADOS con sus relaciones
        Log::info('Fetching shippings with relationships...');
        $shippings = Shipping::with(['user', 'site', 'routeUnitSchedule.routeUnit'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        Log::info('Shippings count:', ['total' => $shippings->total(), 'current_page' => $shippings->currentPage()]);
        Log::info('Shippings collection count:', ['count' => $shippings->count()]);

        // Log detallado de cada envío
        if ($shippings->count() > 0) {
            Log::info('=== DETALLE DE ENVÍOS ===');
            foreach ($shippings as $index => $shipping) {
                Log::info("Envío #{$index}:", [
                    'id' => $shipping->id,
                    'folio' => $shipping->folio,
                    'user_id' => $shipping->user_id,
                    'user_name' => $shipping->user ? $shipping->user->name : 'NULL',
                    'site_id' => $shipping->site_id,
                    'site_name' => $shipping->site ? $shipping->site->name : 'NULL',
                    'route_unit_schedule_id' => $shipping->route_unit_schedule_id,
                    'route_unit_id' => $shipping->routeUnitSchedule ?
                        ($shipping->routeUnitSchedule->routeUnit ? $shipping->routeUnitSchedule->routeUnit->id : 'NULL') : 'NULL',
                    'receiver_name' => $shipping->receiver_name,
                    'status' => $shipping->status,
                    'created_at' => $shipping->created_at
                ]);
            }
        } else {
            Log::warning('No se encontraron envíos en la base de datos');
        }

        // Obtener todos los sitios para el filtro
        Log::info('Fetching sites...');
        $sites = Site::all();
        Log::info('Sites count:', ['count' => $sites->count()]);

        if ($sites->count() > 0) {
            Log::info('Sites list:', $sites->pluck('name', 'id')->toArray());
        } else {
            Log::warning('No se encontraron sitios en la base de datos');
        }

        // Obtener IDs de route_unit disponibles (simplificado)
        Log::info('Fetching unit IDs from route_unit_schedule...');
        try {
            $unitIds = DB::table('route_unit_schedule')
                ->join('shippings', 'route_unit_schedule.id', '=', 'shippings.route_unit_schedule_id')
                ->whereNotNull('route_unit_schedule.route_unit_id')
                ->select('route_unit_schedule.route_unit_id')
                ->distinct()
                ->pluck('route_unit_id');

            Log::info('Unit IDs found:', ['unitIds' => $unitIds->toArray(), 'count' => $unitIds->count()]);

            // Verificar la consulta SQL
            $sql = DB::table('route_unit_schedule')
                ->join('shippings', 'route_unit_schedule.id', '=', 'shippings.route_unit_schedule_id')
                ->whereNotNull('route_unit_schedule.route_unit_id')
                ->select('route_unit_schedule.route_unit_id')
                ->distinct()
                ->toSql();

            Log::debug('SQL query for unitIds:', ['sql' => $sql]);

        } catch (\Exception $e) {
            Log::error('Error fetching unit IDs:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $unitIds = collect();
        }

        // Obtener estadísticas TOTALES desde la base de datos
        Log::info('Fetching statistics...');
        $stats = [
            'total' => Shipping::count(),
            'solicitados' => Shipping::where('status', 'Solicitado')->count(),
            'pagados' => Shipping::where('status', 'Pagado')->count(),
            'en_camino' => Shipping::where('status', 'En camino')->count(),
            'en_terminal' => Shipping::where('status', 'En terminal')->count(),
            'cancelados' => Shipping::where('status', 'Cancelado')->count(),
            'expirados' => Shipping::where('status', 'Expirado')->count(),
        ];

        Log::info('Statistics:', $stats);

        // Verificar relaciones de la base de datos
        Log::info('=== VERIFICACIÓN DE RELACIONES ===');

        // Verificar si hay envíos sin relación con route_unit_schedule
        $shippingsWithoutSchedule = Shipping::whereNull('route_unit_schedule_id')->count();
        Log::info('Shippings without route_unit_schedule:', ['count' => $shippingsWithoutSchedule]);

        // Verificar si hay envíos con schedule pero sin unit
        $shippingsWithSchedule = Shipping::whereNotNull('route_unit_schedule_id')->count();
        Log::info('Shippings with route_unit_schedule:', ['count' => $shippingsWithSchedule]);

        // Verificar datos de route_unit_schedule
        $scheduleCount = DB::table('route_unit_schedule')->count();
        Log::info('Total route_unit_schedule records:', ['count' => $scheduleCount]);

        $schedulesWithUnit = DB::table('route_unit_schedule')
            ->whereNotNull('route_unit_id')
            ->count();
        Log::info('route_unit_schedule with route_unit_id:', ['count' => $schedulesWithUnit]);

        Log::info('=== DATOS QUE SE ENVIAN A LA VISTA ===');
        Log::info('Variables a pasar a la vista:', [
            'shippings_count' => $shippings->count(),
            'sites_count' => $sites->count(),
            'unitIds_count' => $unitIds->count(),
            'stats' => $stats
        ]);

        Log::info('=== FIN ShippingController@index ===');

        return view('shippings.index', compact('shippings', 'sites', 'stats', 'unitIds'));
    }


    /**
     * Show the form for creating a new resource.
     */
    // Método para devolver datos del formulario en JSON
    public function create()
    {
        try {
            $users = User::all();
            $sites = Site::all();
            $schedules = RouteUnitSchedule::with('routeUnit')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $users,
                    'sites' => $sites,
                    'schedules' => $schedules
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos del formulario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        try {
            // Validación
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'site_id' => 'required|exists:sites,id',
                'route_unit_schedule_id' => 'required|exists:route_unit_schedule,id',
                'receiver_name' => 'required|string|max:255',
                'receiver_description' => 'required|string',
                'length_cm' => 'required|numeric|min:0',
                'width_cm' => 'required|numeric|min:0',
                'height_cm' => 'required|numeric|min:0',
                'weight_kg' => 'required|numeric|min:0',
                'amount' => 'required|numeric|min:0',
                'package_description' => 'required|string',
                'status' => 'required|string|in:Solicitado,Recolectado,En tránsito,Entregado,Cancelado',
                'fragile' => 'sometimes|nullable',
                'package_image' => 'nullable|image|max:5120|mimes:jpg,jpeg,png,gif'
            ]);

            // Procesar checkbox frágil
            $validated['fragile'] = $request->has('fragile') && $request->input('fragile') ? 1 : 0;

            // Generar folio único
            $validated['folio'] = 'ENV-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

            // Manejar imagen
            if ($request->hasFile('package_image')) {
                $path = $request->file('package_image')->store('package_images', 'public');
                $validated['package_image'] = $path;
            }

            // Crear el envío
            $shipping = Shipping::create($validated);

            // REGISTRAR NOTIFICACIÓN para creación
            $mensaje = "Nuevo envío creado - Folio: {$shipping->folio} - Destinatario: {$shipping->receiver_name}";

            app(NotificationController::class)->registrarnotificacion($mensaje, 'envio');

             return redirect()->route('shippings.index');
        }
        catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->route('shippings.index')
            ->withErrors($e->errors())
            ->withInput();
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $shipping = Shipping::findOrFail($id);

            // Guardar estado anterior y folio para la notificación
            $estadoAnterior = $shipping->status;
            $folio = $shipping->folio;

            // Validación (descomenta esta sección)


            // Procesar checkbox frágil (CORREGIDO - este debe estar DESPUÉS de la validación)
            $validated['fragile'] = $request->has('fragile') && $request->input('fragile') ? 1 : 0;

            // Manejar imagen
            if ($request->hasFile('package_image')) {
                // Eliminar imagen anterior si existe
                if ($shipping->package_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($shipping->package_image)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($shipping->package_image);
                }

                $path = $request->file('package_image')->store('package_images', 'public');
                $validated['package_image'] = $path;
            } else {
                // Mantener imagen actual
                unset($validated['package_image']);
            }

            // Actualizar
            $shipping->update($validated);

            // REGISTRAR NOTIFICACIÓN para actualización
            $mensaje = "Envío actualizado - Folio: {$folio}";

            // Si cambió el estado, agregar esa información
            if ($estadoAnterior != $shipping->status) {
                $mensaje .= " - Estado: {$estadoAnterior} → {$shipping->status}";
            }

            app(NotificationController::class)->registrarnotificacion($mensaje, 'envio');

            return response()->json([
                'success' => true,
                'message' => 'Envío actualizado correctamente',
                'data' => $shipping->fresh()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Envío no encontrado'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $shipping = Shipping::with('routeUnitSchedule.routeUnit')->findOrFail($id);
        $users = User::all();
        $sites = Site::all();
        $schedules = RouteUnitSchedule::with('routeUnit')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'shipping' => $shipping,
                'users' => $users,
                'sites' => $sites,
                'schedules' => $schedules
            ]
        ]);
    }

    public function destroy($id)
    {
        $shipping = Shipping::findOrFail($id);

        // Eliminar imagen si existe
        if ($shipping->package_image) {
            Storage::disk('public')->delete($shipping->package_image);
        }

        $shipping->delete();

        return response()->json([
            'success' => true,
            'message' => 'Envío eliminado exitosamente.'
        ]);
    }

    /**
     * Acciones en lote
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'exists:shippings,id'
        ]);

        $action = $request->action;
        $ids = $request->ids;

        if ($action === 'delete') {
            // Eliminar múltiples envíos
            $shippings = Shipping::whereIn('id', $ids)->get();

            foreach ($shippings as $shipping) {
                if ($shipping->package_image) {
                    Storage::disk('public')->delete($shipping->package_image);
                }
                $shipping->delete();
            }

            $message = count($ids) . ' envío(s) eliminado(s) exitosamente.';

            // NO registramos notificación para eliminaciones
        } else {
            // Actualizar estado
            $updatedCount = Shipping::whereIn('id', $ids)->update(['status' => $action]);
            $message = $updatedCount . ' envío(s) actualizado(s) a "' . $action . '".';

            // REGISTRAR NOTIFICACIÓN para actualización masiva
            app(NotificationController::class)->registrarnotificacion(
                "Se actualizaron {$updatedCount} envío(s) al estado: {$action}",
                'envio'
            );
        }

        // Obtener estadísticas actualizadas
        $stats = [
            'total' => Shipping::count(),
            'solicitados' => Shipping::where('status', 'Solicitado')->count(),
            'pagados' => Shipping::where('status', 'Pagado')->count(),
            'en_camino' => Shipping::where('status', 'En camino')->count(),
            'en_terminal' => Shipping::where('status', 'En terminal')->count(),
            'cancelados' => Shipping::where('status', 'Cancelado')->count(),
            'expirados' => Shipping::where('status', 'Expirado')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => $message,
            'stats' => $stats
        ]);
    }

    /**
     * Obtener estadísticas (para AJAX)
     */
    public function stats()
    {
        $stats = [
            'total' => Shipping::count(),
            'solicitados' => Shipping::where('status', 'Solicitado')->count(),
            'pagados' => Shipping::where('status', 'Pagado')->count(),
            'en_camino' => Shipping::where('status', 'En camino')->count(),
            'en_terminal' => Shipping::where('status', 'En terminal')->count(),
            'cancelados' => Shipping::where('status', 'Cancelado')->count(),
            'expirados' => Shipping::where('status', 'Expirado')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }


        /**
     * API: Obtener todos los envíos
     */
    public function apiIndex()
    {
        $envios = Shipping::all()->map(function ($envio) {
            return [
                'id' => $envio->id,
                'folio' => $envio->folio,
                'sender_name' => $envio->user->name ?? 'N/A', // Nombre del usuario
                'receiver_name' => $envio->receiver_name,
                'receiver_description' => $envio->receiver_description,
                'package_description' => $envio->package_description,
                'amount' => $envio->amount,
                'length_cm' => $envio->length_cm,
                'width_cm' => $envio->width_cm,
                'height_cm' => $envio->height_cm,
                'weight_kg' => $envio->weight_kg,
                'fragile' => (bool) $envio->fragile,
                'package_image_url' => $envio->package_image ? asset('storage/' . $envio->package_image) : null,
                'route_unit_schedule_id' => $envio->route_unit_schedule_id,
                'status' => $envio->status,
                'site_id' => $envio->site_id,
                'site_name' => $envio->site->name ?? 'N/A',
                'created_at' => $envio->created_at->toDateTimeString(),
                'updated_at' => $envio->updated_at->toDateTimeString(),
            ];
        });

        return response()->json($envios);
    }

    /**
     * API: Obtener un envío específico
     */
    public function apiShow($id)
    {
        $envio = Shipping::with(['user', 'site', 'routeUnitSchedule.routeUnit'])->find($id);

        if (!$envio) {
            return response()->json([
                'success' => false,
                'message' => 'Envío no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $envio->id,
                'folio' => $envio->folio,
                'user' => [
                    'id' => $envio->user->id ?? null,
                    'name' => $envio->user->name ?? 'N/A',
                    'email' => $envio->user->email ?? null,
                ],
                'receiver_name' => $envio->receiver_name,
                'receiver_description' => $envio->receiver_description,
                'package_description' => $envio->package_description,
                'amount' => $envio->amount,
                'dimensions' => [
                    'length_cm' => $envio->length_cm,
                    'width_cm' => $envio->width_cm,
                    'height_cm' => $envio->height_cm,
                    'weight_kg' => $envio->weight_kg,
                ],
                'fragile' => (bool) $envio->fragile,
                'package_image_url' => $envio->package_image ? asset('storage/' . $envio->package_image) : null,
                'route_unit_schedule' => $envio->routeUnitSchedule ? [
                    'id' => $envio->routeUnitSchedule->id,
                    'route_unit_id' => $envio->routeUnitSchedule->route_unit_id,
                    'schedule_date' => $envio->routeUnitSchedule->schedule_date,
                    'schedule_time' => $envio->routeUnitSchedule->schedule_time,
                    'route_unit' => $envio->routeUnitSchedule->routeUnit,
                ] : null,
                'status' => $envio->status,
                'site' => $envio->site ? [
                    'id' => $envio->site->id,
                    'name' => $envio->site->name,
                    'address' => $envio->site->address,
                ] : null,
                'created_at' => $envio->created_at->toDateTimeString(),
                'updated_at' => $envio->updated_at->toDateTimeString(),
            ]
        ]);
    }

    /**
     * API: Crear un nuevo envío
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'site_id' => 'required|exists:sites,id',
            'route_unit_schedule_id' => 'nullable|exists:route_unit_schedule,id',
            'receiver_name' => 'required|string|max:255',
            'receiver_description' => 'required|string',
            'package_description' => 'required|string',
            'length_cm' => 'required|numeric|min:0',
            'width_cm' => 'required|numeric|min:0',
            'height_cm' => 'required|numeric|min:0',
            'weight_kg' => 'required|numeric|min:0',
            'fragile' => 'boolean',
            'status' => 'required|in:Solicitado,Pagado,En camino,En terminal,Cancelado,Expirado',
            'amount' => 'required|numeric|min:0',
        ]);

        // Generar folio único
        $validated['folio'] = 'ENV-' . strtoupper(Str::random(8));

        // Convertir fragile a booleano
        $validated['fragile'] = $request->boolean('fragile');

        $shipping = Shipping::create($validated);

        // REGISTRAR NOTIFICACIÓN para creación
        app(NotificationController::class)->registrarnotificacion(
            "Nuevo envío creado - Folio: {$shipping->folio} - Destinatario: {$shipping->receiver_name} - Estado: {$shipping->status}",
            'envio'
        );

        return response()->json([
            'success' => true,
            'message' => 'Envío creado exitosamente',
            'data' => $shipping
        ], 201);
    }

    /**
     * API: Actualizar un envío
     */
    public function apiUpdate(Request $request, $id)
    {
        $shipping = Shipping::find($id);

        if (!$shipping) {
            return response()->json([
                'success' => false,
                'message' => 'Envío no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'site_id' => 'sometimes|exists:sites,id',
            'route_unit_schedule_id' => 'nullable|exists:route_unit_schedule,id',
            'receiver_name' => 'sometimes|string|max:255',
            'receiver_description' => 'sometimes|string',
            'package_description' => 'sometimes|string',
            'length_cm' => 'sometimes|numeric|min:0',
            'width_cm' => 'sometimes|numeric|min:0',
            'height_cm' => 'sometimes|numeric|min:0',
            'weight_kg' => 'sometimes|numeric|min:0',
            'fragile' => 'sometimes|boolean',
            'status' => 'sometimes|in:Solicitado,Pagado,En camino,En terminal,Cancelado,Expirado',
            'amount' => 'sometimes|numeric|min:0',
        ]);

        // Guardar estado anterior para notificación
        $estadoAnterior = $shipping->status;
        $folio = $shipping->folio;

        // Convertir fragile a booleano si está presente
        if ($request->has('fragile')) {
            $validated['fragile'] = $request->boolean('fragile');
        }

        $shipping->update($validated);

        // REGISTRAR NOTIFICACIÓN para actualización
        $mensaje = "Envío actualizado - Folio: {$folio}";

        // Si cambió el estado, agregar esa información
        if ($estadoAnterior != $shipping->status) {
            $mensaje .= " - Estado: {$estadoAnterior} → {$shipping->status}";
        }

        app(NotificationController::class)->registrarnotificacion($mensaje, 'envio');

        return response()->json([
            'success' => true,
            'message' => 'Envío actualizado exitosamente',
            'data' => $shipping
        ]);
    }

    /**
     * API: Eliminar un envío
     */
    public function apiDestroy($id)
    {
        $shipping = Shipping::find($id);

        if (!$shipping) {
            return response()->json([
                'success' => false,
                'message' => 'Envío no encontrado'
            ], 404);
        }

        // Eliminar imagen si existe
        if ($shipping->package_image) {
            Storage::disk('public')->delete($shipping->package_image);
        }

        $shipping->delete();

        return response()->json([
            'success' => true,
            'message' => 'Envío eliminado exitosamente'
        ]);
    }

    /**
     * API: Obtener estadísticas
     */
    public function apiStats()
    {
        $stats = [
            'total' => Shipping::count(),
            'solicitados' => Shipping::where('status', 'Solicitado')->count(),
            'pagados' => Shipping::where('status', 'Pagado')->count(),
            'en_camino' => Shipping::where('status', 'En camino')->count(),
            'en_terminal' => Shipping::where('status', 'En terminal')->count(),
            'cancelados' => Shipping::where('status', 'Cancelado')->count(),
            'expirados' => Shipping::where('status', 'Expirado')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

}
