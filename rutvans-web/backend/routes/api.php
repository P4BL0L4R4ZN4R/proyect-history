<?php

/*
| ==================================================================
| ARCHIVO DE RUTAS API UNIFICADO Y ORGANIZADO
|
| Migrado desde api3.php con estructura mejorada y comentarios
| organizados por plataforma, carpeta y módulos funcionales.
|
| ORGANIZACIÓN:
| - Imports organizados por carpetas de controllers
| - Endpoints organizados por plataforma y roles
| - Rutas de autenticación al final del archivo
| ==================================================================
*/

/*
| ==================================================================
| IMPORTS ORGANIZADOS POR CARPETAS DE CONTROLLERS
| ==================================================================
*/

// ============ NECESSARY IMPORTS ============
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

// ============ API CONTROLLERS ============
use App\Http\Controllers\API\
    {
        AdminController as AdminControllerApi,
        CompanyController as CompanyControllerApi,
        SiteController as SiteControllerApi,
        UnitController as UnitControllerApi,
        CommentController as CommentControllerApi,
        FaqController as FaqControllerApi,
        FormController as FormControllerApi,
        AuthController as AuthControllerApi,
    };

// ============ SUPER ADMIN CONTROLLERS ============
use App\Http\Controllers\API\SuperAdmin\
    {
        AdminController as AdminControllerSup,
        CompanyController as CompanyControllerSup,
        SiteController as SiteControllerSup,
        UnitController as UnitControllerSup,
        CommentController as CommentControllerSup,
        ComplaintController as ComplaintControllerSup,
        FaqController as FaqControllerSup,
        FormController as FormControllerSup,
        ProfileController as ProfileControllerSup,
    };

// ============ ADMIN CONTROLLERS ============
use App\Http\Controllers\API\Admin\
    {
        AuthController as AuthControllerAdm,
        DriverController as DriverControllerAdm,
        CoordinateController as CoordinateControllerAdm,
        FinanceController as FinanceControllerAdm,
        LocalityController as LocalityControllerAdm,
        ProfileController as ProfileControllerAdm,
        RouteController as RouteControllerAdm,
        UnitController as UnitControllerAdm,
        SaleController as SaleControllerAdm,
        CashierController as CashierControllerAdm,
        SiteController as SiteControllerAdm,
        RouteUnitScheduleController as RouteUnitScheduleControllerAdm,
    };

// ============ DRIVER CONTROLLERS ============
use App\Http\Controllers\API\Driver\
    {
        DriverController as DriverControllerDri,
        IncidentController as IncidentControllerDri,
        DriverStatController as DriverStatControllerDri,
        UserController as UserControllerDri,
        RouteUnitScheduleController as RouteUnitScheduleControllerDri,
        FreightController as FreightControllerDri,
        DeliveryController as DeliveryControllerDri,
    };

// ============ CLIENT CONTROLLERS ============
use App\Http\Controllers\API\Client\
    {
        ShipmentController as ShipmentControllerCli,
        UserController as UserControllerCli,
        TravelHistoryController as TravelHistoryControllerCli,
        RouteUnitScheduleController as RouteUnitScheduleControllerCli,
        SaleController as SaleControllerCli,
        RateController as RateControllerCli,
        ReservationController as ReservationControllerCli,
        PaymentController as PaymentControllerCli,
        UnitController as UnitControllerCli,
        FreightController as FreightControllerCli,
    };

// ============ CASHIER CONTROLLERS ============
use App\Http\Controllers\API\Cashier\
    {
        WebController as WebControllerCas,
    };

// ============ MAIN APP CONTROLLERS ============
use App\Http\Controllers\ShippingController;  // ¡IMPORTANTE! Agregado
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\StripeController;

// ============ MODELS ============
use App\Models\
    {
        User as ModelUser,
    };
use App\Models\Sale;

/*
| ==================================================================
| RUTAS
| ==================================================================
*/

// Notifications
Route::get('/notifications-data', [NotificationController::class, 'data'])->name('notifications.get');
Route::get('/notifications-data-navbar', [NotificationController::class, 'dataNavBar'])->name('notifications.navbar');
Route::get('/notifications-data-notread', [NotificationController::class, 'dataNotRead']);
Route::put('/notifications/{id}/read', [NotificationController::class, 'dataRead'])->name('notifications.markRead');
Route::get('/cashier/notifications-data', [NotificationController::class, 'data']);
Route::get('/cashier/notifications-data-notread', [NotificationController::class, 'dataNotRead']);
Route::put('/cashier/notifications/{id}/read', [NotificationController::class, 'dataRead']);

