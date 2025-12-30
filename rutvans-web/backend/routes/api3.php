<?php

/*
| ==================================================================
| ESTE ARCHIVO FUE AGREGADO CON PROPÓSITOS DE REVISIÓN,
| EL API2.PHP APARENTA TENER EL PROBLEMA DE NO TENER
| CIERTAS RUTAS QUE ESTÁN CONECTADAS A LOS CONTROLLERS
| QUE ESTÁN DENTRO DE LA CARPETA ADMIN.
|
| ESTE ARCHIVO PRESENTA MUCHOS VARIOS CAMBIOS IMPORTANTES
| CON RESPECTO AL API2.PHP, ENTRE ESTOS SE ENCUENTRAN:
| CAMBIAR NOMBRES DE CONTROLLERS, MÉTODOS Y ENDPOINTS.
|
| ES MUY IMPORTANTE REVISAR A GRANDES RASGOS LOS CAMBIOS
| QUE CONLLEVA, PERO CREO QUE ES NECESARIO QUE TODO
| EL CÓDIGO TENGA REGLAS CLARAS CON RESPECTO A ORDEN.
|
| ESPERO Y ESTE MENSAJE SEA LEÍDO
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
use App\Http\Resources\UserResource;

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
        // ComplaintController as ComplaintControllerApi,
        // UserController as UserControllerApi,
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
        // UserController as UserControllerSup,
    };

// ============ ADMIN CONTROLLERS ============
use App\Http\Controllers\API\Admin\
    {
        AuthController as AuthControllerAdm, // AuthAPIController
        DriverController as DriverControllerAdm, // DriverApiController
        CoordinateController as CoordinateControllerAdm, // CoordinateAPIController
        FinanceController as FinanceControllerAdm, // FinanzasController
        LocalityController as LocalityControllerAdm, // LocalidadesApiController
        ProfileController as ProfileControllerAdm, // PerfilApiController
        RouteController as RouteControllerAdm,
        UnitController as UnitControllerAdm, // UnitApiController
        SaleController as SaleControllerAdm, // VentaApiController
        CashierController as CashierControllerAdm, // CashierAPIController
        SiteController as SiteControllerAdm, // SiteAPIController
        RouteUnitScheduleController as RouteUnitScheduleControllerAdm,
    };

// ============ DRIVER CONTROLLERS ============
use App\Http\Controllers\API\Driver\
    {
        DriverController as DriverControllerDri, // ApiChoferController
        IncidentController as IncidentControllerDri, // ApiIncidenciaController
        DriverStatController as DriverStatControllerDri, // ChoferEstadisticasDesempenoController
        UserController as UserControllerDri, // ApiDriverUserController
        RouteUnitScheduleController as RouteUnitScheduleControllerDri, // ApiRouteUnitScheduleApiController
        FreightController as FreightControllerDri, // ApiFleteController
        DeliveryController as DeliveryControllerDri, // ApiEntregasController
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

// ============ MODELS ============
use App\Models\
    {
        User as ModelUser,
    };
use Illuminate\Foundation\Auth\User;

/*
| ==================================================================
| ENDPOINTS ORGANIZADOS POR PLATAFORMA, POR CARPETA Y 
| POR MÓDULOS FUNCIONALES
| ==================================================================
*/


/*
--------------------------------------------------------------------
| WEB/MOBILE ENDPOINTS
--------------------------------------------------------------------
*/

// ============ API ENDPOINTS ============

