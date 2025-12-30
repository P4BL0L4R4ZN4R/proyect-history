<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\UnoController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('auth.login');
});




// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes();


//login y demas

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});



Route::match(['get', 'post', 'put'], 'LaboratorioIndex', [LaboratorioController::class, 'LaboratorioIndex'])->name('laboratorio.LaboratorioIndex')->middleware('auth');






 
    //Rutas y modales
   

    // Route::match(['get', 'post', 'put'], '/ConsumosXPeriodo/{id}', [LaboratorioController::class, 'ConsumosXPeriodo']);



 

    Route::match(['get', 'post', 'put'], '/laboratorio/fecha', [LaboratorioController::class, 'fechaDefault']);

    Route::match(['get', 'post', 'put'], '/fecha/', [LaboratorioController::class, 'fechaDefault']);

    Route::match(['get', 'post', 'put'], 'laboratorio/fecha/', [LaboratorioController::class, 'fechaDefault']);

    Route::match(['get', 'post', 'put'], 'laboratorio/ConsumosXPeriodo/{id}', [LaboratorioController::class, 'ConsumosXPeriodo']);

    Route::match(['get', 'post', 'put'], '/ConsumosXPeriodo/{id}', [LaboratorioController::class, 'ConsumosXPeriodo']);

    Route::get('/laboratorio_ver/{id}', [LaboratorioController::class, 'show']); // En uso  Important

    Route::get('/laboratorio_editConexion/{id}', [LaboratorioController::class, 'editarConexion']);

    Route::get('/laboratorio_edit/{id}', [LaboratorioController::class, 'editar']); // En uso  Important

    Route::get('/laboratorio_editCFDI/{id}', [LaboratorioController::class, 'editarCFDI']); // En uso  Important

    Route::get('/laboratorio_mostrarCFDI/{id}', [LaboratorioController::class, 'mostrarCFDI']); // En uso  Important

    // Route::match(['get', 'post', 'put'], '/laboratorio_update/{id}', [LaboratorioController::class, 'update']);

    Route::put('/laboratorio_update/{id}', [LaboratorioController::class, 'update'])->name('laboratorio.update');

    Route::put('/laboratorio_actualizarCFDI/{id}', [LaboratorioController::class, 'actualizarCFDI'])->name('laboratorio.actualizarCFDI')->middleware('auth');
    



    
    
    // Route::match(['get', 'post', 'put'], '/editar', [LaboratorioController::class, 'editar'])->name('laboratorio.editar')->middleware('auth');
    
    
    // Route::post('/editar', [LaboratorioController::class, 'editar'])->name('laboratorio.editar');


    //Vistas y procesos
    
Route::get('inactivo', [LaboratorioController::class, 'conteoinactivos'])->name('laboratorio.conteoinactivos')->middleware('auth');

Route::get('bitacora', [LaboratorioController::class, 'Bitacoraindex'])->name('laboratorio.Bitacoraindex')->middleware('auth');

Route::get('laboratorio/veterinaria', [LaboratorioController::class, 'Cfdiveterinaria'])->name('laboratorio.Cfdiveterinaria')->middleware('auth');

Route::get('laboratorio/suscripcion', [LaboratorioController::class, 'Cfdisuscripcion'])->name('laboratorio.Cfdisuscripcion')->middleware('auth');

Route::get('laboratorio/version', [LaboratorioController::class, 'Cfdiversion'])->name('laboratorio.Cfdiversion')->middleware('auth');

Route::get('laboratorio/labsinuso', [LaboratorioController::class, 'Labsinuso'])->name('laboratorio.Labsinuso')->middleware('auth');

Route::get('laboratorio/filtro', [LaboratorioController::class, 'filterCFDI'])->name('laboratorio.filterCFDI')->middleware('auth');



// Route::get('laboratorio',[LaboratorioController::class, 'index'])->name('laboratorio.index')->middleware('auth');
// Route::get('laboratorio/create',[LaboratorioController::class, 'create'])->name('laboratorio.create')->middleware('auth');
// Route::post('laboratorio',[LaboratorioController::class, 'store'])->name('laboratorio.store')->middleware('auth');
// Route::get('laboratorio/{id}',[LaboratorioController::class, 'show'])->name('laboratorio.show')->middleware('auth');
// Route::get('laboratorio/{id}/edit',[LaboratorioController::class, 'edit'])->name('laboratorio.edit')->middleware('auth');
// Route::put('laboratorio/{id}',[LaboratorioController::class, 'update'])->name('laboratorio.update')->middleware('auth');
// Route::delete('laboratorio/{id}',[LaboratorioController::class, 'destroy'])->name('laboratorio.destroy')->middleware('auth');

Route::resource('laboratorio','App\Http\Controllers\LaboratorioController')->middleware('auth');

Route::post('/actualizar-suscripcion', [LaboratorioController::class, 'actualizarSuscripcion'])->name('actualizar.suscripcion');


// Route::match(['post', 'put'],'laboratorio/actualizarSuscripcion', [LaboratorioController::class, 'actualizarSuscripcion'])->name('laboratorio.actualizarSuscripcion')->middleware('auth');

Route::match(['post', 'put'],'laboratorio/actualizarVeterinaria', [LaboratorioController::class, 'actualizarVeterinaria'])->name('laboratorio.actualizarVeterinaria')->middleware('auth');



Route::put('laboratorio/configsave/{id}', [LaboratorioController::class, 'configsave'])->name('laboratorio.configsave')->middleware('auth');

Route::post('laboratorio/newconexion/{id}', [LaboratorioController::class, 'newconexion'])->name('laboratorio.newconexion')->middleware('auth');

Route::delete('laboratorio/borrarCFDI/{id}', [LaboratorioController::class, 'borrarCFDI'])->name('laboratorio.borrarCFDI')->middleware('auth');



// Route::post('laboratorio/{id}/testconexion', [LaboratorioController::class, 'testConexion'])->name('laboratorio.testconexion');

Route::match(['post', 'put'], 'laboratorio/{id}/revisaryguardar', [LaboratorioController::class, 'revisarYGuardar'])->name('laboratorio.revisaryguardar')->middleware('auth');

Route::match(['post', 'put'], 'laboratorio/testconexion', [LaboratorioController::class, 'testConexion'])->name('laboratorio.testconexion')->middleware('auth');

Route::match(['post', 'put'], 'laboratorio/testnewconexion', [LaboratorioController::class, 'testNewConexion'])->name('laboratorio.testnewconexion')->middleware('auth');

Route::resource('usuario','App\Http\Controllers\UserController')->middleware('auth');

Route::match(['post', 'get'], '/laboratorios/pdf', [LaboratorioController::class, 'exportPDF'])->name('laboratorios.pdf');