// Shipments (Envíos) [EnvioController]
Route::get('/envios-data', [EnvioController::class, 'getShipmentData']);

/*
| ==================================================================
| ENDPOINTS ORGANIZADOS POR PLATAFORMA
| ==================================================================
*/

// ============ API ENDPOINTS ============
Route::prefix('general')->group(function () {
    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('/stats', [AdminControllerApi::class, 'stats']);
        Route::get('/forms-monthly', [AdminControllerApi::class, 'formsMonthly']);
        Route::get('/sites-count', [AdminControllerApi::class, 'sitesCount']);
        Route::get('/units-count', [AdminControllerApi::class, 'unitsCount']);
        Route::get('/dashboard', [AdminControllerApi::class, 'dashboard']);
    });

    // Companies
    Route::get('companies/stats', [CompanyControllerApi::class, 'stats']);
    Route::apiResource('companies', CompanyControllerApi::class)->only(['index', 'show']);

    // Sites
    Route::apiResource('sites', SiteControllerApi::class)->only(['index', 'show']);

    // Units
    Route::apiResource('units', UnitControllerApi::class)->only(['index', 'show']);

    // Comments
    Route::apiResource('comments', CommentControllerApi::class)->except(['show']);

    // FAQs
    Route::apiResource('faqs', FaqControllerApi::class);

    // Forms
    Route::apiResource('forms', FormControllerApi::class)->only(['index', 'store']);
});

// ============ SUPER ADMIN ENDPOINTS ============
Route::prefix('super-admin')->group(function () {
    // Forms (público)
    Route::apiResource('forms', FormControllerSup::class)->only(['index', 'store']);

    // Comments (público)
    Route::post('/comments', [CommentControllerSup::class, 'store']);
});

Route::prefix('super-admin')->middleware('auth:sanctum')->group(function () {
    // Complaints
    Route::prefix('complaints')->group(function () {
        Route::post('/', [ComplaintControllerSup::class, 'store']);
        Route::get('/user/{userId}', [ComplaintControllerSup::class, 'userComplaints']);
    });

    // Forms
    Route::get('/forms/user', [FormControllerSup::class, 'userForms']);

    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('stats', [AdminControllerSup::class, 'stats']);
        Route::get('forms-monthly', [AdminControllerSup::class, 'formsMonthly']);
        Route::get('sites-count', [AdminControllerSup::class, 'sitesCount']);
        Route::get('units-count', [AdminControllerSup::class, 'unitsCount']);
        Route::get('dashboard', [AdminControllerSup::class, 'dashboard']);
    });

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileControllerSup::class, 'show']);
        Route::put('/', [ProfileControllerSup::class, 'update']);
        Route::put('/password', [ProfileControllerSup::class, 'updatePassword']);
        Route::post('/photo', [ProfileControllerSup::class, 'updatePhoto']);
    });

    // Complaints
    Route::get('/complaints/monthly', [ComplaintControllerSup::class, 'monthly']);
    Route::apiResource('complaints', ComplaintControllerSup::class)->except(['store']);

    // FAQs
    Route::apiResource('faqs', FaqControllerSup::class);

    // Comments
    Route::apiResource('comments', CommentControllerSup::class)->except(['store', 'show']);

    // Sites
    Route::apiResource('sites', SiteControllerSup::class)->only(['index', 'show']);

    // Units
    Route::apiResource('units', UnitControllerSup::class)->only(['index', 'show']);

    // Companies
    Route::get('/companies/stats', [CompanyControllerSup::class, 'stats']);
    Route::apiResource('companies', CompanyControllerSup::class)->only(['index', 'show']);
});

// ============ ADMIN ENDPOINTS ============
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthControllerAdm::class, 'logout']);

    // Profile
    Route::get('/profile', [ProfileControllerAdm::class, 'profile']);
    Route::post('/updateProfile', [ProfileControllerAdm::class, 'updateProfile']);

    // Drivers
    Route::apiResource('drivers', DriverControllerAdm::class);

    // Route-unit-schedules
    Route::apiResource('route-unit-schedules', RouteUnitScheduleControllerAdm::class);

    // Coordinates
    Route::apiResource('coordinates', CoordinateControllerAdm::class);

    // Cashiers
    Route::apiResource('cashiers', CashierControllerAdm::class);

    // Localities
    Route::apiResource('localities', LocalityControllerAdm::class);

    // Routes
    Route::apiResource('routes', RouteControllerAdm::class);

    // Units
    Route::apiResource('units', UnitControllerAdm::class);

    // Sales
    Route::apiResource('sales', SaleControllerAdm::class);

    // Sites
    Route::apiResource('sites', SiteControllerAdm::class);

    // Finance
    Route::prefix('finance')->group(function () {
        Route::get('/historical-balance', [FinanceControllerAdm::class, 'historicalBalance']);
        Route::get('/summary', [FinanceControllerAdm::class, 'summary']);
        Route::get('/top-routes', [FinanceControllerAdm::class, 'topRoutes']);
        Route::get('/sales-detail', [FinanceControllerAdm::class, 'salesDetail']);
        Route::get('/sales-period', [FinanceControllerAdm::class, 'salesPeriod']);
    });
});

