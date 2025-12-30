<?php
namespace App\Http\Controllers\API\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WebController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today()->toDateString();
        
        $viajes = DB::table('route_unit_schedule as rus')
            ->join('route_unit as ru', 'rus.route_unit_id', '=', 'ru.id')
            ->join('routes as r', 'ru.route_id', '=', 'r.id')
            ->join('localities as lo', 'r.location_s_id', '=', 'lo.id')
            ->join('localities as ld', 'r.location_f_id', '=', 'ld.id')
            ->join('driver_unit as du', 'ru.driver_unit_id', '=', 'du.id')
            ->join('drivers as d', 'du.driver_id', '=', 'd.id')
            ->join('users as u', 'd.user_id', '=', 'u.id')
            ->join('units as un', 'du.unit_id', '=', 'un.id')
            ->join('sites as s', 'r.site_id', '=', 's.id')
            ->join('companies as c', 's.company_id', '=', 'c.id') // Join con companies
            ->where('rus.schedule_date', $hoy)
            ->where('rus.status', 'Activo')
            ->select([
                'rus.id as route_unit_schedule_id',
                'rus.schedule_date as fecha',
                'rus.schedule_time as hora_salida',
                'lo.locality as locality_start_name',
                'ld.locality as locality_end_name',
                'un.plate as unit_plate',
                'un.model as unit_model',
                'un.capacity as unit_capacity',
                'un.photo as unit_photo',
                'u.name as driver_name',
                'ru.price as price',
                's.name as site_name',
                'c.name as company_name', // Nombre de la compa├▒├¡a
                DB::raw("DATE_ADD(rus.schedule_time, INTERVAL 2 HOUR) as arrival_time")
            ])
            ->get();

        return response()->json($viajes);
    }

    public function seats($routeUnitScheduleId)
    {
        $routeUnitId = DB::table('route_unit_schedule')
            ->where('id', $routeUnitScheduleId)
            ->value('route_unit_id');

        $capacidad = DB::table('route_unit as ru')
            ->join('driver_unit as du', 'ru.driver_unit_id', '=', 'du.id')
            ->join('units as u', 'du.unit_id', '=', 'u.id')
            ->where('ru.id', $routeUnitId)
            ->value('u.capacity');

        $ocupados = DB::table('sales')
            ->where('route_unit_schedule_id', $routeUnitScheduleId)
            ->pluck('data')
            ->map(function($json) {
                $data = json_decode($json, true);
                if (empty($data) || !isset($data['tickets'][0]['seat_number'])) {
                    return null;
                }
                return $data['tickets'][0]['seat_number'];
            })
            ->filter()
            ->map(function($n) {
                return str_pad($n, 2, '0', STR_PAD_LEFT);
            })
            ->toArray();

        $plantillas = [
            12 => [
                ['02', '00', '00', '00', '12', '00', '00', '00'],
                ['01', '00', '05', '08', '11', '00', '00', '00'],
                ['00', '00', '04', '07', '10', '00', '00', '00'],
                ['00', '00', '03', '06', '09', '00', '00', '00'],
            ],
            15 => [
                ['02', '00', '00', '00', '00', '15', '00', '00'],
                ['01', '00', '05', '08', '11', '14', '00', '00'],
                ['00', '00', '04', '07', '10', '13', '00', '00'],
                ['00', '00', '03', '06', '09', '12', '00', '00'],
            ],
            16 => [
                ['02', '00', '06', '09', '12', '16', '00', '00'],
                ['01', '00', '05', '00', '00', '15', '00', '00'],
                ['00', '00', '04', '08', '11', '14', '00', '00'],
                ['00', '00', '03', '07', '10', '13', '00', '00'],
            ],
            18 => [
                ['02', '00', '00', '08', '11', '14', '18', '00'],
                ['01', '00', '05', '00', '00', '00', '17', '00'],
                ['00', '00', '04', '07', '10', '13', '16', '00'],
                ['00', '00', '03', '06', '09', '12', '15', '00'],
            ],
            19 => [
                ['02', '00', '06', '09', '12', '15', '19', '00'],
                ['01', '00', '05', '00', '00', '00', '18', '00'],
                ['00', '00', '04', '08', '11', '14', '17', '00'],
                ['00', '00', '03', '07', '10', '13', '16', '00'],
            ],
            21 => [
                ['02', '00', '00', '08', '11', '14', '17', '21'],
                ['01', '00', '05', '00', '00', '00', '00', '20'],
                ['00', '00', '04', '07', '10', '13', '16', '19'],
                ['00', '00', '03', '06', '09', '12', '15', '18'],
            ],
            22 => [
                ['02', '00', '06', '09', '12', '15', '18', '22'],
                ['01', '00', '05', '00', '00', '00', '00', '21'],
                ['00', '00', '04', '08', '11', '14', '17', '20'],
                ['00', '00', '03', '07', '10', '13', '16', '19'],
            ],
        ];

        $plantilla = $plantillas[$capacidad] ?? $plantillas[12];
        $asientos = [];
        $idCounter = 1;

        foreach ($plantilla as $fila) {
            foreach ($fila as $celda) {
                if ($celda === '00') {
                    $asientos[] = [
                        'id' => $idCounter++,
                        'seatNumber' => ' ',
                        'status' => 'empty',
                        'selected' => false
                    ];
                } else {
                    $asientos[] = [
                        'id' => $idCounter++,
                        'seatNumber' => $celda,
                        'status' => in_array($celda, $ocupados) ? 'occupied' : 'available',
                        'selected' => false
                    ];
                }
            }
        }

        return response()->json($asientos);
    }

    public function getRatesType()
    {
        return response()->json(DB::table('rates')->select(['id', 'name', 'percentage'])->get());
    }

    public function getTripInfo($routeUnitScheduleId)
    {
        $info = DB::table('route_unit_schedule as rus')
            ->join('route_unit as ru', 'rus.route_unit_id', '=', 'ru.id')
            ->join('routes as r', 'ru.route_id', '=', 'r.id')
            ->join('localities as lo', 'r.location_s_id', '=', 'lo.id')
            ->join('localities as ld', 'r.location_f_id', '=', 'ld.id')
            ->join('driver_unit as du', 'ru.driver_unit_id', '=', 'du.id')
            ->join('units as un', 'du.unit_id', '=', 'un.id')
            ->join('sites as s', 'r.site_id', '=', 's.id')
            ->join('companies as c', 's.company_id', '=', 'c.id') // Join con companies
            ->where('rus.id', $routeUnitScheduleId)
            ->select([
                'ru.price as precio_base',
                'lo.locality as origen',
                'ld.locality as destino',
                'rus.schedule_date as fecha',
                'rus.schedule_time as hora_salida',
                'un.photo as imagen',
                's.name as site_name',
                'c.name as company_name', // Nombre de la compa├▒├¡a
                DB::raw("DATE_ADD(rus.schedule_time, INTERVAL 2 HOUR) as arrival_time")
            ])
            ->first();

        return response()->json([
            'precio_base' => $info->precio_base ?? 100,
            'origen' => $info->origen,
            'destino' => $info->destino,
            'fecha' => $info->fecha,
            'hora_salida' => $info->hora_salida,
            'imagen' => $info->imagen ? asset('storage/' . $info->imagen) : null,
            'site_name' => $info->site_name,
            'company_name' => $info->company_name, // Nombre de la compa├▒├¡a
            'hora_llegada' => $info->arrival_time ? substr($info->arrival_time, 0, 5) : null
        ]);
    }

    public function storeSale(Request $request)
    {
        $validated = $request->validate([
            'passenger_name' => 'required|string|max:255',
            'fare_type' => 'required|string',
            'seat_number' => 'required|integer',
            'origin' => 'required|string',
            'destination' => 'required|string',
            'date' => 'required|date',
            'departure_time' => 'required',
            'base_price' => 'required|numeric',
            'discount' => 'required|numeric',
            'total' => 'required|numeric',
            'route_unit_schedule_id' => 'required|integer|exists:route_unit_schedule,id'
        ]);

        $ocupado = DB::table('sales')
            ->where('route_unit_schedule_id', $validated['route_unit_schedule_id'])
            ->where('data->tickets[0]->seat_number', $validated['seat_number'])
            ->exists();

        if ($ocupado) {
            return response()->json([
                'error' => 'El asiento ya est├í ocupado para este viaje',
                'seat_number' => $validated['seat_number']
            ], 409);
        }

        $folio = 'F' . strtoupper(Str::random(7));

        $userId = auth()->id() ?? 1;

        $saleId = DB::table('sales')->insertGetId([
            'folio' => $folio,
            'user_id' => $userId,
            'payment_id' => 1,
            'route_unit_schedule_id' => $validated['route_unit_schedule_id'],
            'rate_id' => 1,
            'data' => json_encode([
                'tickets' => [
                    [
                        'passenger_name' => $validated['passenger_name'],
                        'fare_type' => $validated['fare_type'],
                        'seat_number' => $validated['seat_number'],
                        'origin' => $validated['origin'],
                        'destination' => $validated['destination'],
                        'date' => $validated['date'],
                        'departure_time' => $validated['departure_time'],
                        'base_price' => $validated['base_price'],
                        'discount' => $validated['discount'],
                        'total' => $validated['total']
                    ]
                ],
                'total' => $validated['total'],
                'taxes' => 0,
                'grand_total' => $validated['total']
            ]),
            'amount' => $validated['total'],
            'status' => 'Completado',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('travel_history')->insert([
            'sale_id' => $saleId,
            'route_unit_schedule_id' => $validated['route_unit_schedule_id'],
            'status' => 'in_progress',
            'actual_departure' => null,
            'actual_arrival' => null,
            'passenger_rating' => null,
            'report' => '',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Venta registrada exitosamente',
            'folio' => $folio,
            'sale_id' => $saleId
        ], 201);
    }

    public function getRecentTrips()
{
    $userId = auth()->id();
    $trips = DB::table('travel_history as th')
        ->join('sales as s', 'th.sale_id', '=', 's.id')
        ->join('route_unit_schedule as rus', 'th.route_unit_schedule_id', '=', 'rus.id')
        ->join('route_unit as ru', 'rus.route_unit_id', '=', 'ru.id')
        ->join('routes as r', 'ru.route_id', '=', 'r.id')
        ->join('localities as lo', 'r.location_s_id', '=', 'lo.id')
        ->join('localities as ld', 'r.location_f_id', '=', 'ld.id')
        ->join('driver_unit as du', 'ru.driver_unit_id', '=', 'du.id')
        ->join('drivers as d', 'du.driver_id', '=', 'd.id')
        ->join('users as u', 'd.user_id', '=', 'u.id')
        ->join('sites as site', 'r.site_id', '=', 'site.id')  // <-- Cambio: 'sites as site'
        ->where('s.user_id', $userId)
        ->select([
            'th.id',
            'lo.locality as origin',
            'ld.locality as destination',
            'rus.schedule_date as date',
            'rus.schedule_time as time',
            'u.name as driver',
            's.amount',
            'site.name as site_name'  // <-- Cambio: 'site.name' en lugar de 's.name'
        ])
        ->orderByDesc('th.created_at')
        ->limit(5)
        ->get();

    return response()->json(['data' => $trips]);
}

// Agregar este método para cajero
public function getCashierSales(Request $request)
{
    $userId = auth()->id();
    
    // Verificar que el usuario sea cajero
    $user = auth()->user();
    if (!$user->hasRole('cashier')) {
        return response()->json([
            'success' => false,
            'message' => 'No tienes permisos para acceder a esta información'
        ], 403);
    }

    $date = $request->input('date', now()->toDateString());
    $status = $request->input('status', 'completed');

    // Obtener ventas del cajero usando la misma estructura que getRecentTrips
    $sales = DB::table('sales as s')
        ->leftJoin('travel_history as th', 's.id', '=', 'th.sale_id')
        ->leftJoin('route_unit_schedule as rus', 's.route_unit_schedule_id', '=', 'rus.id')
        ->leftJoin('route_unit as ru', 'rus.route_unit_id', '=', 'ru.id')
        ->leftJoin('routes as r', 'ru.route_id', '=', 'r.id')
        ->leftJoin('localities as lo', 'r.location_s_id', '=', 'lo.id')
        ->leftJoin('localities as ld', 'r.location_f_id', '=', 'ld.id')
        ->where('s.user_id', $userId)
        ->whereDate('s.created_at', $date)
        ->where('s.status', $status)
        ->select([
            's.id',
            's.folio',
            's.created_at',
            's.amount',
            's.status',
            's.data',
            'lo.locality as origin',
            'ld.locality as destination',
            'rus.schedule_date as travel_date',
            'rus.schedule_time as departure_time'
        ])
        ->orderByDesc('s.created_at')
        ->get()
        ->map(function ($sale) {
            $data = json_decode($sale->data, true);
            $ticket = $data['tickets'][0] ?? [];
            
            return [
                'id' => $sale->id,
                'folio' => $sale->folio,
                'created_at' => $sale->created_at,
                'passenger_name' => $ticket['passenger_name'] ?? 'N/A',
                'origin' => $sale->origin ?? ($ticket['origin'] ?? 'N/A'),
                'destination' => $sale->destination ?? ($ticket['destination'] ?? 'N/A'),
                'seat_number' => $ticket['seat_number'] ?? 'N/A',
                'fare_type' => $ticket['fare_type'] ?? 'N/A',
                'amount' => $sale->amount,
                'base_price' => $ticket['base_price'] ?? 0,
                'discount' => $ticket['discount'] ?? 0,
                'payment_method' => 'cash',
                'status' => $sale->status,
                'travel_date' => $sale->travel_date ?? ($ticket['date'] ?? null),
                'departure_time' => $sale->departure_time ?? ($ticket['departure_time'] ?? null)
            ];
        });

    return response()->json([
        'success' => true,
        'sales' => $sales,
        'summary' => [
            'total_sales' => $sales->sum('amount'),
            'total_tickets' => $sales->count(),
            'date' => $date
        ]
    ]);
}
}