// --- PREFIX (RUTAS GENERALES DE API) ---
Route::prefix('general')->group(function () {
    // Admin (Administrador)
    Route::prefix('admin')->group(function () {
        Route::get('/stats', [AdminControllerApi::class, 'stats']);
        Route::get('/forms-monthly', [AdminControllerApi::class, 'formsMonthly']);
        Route::get('/sites-count', [AdminControllerApi::class, 'sitesCount']);
        Route::get('/units-count', [AdminControllerApi::class, 'unitsCount']);
        Route::get('/dashboard', [AdminControllerApi::class, 'dashboard']);
    });
    
    // Companies (Empresas)
    Route::get('companies/stats', [CompanyControllerApi::class, 'stats']);
    Route::apiResource('companies', CompanyControllerApi::class)->only(['index', 'show']);
    
    // Sites (Sitios)
    Route::apiResource('sites', SiteControllerApi::class)->only(['index', 'show']);
    
    // Units (Unidades)
    Route::apiResource('units', UnitControllerApi::class)->only(['index', 'show']);
    
    // Comments (Comentarios)
    Route::apiResource('comments', CommentControllerApi::class)->except(['show']);
    
    // FAQs (Preguntas frecuentes)
    Route::apiResource('faqs', FaqControllerApi::class);
    
    // Forms (Formularios) [Cotización]
    Route::apiResource('forms', FormControllerApi::class)->only(['index', 'store']);
});

// ============ SUPER ADMIN ENDPOINTS ============

// --- PREFIX (RUTAS PARA SUPER-ADMIN) ---
Route::prefix('super-admin')->group(function () {
    // Forms (Formularios) [Acceso mixto público]
    Route::apiResource('forms', FormControllerSup::class)->only(['index', 'store']);
    
    // Comments (Comentarios) [Crear público]
    Route::post('/comments', [CommentControllerSup::class, 'store']);
});

// --- MIDDLEWARE (RUTAS PARA SUPER-ADMIN)  ---
Route::prefix('super-admin')->middleware('auth:sanctum')->group(function () {
    // Complaints (Quejas) [Quejas del usuario]
    Route::prefix('complaints')->group(function () {
        Route::post('/', [ComplaintControllerSup::class, 'store']);
        Route::get('/user/{userId}', [ComplaintControllerSup::class, 'userComplaints']);
    });

    // Forms (Formularios) [Formularios del usuario]
    Route::get('/forms/user', [FormControllerSup::class, 'userForms']);

    // Admin (Administrador) [Dashboard y estadísticas]
    Route::prefix('admin')->group(function () {
        Route::get('stats', [AdminControllerSup::class, 'stats']);
        Route::get('forms-monthly', [AdminControllerSup::class, 'formsMonthly']);
        Route::get('sites-count', [AdminControllerSup::class, 'sitesCount']);
        Route::get('units-count', [AdminControllerSup::class, 'unitsCount']);
        Route::get('dashboard', [AdminControllerSup::class, 'dashboard']);
    });

    // Profile (Perfil) [Perfil de administrador]
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileControllerSup::class, 'show']);
        Route::put('/', [ProfileControllerSup::class, 'update']);
        Route::put('/password', [ProfileControllerSup::class, 'updatePassword']);
        Route::post('/photo', [ProfileControllerSup::class, 'updatePhoto']);
    });

    // Complaints (Quejas) [Gestión de quejas]
    Route::get('/complaints/monthly', [ComplaintControllerSup::class, 'monthly']);
    Route::apiResource('complaints', ComplaintControllerSup::class)->except(['store']);

    // FAQs (Preguntas frecuentes) [Privadas para admin]
    Route::apiResource('faqs', FaqControllerSup::class);

    // Comments (Comentarios) [privado]
    Route::apiResource('comments', CommentControllerSup::class)->except(['store', 'show']);

    // Sites (Sitios)
    Route::apiResource('sites', SiteControllerSup::class)->only(['index', 'show']);

    // Units (Unidades)
    Route::apiResource('units', UnitControllerSup::class)->only(['index', 'show']);

    // Companies (Empresas)
    Route::get('/companies/stats', [CompanyControllerSup::class, 'stats']);
    Route::apiResource('companies', CompanyControllerSup::class)->only(['index', 'show']);
});

// ============ ADMIN ENDPOINTS ============