Route::prefix('admin')->group(function () {
    // Login (público)
    Route::post('/login_admin', [AuthControllerAdm::class, 'login_admin']);
    Route::get('/validate-token', [AuthControllerAdm::class, 'validateToken']);
});

// ============ DRIVER ENDPOINTS ============
Route::prefix('driver')->group(function () {
    // Login
    Route::post('/login', [DriverControllerDri::class, 'login']);

    // Incidents
    Route::get('/incident/{id}', [IncidentControllerDri::class, 'show']);
    Route::delete('/incident/delete/{id}', [IncidentControllerDri::class, 'destroy']);

    // Performance
    Route::get('/performance/{driverId}', [DriverStatControllerDri::class, 'show']);

    // User
    Route::get('/user/edit/{id}', [UserControllerDri::class, 'edit']);
    Route::put('/user/update/{id}', [UserControllerDri::class, 'update']);

    // Route-unit-schedules
    Route::apiResource('route-unit-schedules', RouteUnitScheduleControllerDri::class)->only(['index', 'show']);

    // Freights
    Route::apiResource('freights', FreightControllerDri::class)->only(['index', 'show']);
    Route::get('/freights/edit/{id}', [FreightControllerDri::class, 'edit']);
    Route::put('/freights/update/{id}', [FreightControllerDri::class, 'update']);

    // Shipments
    Route::apiResource('shipments', DeliveryControllerDri::class)->only(['index', 'show']);
    Route::put('/shipments/update/{id}', [DeliveryControllerDri::class, 'update']);
});

// ============ CLIENT ENDPOINTS ============
Route::prefix('client')->group(function () {
    // Ping
    Route::get('/ping', function () {
        return response()->json([
            'status' => 'ok',
            'message' => 'API funcionando en Laravel'
        ]);
    });

    // Shipment
    Route::post('/shipment', [ShipmentControllerCli::class, 'store']);

    // Users
    Route::apiResource('users', UserControllerCli::class)->only(['index', 'store']);
    Route::prefix('user')->group(function () {
        Route::get('/', [UserControllerCli::class, 'getUser']);
        Route::patch('/', [UserControllerCli::class, 'updateUser']);
        Route::post('/upload-photo', [UserControllerCli::class, 'uploadPhoto']);
    });

    // Login
    Route::post('/login', [UserControllerCli::class, 'login']);

    // Travel history
    Route::get('/recent-trips', [TravelHistoryControllerCli::class, 'getRecentTrips']);
    Route::get('/travel-history', [TravelHistoryControllerCli::class, 'getAllTravelHistory']);
    Route::patch('/travel-history/{id}', [TravelHistoryControllerCli::class, 'updateTravelRating'])->middleware('auth:sanctum');

    // Password
    Route::post('/verify-password', [UserControllerCli::class, 'verifyPassword']);
    Route::post('/change-password', [UserControllerCli::class, 'changePassword']);
    Route::post('/update-password', [UserControllerCli::class, 'updatePassword']);

    // Route-unit-schedules
    Route::get('/available-destinations', [RouteUnitScheduleControllerCli::class, 'getAvailableDestinations']);
    Route::get('/route-unit-schedules', [RouteUnitScheduleControllerCli::class, 'getRouteUnitSchedules']);

    // Sales
    Route::get('/sales/recent', [SaleControllerCli::class, 'recentSales']);
    Route::post('/sales', [SaleControllerCli::class, 'store']);

    // Rates
    Route::apiResource('rates', RateControllerCli::class);

    // Reservations
    Route::apiResource('reservations', ReservationControllerCli::class)->only(['store', 'destroy']);

    // Payments
    Route::apiResource('payments', PaymentControllerCli::class)->only(['index', 'store']);

    // Units
    Route::prefix('units/{unit}')->group(function () {
        Route::get('/', [UnitControllerCli::class, 'show']);
        Route::get('/occupied-seats', [UnitControllerCli::class, 'getOccupiedSeats']);
        Route::post('/reserve', [ReservationControllerCli::class, 'store']);
        Route::delete('/reservations/{id}', [ReservationControllerCli::class, 'cancelReservation']);
    });

    // Freights
    Route::apiResource('freights', FreightControllerCli::class)->middleware('auth:sanctum');
});

