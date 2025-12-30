<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Route;
use App\Models\RouteUnitSchedule;
use App\Models\RouteUnit;
use App\Models\Service;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EnvioController extends Controller
{
    // ============================================================
    // VISTA PRINCIPAL
    // ============================================================
    public function index()
    {
        $envios = Shipment::with(['site', 'service'])->get();
        $rutas = Route::all();
        $horarios = RouteUnitSchedule::all();
        $rutasUnidades = RouteUnit::all();
        $servicios = Service::all();

        return view('envios.index', compact('envios', 'rutas', 'horarios', 'rutasUnidades', 'servicios'));
    }

    // ============================================================
    // ACCIONES EN LOTE - CON NOTIFICACIONES
    // ============================================================
    public function bulkAction(Request $request)
    {
        try {
            $action = $request->input('action');
            $ids = $request->input('ids');

            Log::info('Bulk Action Request:', [
                'action' => $action,
                'ids' => $ids,
                'user' => auth()->id()
            ]);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se seleccionaron envíos'
                ], 400);
            }

            DB::beginTransaction();

            if ($action === 'delete') {
                // Eliminar envíos
                Shipment::whereIn('id', $ids)->delete();
                $message = 'Envíos eliminados correctamente';

                Log::info('Envíos eliminados:', ['ids' => $ids]);

                // NO registramos notificación para eliminaciones según tu requerimiento

            } else {
                // Validar que el estado sea válido
                $validStatuses = ['Pendiente', 'En camino', 'Entregado', 'Cancelado'];
                if (!in_array($action, $validStatuses)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Estado no válido'
                    ], 400);
                }

                // Cambiar estado de envíos
                $updated = Shipment::whereIn('id', $ids)->update(['status' => $action]);
                $message = "{$updated} envío(s) actualizado(s) a estado: {$action}";

                Log::info('Estados actualizados:', [
                    'ids' => $ids,
                    'nuevo_estado' => $action,
                    'actualizados' => $updated
                ]);

                // REGISTRAR NOTIFICACIÓN para actualización de estados
                app(NotificationController::class)->registrarnotificacion(
                    "Se actualizaron {$updated} envío(s) al estado: {$action}",
                    'envio'
                );
            }

            DB::commit();

            // Obtener estadísticas actualizadas
            $stats = [
                'total' => Shipment::count(),
                'pendientes' => Shipment::where('status', 'Pendiente')->count(),
                'transito' => Shipment::where('status', 'En camino')->count(),
                'entregados' => Shipment::where('status', 'Entregado')->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => $message,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error en bulkAction:', [
                'error' => $e->getMessage(),
                'action' => $action,
                'ids' => $ids
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la acción: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================================
    // EXPORTAR ENVÍOS
    // ============================================================
    public function export(Request $request)
    {
        $format = $request->input('format', 'excel');
        $ids = $request->input('ids');

        $query = Shipment::query();

        if ($ids) {
            $idArray = explode(',', $ids);
            $query->whereIn('id', $idArray);
        }

        $envios = $query->get();

        // REGISTRAR NOTIFICACIÓN para exportación
        app(NotificationController::class)->registrarnotificacion(
            "Se exportaron " . $envios->count() . " envío(s) en formato: " . strtoupper($format),
            'envio'
        );

        // Aquí implementarías la lógica de exportación según el formato
        // Por ahora solo redirige de vuelta con un mensaje
        return back()->with('success', 'Función de exportación en desarrollo');
    }

    // ============================================================
    // MÉTODOS EXISTENTES (MODIFICADOS CON NOTIFICACIONES)
    // ============================================================

    public function getShipmentData()
    {
        $envios = Shipment::with(['site'])->get()->map(function ($envio) {
            return [
                'id' => $envio->id,
                'sender_name' => e($envio->sender_name),
                'receiver_name' => e($envio->receiver_name),
                'total' => '$' . number_format($envio->amount, 2),
                'description' => e($envio->package_description),
                'photo' => $envio->package_image
                    ? '<img src="' . asset('storage/' . $envio->package_image) . '" width="80">'
                    : 'Sin foto',
                'route_unit_id' => $envio->route_unit_id,
                'service_id' => $envio->service_id,
                'driver_id' => $envio->driver_id,
                'status' => $envio->status,
                'folio' => $envio->folio,
            ];
        });

        return response()->json(['data' => $envios]);
    }

    public function edit($id)
    {
        try {
            $envio = Shipment::with(['site'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $envio->id,
                    'sender_name' => $envio->sender_name,
                    'receiver_name' => $envio->receiver_name,
                    'amount' => $envio->amount,
                    'package_description' => $envio->package_description,
                    'package_image' => $envio->package_image,
                    'route_unit_id' => $envio->route_unit_id,
                    'service_id' => $envio->service_id,
                    'driver_id' => $envio->driver_id,
                    'status' => $envio->status,
                    'folio' => $envio->folio,
                    'package_image_url' => $envio->package_image ? asset('storage/' . $envio->package_image) : null,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Error al obtener envío: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los datos del envío'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'sender_name' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'route_unit_id' => 'required|integer',
            'package_description' => 'nullable|string|max:255',
            'package_image' => 'nullable|image|max:2048',
            'status' => 'required|string|max:50',
            'schedule_id' => 'required|integer',
            'route_id' => 'required|integer',
        ]);

        $data = $request->all();

        // Campos obligatorios faltantes
        $data['folio'] = 'F-' . time() . rand(100, 999);
        $data['user_id'] = auth()->check() ? auth()->id() : 1;
        $data['site_id'] = 1;
        $data['service_id'] = $data['service_id'] ?? 1;
        $data['driver_id'] = 4;

        if ($request->hasFile('package_image')) {
            $data['package_image'] = $request->file('package_image')->store('envios', 'public');
        }

        // Crear el envío
        $envio = Shipment::create($data);

        // REGISTRAR NOTIFICACIÓN para creación
        app(NotificationController::class)->registrarnotificacion(
            "Nuevo envío creado - Folio: {$envio->folio} - Remitente: {$envio->sender_name}",
            'envio'
        );

        return redirect()->route('envios.index')->with('success', 'Envío creado exitosamente.');
    }

    public function update(Request $request, Shipment $envio)
    {
        $request->validate([
            'sender_name' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'route_unit_id' => 'required|integer',
            'package_description' => 'nullable|string|max:255',
            'package_image' => 'nullable|image|max:2048',
            'status' => 'required|string|max:50',
            'schedule_id' => 'required|integer',
            'route_id' => 'required|integer',
        ]);

        $data = $request->all();

        if ($request->hasFile('package_image')) {
            $data['package_image'] = $request->file('package_image')->store('envios', 'public');
        } else {
            unset($data['package_image']);
        }

        if (isset($data['total'])) {
            $data['amount'] = $data['total'];
            unset($data['total']);
        }

        // Guardar datos antiguos para el mensaje
        $estadoAnterior = $envio->status;
        $folio = $envio->folio;

        // Actualizar el envío
        $envio->update($data);

        // REGISTRAR NOTIFICACIÓN para actualización
        $mensaje = "Envío actualizado - Folio: {$folio}";

        // Si cambió el estado, agregar esa información
        if ($estadoAnterior != $envio->status) {
            $mensaje .= " - Estado: {$estadoAnterior} → {$envio->status}";
        }

        app(NotificationController::class)->registrarnotificacion($mensaje, 'envio');

        return redirect()->route('envios.index')->with('success', 'Envío actualizado');
    }

    public function destroy(Shipment $envio)
    {
        // NO registramos notificación para eliminaciones según tu requerimiento
        $envio->delete();
        return redirect()->route('envios.index')->with('success', 'Envío eliminado');
    }

    public function apiIndex()
    {
        $envios = Shipment::all()->map(function ($envio) {
            return [
                'id' => $envio->id,
                'sender_name' => $envio->sender_name,
                'receiver_name' => $envio->receiver_name,
                'amount' => $envio->amount,
                'package_description' => $envio->package_description,
                'package_image_url' => $envio->package_image ? asset('storage/' . $envio->package_image) : null,
                'route_unit_id' => $envio->route_unit_id,
                'status' => $envio->status,
            ];
        });

        return response()->json($envios);
    }
}