// --- MIDDLEWARE (RUTAS PARA ADMIN)  ---
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    // Logout (Auth)
    Route::post('/logout', [AuthControllerAdm::class, 'logout']);
    
    // Profile (Perfil)
    Route::get('/profile', [ProfileControllerAdm::class, 'profile']);
    Route::post('/updateProfile', [ProfileControllerAdm::class, 'updateProfile']);

    // Drivers (Conductores)
    Route::apiResource('drivers', DriverControllerAdm::class);

    // Route-unit-schedules (Rutas-unidades-horarios)
    Route::apiResource('route-unit-schedules', RouteUnitScheduleControllerAdm::class);

    // Coordinates (Coordinadores)
    Route::apiResource('coordinates', CoordinateControllerAdm::class);

    // Cashiers (Cajeros)
    Route::apiResource('cashiers', CashierControllerAdm::class);

    // Localities (Localidades)
    Route::apiResource('localities', LocalityControllerAdm::class);

    // Routes (Rutas)
    Route::apiResource('routes', RouteControllerAdm::class);

    // Units (Unidades)
    Route::apiResource('units', UnitControllerAdm::class);

    // Sales (Ventas)
    Route::apiResource('sales', SaleControllerAdm::class);

    // Sites (Sitios)
    Route::apiResource('sites', SiteControllerAdm::class);

    // Finance (Finanzas)
    Route::prefix('finance')->group(function () {
        Route::get('/historical-balance', [FinanceControllerAdm::class, 'historicalBalance']);
        Route::get('/summary', [FinanceControllerAdm::class, 'summary']);
        Route::get('/top-routes', [FinanceControllerAdm::class, 'topRoutes']);
        Route::get('/sales-detail', [FinanceControllerAdm::class, 'salesDetail']);
        Route::get('/sales-period', [FinanceControllerAdm::class, 'salesPeriod']);
    });
});

// --- PREFIX (RUTAS PARA ADMIN)  ---
Route::prefix('admin')->group(function () {
    // Login (Inicio de sesión) [Público]
    Route::post('/login_admin', [AuthControllerAdm::class, 'login_admin']);
    Route::get('/validate-token', [AuthControllerAdm::class, 'validateToken']);
});

// ============ DRIVER ENDPOINTS ============

// --- PREFIX (RUTAS PARA DRIVER)  ---
Route::prefix('driver')->group(function () {
    // Login (Inicio de sesión)
    Route::post('/login', [DriverControllerDri::class, 'login']);

    // Incidents (Incidentes)
    Route::get('/incident/{id}', [IncidentControllerDri::class, 'show']);
    Route::delete('/incident/delete/{id}', [IncidentControllerDri::class, 'destroy']);

    // Performance ("Stats", Estadísticas del conductor)
    Route::get('/performance/{driverId}', [DriverStatControllerDri::class, 'show']);

    // User (Usuario) [Conductores]
    Route::get('/user/edit/{id}', [UserControllerDri::class, 'edit']);
    Route::put('/user/update/{id}', [UserControllerDri::class, 'update']);

    // Route-unit-schedules (Rutas-unidades-horarios)
    Route::apiResource('route-unit-schedules', RouteUnitScheduleControllerDri::class)->only(['index', 'show']);

    // Freights (Fletes)
    Route::apiResource('freights', FreightControllerDri::class)->only(['index', 'show']);
    Route::get('/freights/edit/{id}', [FreightControllerDri::class, 'edit']);
    Route::put('/freights/update/{id}', [FreightControllerDri::class, 'update']);

    // Shipments (Envíos)
    Route::apiResource('shipments', DeliveryControllerDri::class)->only(['index', 'show']);
    Route::put('/shipments/update/{id}', [DeliveryControllerDri::class, 'update']);
});

// ============ CLIENT ENDPOINTS ============

