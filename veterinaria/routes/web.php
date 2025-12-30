<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\ProductosController;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\ClasificacionController;
use App\Jobs\RevisarProductosCaducados;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CajaController;
use App\Http\Middleware\ControlCortes;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'caja.abierta'])->group(function () {

    // Ventas
    Route::get('/ventas', [DetalleVentaController::class, 'index'])
        ->name('ventas.index');

    Route::get('/ventas/json', [VentasController::class, 'json'])
        ->name('ventas.json');

    Route::get('/ventas/{id}', [VentasController::class, 'show'])
        ->name('ventas.show');

    Route::post('/ventas', [VentasController::class, 'store'])
        ->name('ventas.store');

    Route::get('/venta/create', [VentasController::class, 'create'])
        ->name('ventas.create');

        // Rutas para Productos
    Route::get('productos/data', [ProductosController::class, 'data'])
        ->name('productos.data');

    Route::get('productos/databajas', [ProductosController::class, 'databajas'])
        ->name('productos.databajas');

    Route::put('productos/setbaja/{id}', [ProductosController::class, 'setbaja'])
        ->name('productos.setbaja');

    Route::get('productos/bajas', [ProductosController::class, 'Bajasindex'])
        ->name('productos.bajas');

    Route::match(['PUT', 'POST'],'productos/bajadefinitiva/{id}', [ProductosController::class, 'eliminar'])
        ->name('productos.bajadefinitiva');

    Route::resource('productos', ProductosController::class);



    //notificaciones
    // Route::get('/verificar-caducidad', [ProductosController::class, 'verificarCaducidad'])
    //      ->name('verificar-caducidad');

    // Route::get('/verificar-stock', [ProductosController::class, 'verificarStock'])
    //      ->name('verificar-stock');

    Route::get('/notificaciones', [ProductosController::class, 'getNotificationsData'])
         ->name('notificaciones');


           // alertas
    Route::get('/alertas-caducidad', [ProductosController::class, 'alertaCaducidad'])
         ->name('alertas-caducidad');

    Route::get('/alertas-stock', [ProductosController::class, 'alertaStock'])
         ->name('alertas-stock');

    // Clasificaciones (categorias y subcategorias)
    Route::get('/categorias/ajax', [ClasificacionController::class, 'categoriasAjax'])
         ->name('clasificacion.categorias.ajax');

    Route::get('/subcategorias/ajax', [ClasificacionController::class, 'subcategoriasAjax'])
         ->name('clasificacion.subcategorias.ajax');

    Route::get('/clasificacion', [ClasificacionController::class, 'index'])
         ->name('clasificacion.index');

    Route::post('/categorias', [ClasificacionController::class, 'storeCategoria'])
        ->name('clasificacion.categorias.store');

    Route::post('/subcategorias', [ClasificacionController::class, 'storeSubcategoria'])
        ->name('clasificacion.subcategorias.store');


    Route::put('/categorias/{id}', [ClasificacionController::class, 'updateCategoria'])
        ->name('clasificacion.categorias.update');

    Route::put('/subcategorias/{id}', [ClasificacionController::class, 'updateSubcategoria'])
        ->name('clasificacion.subcategorias.update');

    Route::delete('/categorias/{id}', [ClasificacionController::class, 'destroyCategoria'])
        ->name('clasificacion.categorias.destroy');

    Route::delete('/subcategorias/{id}', [ClasificacionController::class, 'destroySubcategoria'])
        ->name('clasificacion.subcategorias.destroy');


         // Configuración
    Route::get('/configuracion/edit', [ConfiguracionController::class, 'edit'])
         ->name('configuracion.edit');

    Route::match(['PUT', 'POST'], '/configuracion', [ConfiguracionController::class, 'update'])
         ->name('configuracion.update');

         // Reportes
    Route::get('/exportar/pdf', [DetalleVentaController::class, 'exportarPdf'])
        ->name('exportar.pdf');;

    Route::get('detalleventa/data', [DetalleVentaController::class, 'data'])
        ->name('detalleventa.data');

    Route::get('/exportar/excel', [DetalleVentaController::class, 'exportarExcel'])
        ->name('exportar.excel');

        // usuarios
    Route::resource('users', UserController::class);


    Route::get('/caja-data', [CajaController::class, 'data'])
        ->name('caja.data');

    Route::get('/historial/cortes', [CajaController::class, 'index'])
        ->name('caja.index');



    // Opción A: Todos los datos en una sola ruta
    Route::get('/dashboard/datos-completos', [DashboardController::class, 'obtenerTodosLosDatos']);

    // Opción B: Rutas individuales
    Route::get('/dashboard/total-ventas-mes', [DashboardController::class, 'totalVentasMes']);
    Route::get('/dashboard/ventas-hoy', [DashboardController::class, 'ventasHoy']);
    Route::get('/dashboard/total-productos', [DashboardController::class, 'totalProductos']);
    Route::get('/dashboard/productos-bajo-stock', [DashboardController::class, 'productosBajoStock']);
    Route::get('/dashboard/productos-proximos-caducar', [DashboardController::class, 'productosProximosCaducar']);
    Route::get('/dashboard/corte-actual', [DashboardController::class, 'corteActual']);
    Route::get('/dashboard/promedio-venta-diaria', [DashboardController::class, 'promedioVentaDiaria']);
    Route::get('/dashboard/ventas-del-dia', [DashboardController::class, 'ticketsHoy']);

});


    // cortes
Route::middleware(['auth'])->group(function () {

    //formuulario
    Route::get('/turno', [CajaController::class, 'create'])
        ->name('caja.abierta');

    // guardar
    Route::post('/turnoinicio', [CajaController::class, 'store'])
        ->name('caja.store');




});
