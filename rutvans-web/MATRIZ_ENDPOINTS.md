# 📊 MATRIZ DE ENDPOINTS - ESTADO ACTUAL VS PROPUESTO

## 🔍 ENDPOINTS ACTUALES AGRUPADOS POR FUNCIONALIDAD

### 🔐 AUTENTICACIÓN

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/auth/mobile-login` | POST | Closure | ✅ Mantener base | `/api/v2/auth/login` |
| `/auth/mobile-register` | POST | Closure | ✅ Mantener base | `/api/v2/auth/register` |
| `/login_admin` | POST | AuthAPIController | ❌ Deprecar | `/api/v2/auth/login` |
| `/driver/login` | POST | ApiChoferController | ❌ Deprecar | `/api/v2/auth/login` |
| `/clientes/login` | POST | ClienteUserController | ❌ Deprecar | `/api/v2/auth/login` |
| `/login-cliente` | POST | ClienteUserController | ❌ Deprecar Legacy | `/api/v2/auth/login` |
| `/admin/logout` | POST | AuthAPIController | ✅ Unificar | `/api/v2/auth/logout` |

### 👤 GESTIÓN DE USUARIOS

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/user` | GET | Closure | ✅ Base | `/api/v2/users/profile` |
| `/admin/perfil` | GET | PerfilApiController | ❌ Deprecar | `/api/v2/users/profile` |
| `/super-admin/profile` | GET | ProfileController | ❌ Deprecar | `/api/v2/users/profile` |
| `/clientes/user` | GET | ClienteUserController | ❌ Deprecar | `/api/v2/users/profile` |
| `/user-cliente` | GET | ClienteUserController | ❌ Legacy | `/api/v2/users/profile` |
| `/admin/actualizarPerfil` | POST | PerfilApiController | ❌ Deprecar | `/api/v2/users/profile` |
| `/clientes/user/upload-photo` | POST | ClienteUserController | ✅ Unificar | `/api/v2/users/avatar` |
| `/admin/drivers` | GET,POST,PUT,DELETE | DriverApiController | ✅ Unificar | `/api/v2/users?role=driver` |
| `/admin/cashiers` | GET,POST,PUT,DELETE | CashierAPIController | ✅ Unificar | `/api/v2/users?role=cashier` |

### 🚗 VEHÍCULOS/UNIDADES

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/admin/units` | GET,POST,PUT,DELETE | UnitApiController | ✅ Unificar | `/api/v2/vehicles` |
| `/super-admin/units` | GET | UnitController | ❌ Deprecar | `/api/v2/vehicles` |
| `/clientes/unit` | GET | ClienteUnitController | ❌ Deprecar | `/api/v2/vehicles` |

### 🛣️ RUTAS

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/admin/routes` | GET,POST,PUT,DELETE | RouteController | ✅ Base | `/api/v2/routes` |

### ⏰ HORARIOS

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/horarios` | GET | HorarioController | ✅ Público | `/api/v2/schedules` |
| `/admin/route-unit-schedules` | GET,POST,PUT,DELETE | RouteUnitScheduleController | ✅ Admin | `/api/v2/schedules` |
| `/driver/route-unit-schedules` | GET | ApiRouteUnitScheduleApiController | ✅ Driver | `/api/v2/schedules` |
| `/clientes/route-unit-schedules` | GET | ClienteRouteUnitScheduleController | ✅ Cliente | `/api/v2/schedules` |

### 💰 VENTAS Y FINANZAS

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/admin/ventas` | GET,POST,PUT,DELETE | VentaApiController | ✅ Base | `/api/v2/sales` |
| `/viajes/sales` | POST | ViajeController | ✅ Unificar | `/api/v2/sales` |
| `/admin/finanzas/resumen` | GET | FinanzasController | ✅ Reportes | `/api/v2/reports/financial-summary` |
| `/admin/finanzas/balance-historico` | GET | FinanzasController | ✅ Reportes | `/api/v2/reports/balance-history` |
| `/admin/finanzas/top-rutas` | GET | FinanzasController | ✅ Reportes | `/api/v2/reports/top-routes` |
| `/admin/finanzas/ventas-detalle` | GET | FinanzasController | ✅ Reportes | `/api/v2/reports/sales-detail` |