// --- PREFIX (RUTAS PARA CLIENT)  ---
Route::prefix('client')->group(function () {
    // Ping (Prueba de funcionalidad)
    Route::get('/ping', function () {
        return response()->json([
            'status' => 'ok',
            'message' => 'API funcionando en Laravel'
        ]);
    });

    // Shipment (Envíos)
    Route::post('/shipment', [ShipmentControllerCli::class, 'index']);
    
    // Users (Usuarios)
    Route::apiResource('users', UserControllerCli::class)->only(['index', 'store']);
    Route::prefix('user')->group(function () {
        Route::get('/', [UserControllerCli::class, 'getUser']);
        Route::patch('/', [UserControllerCli::class, 'updateUser']);
        Route::post('/upload-photo', [UserControllerCli::class, 'uploadPhoto']);
    });
    Route::post('/login', [UserControllerCli::class, 'login']);
    
    // Travel history (Historial de viajes)
    Route::get('/recent-trips', [TravelHistoryControllerCli::class, 'getRecentTrips']);
    Route::patch('/travel-history/{id}', [TravelHistoryControllerCli::class, 'updateTravelRating'])->middleware('auth:sanctum');
    
    // Route-unit-schedules (Rutas-unidades-horarios)
    Route::get('/route-unit-schedules', [RouteUnitScheduleControllerCli::class, 'getRouteUnitSchedules']);
    
    // Sales (Ventas)
    Route::get('/sales/recent', [SaleControllerCli::class, 'recentSales']);
    Route::post('/sales', [SaleControllerCli::class, 'store']);
    
    // Rates (Tarifas)
    Route::apiResource('rates', RateControllerCli::class);
    
    // Reservations (Reservaciones)
    Route::apiResource('reservations', ReservationControllerCli::class)->only(['store', 'destroy']);
    
    // Payments (Pagos)
    Route::apiResource('payments', PaymentControllerCli::class)->only(['index', 'store']);
    
    // Units (Unidades)
    Route::prefix('units/{unit}')->group(function () {
        Route::get('/', [UnitControllerCli::class, 'show']);
        Route::get('/occupied-seats', [UnitControllerCli::class, 'getOccupiedSeats']);
        Route::get('/occupied-seats', [ReservationControllerCli::class, 'getOccupiedSeats']);
        Route::post('/reserve', [ReservationControllerCli::class, 'store']);
        Route::delete('/reservations/{id}', [ReservationControllerCli::class, 'cancelReservation']);
    });
    
    // Freights (Fletes)
    Route::apiResource('freights', FreightControllerCli::class)->middleware('auth:sanctum');
});

// ============ CASHIER ENDPOINTS ============

// --- PREFIX (RUTAS PARA CASHIER)  ---
Route::prefix('cashier')->group(function () {
    // Trips (Viajes)
    Route::prefix('trips')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [WebControllerCas::class, 'index']);
        Route::get('/rates-type', [WebControllerCas::class, 'getRatesType']);
        Route::get('/recent-trips', [WebControllerCas::class, 'getRecentTrips']);
        Route::get('/seats/{routeUnitId}', [WebControllerCas::class, 'seats']);
        Route::get('/trip-info/{routeUnitId}', [WebControllerCas::class, 'getTripInfo']);
        Route::post('/sales', [WebControllerCas::class, 'storeSale']);
        // Route::post('/tickets', [WebControllerCas::class, 'getTicket']);
    });
});

// ============ EXTRA ENDPOINTS ============

// --- LOGIN (RUTAS PARA LOGIN DE MÓVIL)  ---
Route::post('/mobile-login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    if (!$user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Debes verificar tu email antes de iniciar sesión.'], 403);
    }

    $token = $user->createToken('mobile')->plainTextToken;

    $user->load('roles');

    return response()->json([
        'token' => $token,
        'user' => new UserResource($user),
    ]);
});

// --- REGISTER (RUTAS PARA REGISTER DE MÓVIL)  ---
Route::post('/mobile-register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    $user->assignRole('client');

    $token = $user->createToken('mobile')->plainTextToken;

    $user->load('roles');

    return response()->json([
        'token' => $token,
        'user' => new UserResource($user),
    ], 201);
});

// --- LOGIN (RUTAS PARA LOGIN DE MÓVIL CON AUTH)  ---
Route::post('auth/mobile-login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    if (!$user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Debes verificar tu email antes de iniciar sesión.'], 403);
    }

    $token = $user->createToken('mobile')->plainTextToken;

    $user->load('roles');

    return response()->json([
        'token' => $token,
        'user' => new UserResource($user),
    ]);
});

// --- REGISTER (RUTAS PARA REGISTER DE MÓVIL CON AUTH)  ---
Route::post('auth/mobile-register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    $user->assignRole('client');

    $user->sendEmailVerificationNotification();

    return response()->json([
        'success' => true,
        'message' => 'Usuario registrado correctamente. Por favor, verifica tu correo electrónico antes de iniciar sesión.',
    ], 201);
});