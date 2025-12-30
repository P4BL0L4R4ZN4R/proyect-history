# 📚 DOCUMENTACIÓN TÉCNICA - API UNIFICADA v2

## 🏗️ ARQUITECTURA PROPUESTA

### Estructura de Directorios
```
app/Http/Controllers/API/V2/
├── Auth/
│   └── AuthController.php
├── Management/
│   ├── UserController.php
│   ├── VehicleController.php
│   ├── RouteController.php
│   ├── ScheduleController.php
│   └── SiteController.php
├── Operations/
│   ├── SaleController.php
│   ├── TripController.php
│   └── ShipmentController.php
├── Reports/
│   └── ReportController.php
├── Support/
│   ├── FaqController.php
│   ├── CommentController.php
│   └── ComplaintController.php
└── BaseApiController.php
```

---

## 🔐 SISTEMA DE AUTENTICACIÓN UNIFICADO

### AuthController
```php
<?php

namespace App\Http\Controllers\API\V2\Auth;

use App\Http\Controllers\API\V2\BaseApiController;

class AuthController extends BaseApiController
{
    /**
     * Login universal para todos los tipos de usuario
     * POST /api/v2/auth/login
     */
    public function login(Request $request)
    {
        // Validación
        // Autenticación
        // Retorno de token y usuario con roles
    }

    /**
     * Registro de nuevos usuarios
     * POST /api/v2/auth/register
     */
    public function register(Request $request)
    {
        // Validación
        // Creación de usuario
        // Asignación de rol
        // Envío de verificación
    }

    /**
     * Logout universal
     * POST /api/v2/auth/logout
     */
    public function logout(Request $request)
    {
        // Revocación de token
    }

    /**
     * Refresh token
     * POST /api/v2/auth/refresh
     */
    public function refresh(Request $request)
    {
        // Renovación de token
    }

    /**
     * Verificación de email
     * POST /api/v2/auth/verify-email
     */
    public function verifyEmail(Request $request)
    {
        // Verificación de email
    }
}
```

---

## 👥 GESTIÓN DE USUARIOS UNIFICADA

### UserController
```php
<?php

namespace App\Http\Controllers\API\V2\Management;

use App\Http\Controllers\API\V2\BaseApiController;

class UserController extends BaseApiController
{
    /**
     * Listar usuarios con filtros
     * GET /api/v2/users?role=driver&company_id=1&page=1
     */
    public function index(Request $request)
    {
        // Filtros por rol, empresa, estado, etc.
        // Paginación
        // Autorización basada en roles
    }

    /**
     * Crear nuevo usuario
     * POST /api/v2/users
     */
    public function store(Request $request)
    {
        // Validación según tipo de usuario
        // Creación con rol asignado
    }

    /**
     * Mostrar usuario específico
     * GET /api/v2/users/{id}
     */
    public function show($id)
    {
        // Autorización: solo propio perfil o admin
        // Información completa del usuario
    }

    /**
     * Actualizar usuario
     * PUT /api/v2/users/{id}
     */
    public function update(Request $request, $id)
    {
        // Validación de permisos
        // Actualización de datos
    }

    /**
     * Eliminar usuario
     * DELETE /api/v2/users/{id}
     */
    public function destroy($id)
    {
        // Solo super admin
        // Soft delete
    }

    /**
     * Perfil del usuario autenticado
     * GET /api/v2/users/profile
     */
    public function profile(Request $request)
    {
        // Perfil del usuario logueado
        // Información completa según rol
    }

    /**
     * Actualizar perfil propio
     * PUT /api/v2/users/profile
     */
    public function updateProfile(Request $request)
    {
        // Actualización de perfil propio
    }

    /**
     * Subir avatar
     * POST /api/v2/users/avatar
     */
    public function uploadAvatar(Request $request)
    {
        // Upload de imagen de perfil
        // Validación de archivo
    }

    /**
     * Estadísticas de desempeño (para drivers)
     * GET /api/v2/users/{id}/performance
     */
    public function performance($id)
    {
        // Solo para drivers
        // Estadísticas de desempeño
    }
}
```

---

## 🚗 GESTIÓN DE VEHÍCULOS

### VehicleController
```php
<?php

namespace App\Http\Controllers\API\V2\Management;

class VehicleController extends BaseApiController
{
    /**
     * Listar vehículos
     * GET /api/v2/vehicles?company_id=1&status=active
     */
    public function index(Request $request) { }

    /**
     * Crear vehículo
     * POST /api/v2/vehicles
     */
    public function store(Request $request) { }

    /**
     * Mostrar vehículo
     * GET /api/v2/vehicles/{id}
     */
    public function show($id) { }

    /**
     * Actualizar vehículo
     * PUT /api/v2/vehicles/{id}
     */
    public function update(Request $request, $id) { }

    /**
     * Eliminar vehículo
     * DELETE /api/v2/vehicles/{id}
     */
    public function destroy($id) { }
}
```

---

## 🎫 GESTIÓN DE VIAJES

### TripController
```php
<?php

namespace App\Http\Controllers\API\V2\Operations;

class TripController extends BaseApiController
{
    /**
     * Listar viajes disponibles
     * GET /api/v2/trips?origin=1&destination=2&date=2025-09-23
     */
    public function index(Request $request) { }

    /**
     * Información detallada del viaje
     * GET /api/v2/trips/{id}
     */
    public function show($id) { }

    /**
     * Asientos disponibles
     * GET /api/v2/trips/{id}/seats
     */
    public function seats($id) { }

    /**
     * Tipos de tarifas
     * GET /api/v2/trips/fare-types
     */
    public function fareTypes() { }

    /**
     * Generar ticket
     * POST /api/v2/trips/tickets
     */
    public function generateTicket(Request $request) { }
}
```