### 📦 ENVÍOS Y ENTREGAS

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/envios` | GET | EnvioController | ✅ Público | `/api/v2/shipments` |
| `/driver/freights` | GET,PUT | ApiFleteController | ✅ Driver | `/api/v2/shipments?type=freight` |
| `/driver/shipments` | GET,PUT | ApiEntregasController | ✅ Driver | `/api/v2/shipments` |

### 🏢 EMPRESAS Y SITIOS

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/super-admin/companies` | GET | CompanyController | ✅ Base | `/api/v2/companies` |
| `/admin/sites` | GET,POST,PUT,DELETE | SiteAPIController | ✅ Admin | `/api/v2/sites` |
| `/super-admin/sites` | GET | SiteController | ❌ Deprecar | `/api/v2/sites` |

### 📍 LOCALIDADES Y COORDENADAS

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/localidades` | GET | LocalidadesController | ✅ Público | `/api/v2/locations` |
| `/admin/localities` | GET,POST,PUT,DELETE | LocalidadesApiController | ✅ Admin | `/api/v2/locations` |
| `/admin/coordinates` | GET,POST,PUT,DELETE | CoordinateAPIController | ✅ Admin | `/api/v2/coordinates` |

### 🎫 VIAJES Y RESERVAS

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/viajes` | GET | ViajeController | ✅ Base | `/api/v2/trips` |
| `/viajes/asientos/{id}` | GET | ViajeController | ✅ Base | `/api/v2/trips/{id}/seats` |
| `/viajes/tipos-tarifas` | GET | ViajeController | ✅ Base | `/api/v2/trips/fare-types` |
| `/viajes/info/{id}` | GET | ViajeController | ✅ Base | `/api/v2/trips/{id}` |
| `/viajes/tickets` | POST | ViajeController | ✅ Base | `/api/v2/trips/tickets` |

### 📊 DASHBOARD Y ESTADÍSTICAS

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/super-admin/admin/stats` | GET | AdminController | ✅ Base | `/api/v2/dashboard/stats` |
| `/super-admin/admin/dashboard` | GET | AdminController | ✅ Base | `/api/v2/dashboard` |
| `/driver/performance/{id}` | GET | ChoferEstadisticasDesempenoController | ✅ Base | `/api/v2/users/{id}/performance` |

### 💬 COMUNICACIÓN Y SOPORTE

| Endpoint Actual | Método | Controlador | Estado | Endpoint Propuesto v2 |
|----------------|--------|-------------|--------|----------------------|
| `/faqs` | GET | FaqController | ✅ Público | `/api/v2/support/faqs` |
| `/super-admin/faqs` | GET,POST,PUT,DELETE | FaqController | ✅ Admin | `/api/v2/support/faqs` |
| `/comments` | GET,POST | CommentController | ✅ Público | `/api/v2/support/comments` |
| `/super-admin/comments` | GET,PUT,DELETE | CommentController | ✅ Admin | `/api/v2/support/comments` |
| `/complaints` | POST | ComplaintController | ✅ Usuario | `/api/v2/support/complaints` |
| `/super-admin/complaints` | GET,PUT,DELETE | ComplaintController | ✅ Admin | `/api/v2/support/complaints` |
| `/forms` | GET,POST | FormController | ✅ Mixto | `/api/v2/support/forms` |

---

## 📈 RESUMEN DE CONSOLIDACIÓN

### Endpoints Actuales: **89**
### Endpoints Propuestos v2: **47** (reducción del 47%)

### Por Estado:
- ✅ **Mantener/Unificar**: 67 endpoints
- ❌ **Deprecar**: 22 endpoints

### Controladores Actuales: **28**
### Controladores Propuestos v2: **12** (reducción del 57%)

---

## 🔄 MAPEO DE MIGRACIÓN CRÍTICA

### Endpoints de Alto Impacto (requieren migración cuidadosa):
1. `/auth/mobile-login` → `/api/v2/auth/login`
2. `/viajes/*` → `/api/v2/trips/*`
3. `/admin/ventas` → `/api/v2/sales`
4. `/admin/drivers` → `/api/v2/users?role=driver`
5. `/admin/route-unit-schedules` → `/api/v2/schedules`

### Endpoints Legacy (deprecar gradualmente):
1. `/login-cliente`
2. `/user-cliente`
3. `/user-cliente/upload-photo`
4. Duplicados de login admin

---

**Última actualización:** 23 de septiembre de 2025