// ============ CASHIER ENDPOINTS ============
Route::prefix('cashier')->group(function () {
    // Trips
    Route::prefix('trips')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [WebControllerCas::class, 'index']);
        Route::get('/rates-type', [WebControllerCas::class, 'getRatesType']);
        Route::get('/recent-trips', [WebControllerCas::class, 'getRecentTrips']);
        Route::get('/seats/{routeUnitId}', [WebControllerCas::class, 'seats']);
        Route::get('/trip-info/{routeUnitId}', [WebControllerCas::class, 'getTripInfo']);
        Route::post('/sales', [WebControllerCas::class, 'storeSale']);
        Route::post('/tickets', [WebControllerCas::class, 'getTicket']);
    });
});

// ============ EXTRA ENDPOINTS ============
// Legacy routes
Route::get('/user-cliente', [UserControllerCli::class, 'getUser']);
Route::get('/users-cliente', [UserControllerCli::class, 'index']);
Route::post('/users-cliente', [UserControllerCli::class, 'store']);
Route::post('/user-cliente/upload-photo', [UserControllerCli::class, 'uploadPhoto']);
Route::post('/login-cliente', [UserControllerCli::class, 'login']);
Route::patch('/user-cliente', [UserControllerCli::class, 'updateUser']);

/*
| ==================================================================
| AUTHENTICATION ENDPOINTS
| ==================================================================
*/

// Mobile Login
Route::post('/mobile-login', function (Request $request) {
    try {
        Log::info('Mobile Login Intent', ['email' => $request->email]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = ModelUser::where('email', $request->email)->first();

        Log::info('User Found', ['user_exists' => $user ? true : false]);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        $user->load('roles');

        if ($user->profile_photo_path) {
            $baseUrl = request()->getSchemeAndHttpHost();
            $user->profile_photo_url = $baseUrl . '/storage/' . $user->profile_photo_path;
        }

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    } catch (\Exception $e) {
        Log::error('Mobile Login Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json(['message' => 'Server Error', 'debug' => $e->getMessage()], 500);
    }
});

// Mobile Register
Route::post('/mobile-register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
    ]);

    $user = ModelUser::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    $user->assignRole('client');

    $token = $user->createToken('mobile')->plainTextToken;

    $user->load('roles');

    return response()->json([
        'token' => $token,
        'user' => $user,
    ], 201);
});

// Stripe
Route::post('/create-payment-intent', [StripeController::class, 'createPaymentIntent']);
Route::post('/confirm-payment', [StripeController::class, 'confirmPayment']);