---

## 💰 GESTIÓN DE VENTAS

### SaleController
```php
<?php

namespace App\Http\Controllers\API\V2\Operations;

class SaleController extends BaseApiController
{
    /**
     * Listar ventas
     * GET /api/v2/sales?user_id=1&date_from=2025-09-01&date_to=2025-09-30
     */
    public function index(Request $request) { }

    /**
     * Crear venta
     * POST /api/v2/sales
     */
    public function store(Request $request) { }

    /**
     * Mostrar venta
     * GET /api/v2/sales/{id}
     */
    public function show($id) { }

    /**
     * Actualizar venta
     * PUT /api/v2/sales/{id}
     */
    public function update(Request $request, $id) { }

    /**
     * Cancelar venta
     * DELETE /api/v2/sales/{id}
     */
    public function destroy($id) { }
}
```

---

## 📊 SISTEMA DE REPORTES

### ReportController
```php
<?php

namespace App\Http\Controllers\API\V2\Reports;

class ReportController extends BaseApiController
{
    /**
     * Dashboard general
     * GET /api/v2/dashboard
     */
    public function dashboard(Request $request) { }

    /**
     * Estadísticas generales
     * GET /api/v2/dashboard/stats
     */
    public function stats(Request $request) { }

    /**
     * Resumen financiero
     * GET /api/v2/reports/financial-summary
     */
    public function financialSummary(Request $request) { }

    /**
     * Balance histórico
     * GET /api/v2/reports/balance-history
     */
    public function balanceHistory(Request $request) { }

    /**
     * Top rutas
     * GET /api/v2/reports/top-routes
     */
    public function topRoutes(Request $request) { }

    /**
     * Detalle de ventas
     * GET /api/v2/reports/sales-detail
     */
    public function salesDetail(Request $request) { }
}
```

---

## 🛡️ MIDDLEWARE Y SEGURIDAD

### Middleware Personalizado
```php
// ApiVersionMiddleware.php
class ApiVersionMiddleware
{
    public function handle($request, Closure $next, $version = 'v1')
    {
        $request->merge(['api_version' => $version]);
        return $next($request);
    }
}

// RolePermissionMiddleware.php
class RolePermissionMiddleware
{
    public function handle($request, Closure $next, $role = null)
    {
        if (!auth()->user()->hasRole($role)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $next($request);
    }
}
```

### Rutas Protegidas
```php
// routes/api_v2.php
Route::prefix('api/v2')->middleware(['api', 'api.version:v2'])->group(function () {
    
    // Rutas públicas
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::get('trips', [TripController::class, 'index']);
    Route::get('locations', [LocationController::class, 'index']);
    
    // Rutas autenticadas
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('users/profile', [UserController::class, 'profile']);
        Route::put('users/profile', [UserController::class, 'updateProfile']);
        
        // Rutas de admin
        Route::middleware('role:admin|super-admin')->group(function () {
            Route::apiResource('users', UserController::class);
            Route::apiResource('vehicles', VehicleController::class);
            Route::apiResource('routes', RouteController::class);
        });
        
        // Rutas de super admin
        Route::middleware('role:super-admin')->group(function () {
            Route::apiResource('companies', CompanyController::class);
            Route::get('dashboard/stats', [ReportController::class, 'stats']);
        });
    });
});
```

---

## 📝 FORMATO DE RESPUESTAS ESTÁNDAR

### Estructura Base
```json
{
    "success": true,
    "message": "Operación exitosa",
    "data": {
        // Datos solicitados
    },
    "meta": {
        "current_page": 1,
        "total_pages": 10,
        "total_items": 100,
        "per_page": 10
    },
    "errors": []
}
```

### Respuestas de Error
```json
{
    "success": false,
    "message": "Error en la validación",
    "data": null,
    "errors": [
        {
            "field": "email",
            "message": "El email es requerido"
        }
    ]
}
```

### BaseApiController Trait
```php
<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Traits\ApiResponseTrait;

class BaseApiController extends Controller
{
    use ApiResponseTrait;
    
    protected function successResponse($data = null, $message = 'Success', $meta = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => []
        ]);
    }
    
    protected function errorResponse($message, $errors = [], $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors
        ], $code);
    }
}
```

---

## 🔍 FILTROS Y PAGINACIÓN

### Query Builder Helper
```php
class ApiQueryBuilder
{
    public static function applyFilters($query, $request)
    {
        // Filtros dinámicos
        foreach ($request->all() as $key => $value) {
            if (in_array($key, ['page', 'per_page', 'sort_by', 'sort_order'])) {
                continue;
            }
            
            if (str_contains($key, '_from')) {
                $field = str_replace('_from', '', $key);
                $query->where($field, '>=', $value);
            } elseif (str_contains($key, '_to')) {
                $field = str_replace('_to', '', $key);
                $query->where($field, '<=', $value);
            } else {
                $query->where($key, $value);
            }
        }
        
        return $query;
    }
    
    public static function applySorting($query, $request)
    {
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        
        return $query->orderBy($sortBy, $sortOrder);
    }
    
    public static function applyPagination($query, $request)
    {
        $perPage = $request->get('per_page', 15);
        return $query->paginate($perPage);
    }
}
```

---

**Última actualización:** 23 de septiembre de 2025
**Versión:** 1.0