// Test Stripe
Route::get('/test-stripe-fixed', function() {
    try {
        \Stripe\Stripe::setApiKey('sk_test_51SCrD919HZqLwOqTGoe8kZm0tCaX4Q9GNUAGfITpAZLpgIqJ1equsTyQNEvqMWNK5roU1aw6vzhdxxIePIt9D94800hSVxcIQC');

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => 2000,
            'currency' => 'mxn',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return response()->json([
            'success' => true,
            'client_secret' => $paymentIntent->client_secret
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

// Auth
Route::post('auth/login', [AuthControllerApi::class, 'login']);

// ============ PROTECTED ROUTES ============
Route::middleware('auth:sanctum')->group(function () {
    // User roles and permissions
    Route::get('/user/roles', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    });

    // Check permission
    Route::post('/user/can', function (Request $request) {
        $user = $request->user();
        $permission = $request->input('permission');

        return response()->json([
            'can' => $user->hasPermissionTo($permission)
        ]);
    });

    // Check role
    Route::post('/user/has-role', function (Request $request) {
        $user = $request->user();
        $role = $request->input('role');

        return response()->json([
            'has_role' => $user->hasRole($role)
        ]);
    });

    // Sales
    Route::prefix('sales')->group(function () {
        // Cashier sales
        Route::get('/cashier-sales', [WebControllerCas::class, 'getCashierSales']);

        // Cashier stats
        Route::get('/cashier-stats', function (Request $request) {
            $user = $request->user();

            if (!$user->hasRole('cashier')) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
            }

            $date = $request->input('date', now()->toDateString());

            $todaySales = Sale::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->where('status', 'completed')
                ->get();

            $yesterdaySales = Sale::where('user_id', $user->id)
                ->whereDate('created_at', now()->subDay()->toDateString())
                ->where('status', 'completed')
                ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'today' => [
                        'total_sales' => $todaySales->sum('amount'),
                        'tickets_sold' => $todaySales->count(),
                        'average_ticket' => $todaySales->count() > 0 ? $todaySales->sum('amount') / $todaySales->count() : 0
                    ],
                    'yesterday' => [
                        'total_sales' => $yesterdaySales->sum('amount'),
                        'tickets_sold' => $yesterdaySales->count(),
                        'average_ticket' => $yesterdaySales->count() > 0 ? $yesterdaySales->sum('amount') / $yesterdaySales->count() : 0
                    ]
                ]
            ]);
        });

        // All cashier sales
        Route::get('/cashier-all-sales', function (Request $request) {
            $user = $request->user();

            if (!$user->hasRole('cashier')) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
            }

            $perPage = $request->input('per_page', 15);
            $status = $request->input('status', '');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $query = Sale::with(['user', 'routeUnitSchedule.routeUnit.route'])
                ->where('user_id', $user->id);

            if ($status) {
                $query->where('status', $status);
            }

            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }

            $sales = $query->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->through(function ($sale) {
                    $data = $sale->data;
                    $ticket = $data['tickets'][0] ?? [];

                    return [
                        'id' => $sale->id,
                        'folio' => $sale->folio,
                        'created_at' => $sale->created_at,
                        'passenger_name' => $ticket['passenger_name'] ?? 'N/A',
                        'origin' => $ticket['origin'] ?? 'N/A',
                        'destination' => $ticket['destination'] ?? 'N/A',
                        'seat_number' => $ticket['seat_number'] ?? 'N/A',
                        'fare_type' => $ticket['fare_type'] ?? 'N/A',
                        'amount' => $sale->amount,
                        'base_price' => $ticket['base_price'] ?? 0,
                        'discount' => $ticket['discount'] ?? 0,
                        'payment_method' => 'cash',
                        'status' => $sale->status,
                        'travel_date' => $ticket['date'] ?? null,
                        'departure_time' => $ticket['departure_time'] ?? null
                    ];
                });

            return response()->json([
                'success' => true,
                'sales' => $sales,
                'summary' => [
                    'total_sales' => $sales->sum('amount'),
                    'total_tickets' => $sales->count(),
                    'filtered_total' => $query->sum('amount')
                ]
            ]);
        });

        // Generate cut report
        Route::post('/generate-cut-report', function (Request $request) {
            $user = $request->user();

            if (!$user->hasRole('cashier')) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
            }

            $date = $request->input('date', now()->toDateString());

            $sales = Sale::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->where('status', 'completed')
                ->get();

            $reportData = [
                'cajero' => $user->name,
                'fecha_corte' => $date,
                'hora_generacion' => now()->toDateTimeString(),
                'total_ventas' => $sales->sum('amount'),
                'total_boletos' => $sales->count(),
                'ventas' => $sales->map(function ($sale) {
                    $data = $sale->data;
                    $ticket = $data['tickets'][0] ?? [];
                    return [
                        'folio' => $sale->folio,
                        'pasajero' => $ticket['passenger_name'] ?? 'N/A',
                        'ruta' => ($ticket['origin'] ?? 'N/A') . ' - ' . ($ticket['destination'] ?? 'N/A'),
                        'asiento' => $ticket['seat_number'] ?? 'N/A',
                        'tarifa' => $ticket['fare_type'] ?? 'N/A',
                        'monto' => $sale->amount
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'message' => 'Reporte generado exitosamente',
                'report' => $reportData
            ]);
        });
    });

    // User profile
    Route::get('/user/profile', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_url' => $user->profile_photo_url,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ]
        ]);
    });
});

/*
| ==================================================================
| SHIPPINGS API ENDPOINTS
| ==================================================================
*/

// ============ ENVÍOS (SHIPPINGS) API ENDPOINTS ============
Route::prefix('shippings')->group(function () {
    Route::get('/', [ShippingController::class, 'apiIndex']);
    Route::get('/stats', [ShippingController::class, 'apiStats']);
    Route::get('/{id}', [ShippingController::class, 'apiShow']);
    Route::post('/', [ShippingController::class, 'apiStore']);
    Route::put('/{id}', [ShippingController::class, 'apiUpdate']);
    Route::delete('/{id}', [ShippingController::class, 'apiDestroy']);
});

// PARA PRUEBAS - rutas públicas de shippings
Route::prefix('shippings-public')->group(function () {
    Route::get('/test', function() {
        return response()->json([
            'message' => 'API de shippings funcionando',
            'timestamp' => now(),
            'total_shippings' => App\Models\Shipping::count()
        ]);
    });

    Route::get('/list', [ShippingController::class, 'apiIndex']);
});
