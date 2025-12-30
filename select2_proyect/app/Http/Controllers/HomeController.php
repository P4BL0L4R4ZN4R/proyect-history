<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Session;

use Exception;
use Carbon\Carbon;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Datatable;
use PDF;

use Yajra\DataTables\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Remoto;
use App\Models\Laboratorio;
use App\Models\Bitacora;
use App\Models\User;
use App\Models\Sesion;

use App\Services\GestorBasesDeDatos;


class HomeController extends Controller
{ 
    
    
    
    
    private $gestorBD;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct(GestorBasesDeDatos $gestorBD)
     {
         $this->middleware('auth');
         
         $this->gestorBD = $gestorBD; 
         
     }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */



    







    public function index(Request $request)
    {
        try {

            $laboratorios = Laboratorio::all();

           
            $errores = [];
            $sucursales = [];
            $cfdis = [];
            $activadosCount = 0; // Contador para CFDis activados
            $desactivadosCount = 0; // Contador para CFDis desactivados
            $conteosPorCFDi = [];
            $totalSucursales = 0; // Variable para contar el total de sucursales
            $cfdisValidosCount = 0; // Contador de CFDis válidos
            $VETERINARIASI = 0;
            $VETERINARIANO= 0;
            $conteoMesAnterior= 0;

            // $BDtotales =


            foreach ($laboratorios as $laboratorio) {
                try {

                    $connectionName = 'externo_' . $laboratorio->id;
    
                    // Obtener conexión del gestor de bases de datos
                    $conexion = $this->gestorBD->obtenerConexion($connectionName);

                        // Obtener y almacenar datos de sucursales
                        $sucursalesData = $conexion
                            ->table('sucursales as s')
                            ->select('idSucursal', 'descripcion')
                            ->get()
                            ->keyBy('idSucursal')
                            ->toArray();
    
                        $sucursales[$laboratorio->id] = $sucursalesData;
    
                        // Consultar datos de CFDis con información de suscripción
                        $cfdis[$laboratorio->id] = $conexion
                            ->table('cfdi_parametros as c')
                            ->leftJoin('sucursales as s', 'c.CFDISUCURSAL', '=', 's.idSucursal')
                            ->select(
                                'c.CFDISUCURSAL',
                                'c.id',
                                'c.SUSCRIPCION',
                                'c.VETERINARIA',
                                's.descripcion as descripcion_sucursal',
                                'c.flag_sucursales',
                            )
                            ->whereNotNull('s.idSucursal') // Filtrar solo registros con sucursal válida
                            ->get();

                        // Contar CFDis activados y desactivados
                        foreach ($cfdis[$laboratorio->id] as $cfdi) {
                            if ($cfdi->SUSCRIPCION === 'ACTIVADO') {
                                $activadosCount++;
                            } elseif ($cfdi->SUSCRIPCION === 'DESACTIVADO') {
                                $desactivadosCount++;
                            }
                        }
    

                        foreach ($cfdis[$laboratorio->id] as $cfdi) {
                            if ($cfdi->VETERINARIA === '1') {
                                $VETERINARIASI++;
                            } elseif ($cfdi->VETERINARIA === '0') {
                                $VETERINARIANO++;
                            }
                        }
    

                        // Consultar conteos por CFDI
                        foreach ($cfdis[$laboratorio->id] as $cfdi) {
                            $IDsucursal = $cfdi->CFDISUCURSAL;
                            $fechaMesAnterior = now()->subMonth();
    
                            $conteos = $conexion
                                ->table('solicitud')
                                ->selectRaw('
                                    COUNT(CASE WHEN DATE(fecha) = CURDATE() THEN 1 END) AS conteoPorDia,
                                    COUNT(CASE WHEN YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE()) THEN 1 END) AS conteoPorMes,
                                    COUNT(CASE WHEN YEAR(fecha) = YEAR(CURDATE()) THEN 1 END) AS conteoPorAnio,
                                    COUNT(CASE WHEN YEAR(fecha) = ? AND MONTH(fecha) = ? THEN 1 END) AS conteoMesAnterior,
                                    MAX(updated_at) AS lastUpdate
                                ', [$fechaMesAnterior->year, $fechaMesAnterior->month])
                                ->where('sucursal', $IDsucursal)
                                ->first();
    
                            if ($conteos) {
                                $conteosPorCFDi[$laboratorio->id][$cfdi->id] = [
                                    'conteoPorDia' => $conteos->conteoPorDia ?? 0,
                                    'conteoPorMes' => $conteos->conteoPorMes ?? 0,
                                    'conteoPorAnio' => $conteos->conteoPorAnio ?? 0,
                                    'conteoMesAnterior' => $conteos->conteoMesAnterior ?? 0,
                                    'lastUpdate' => $conteos->lastUpdate ?? null,
                                ];
    
                                // Contar CFDis válidos
                                if ($conteos->conteoPorDia <= 0) {
                                    $cfdisValidosCount++;
                                }
                            }
                                        $totalsuc = $activadosCount + $desactivadosCount;
                                                                
                                        $totalSucursales = $totalsuc -  $cfdisValidosCount;

                                if ($conteos->conteoMesAnterior > $cfdi->flag_sucursales) {
                                    $conteoMesAnterior++;
                                } 

                                


                        }
    
                        // Incrementar contador de sucursales
                        



    
                        
    

                } catch (\Exception $e) {
                    Log::error('Error al procesar laboratorio ' . $laboratorio->id . ': ' . $e->getMessage());
                    $errores[] = 'Error en laboratorio ' . $laboratorio->nombre . ': ' . $e->getMessage();
                }
            }
            
            // $usuariosactivos = Session::whereNotNull('user_id')  // Filtra las sesiones con user_id no nulo
            // ->groupBy('user_id')  // Agrupa por el campo user_id
            // ->count();  // Cuenta el número de grupos (usuarios distintos)
            
            $usuariosactivos = Sesion::whereNotNull('user_id')  // Filtra las sesiones con user_id no nulo
            ->distinct('user_id')  // Asegura que contemos cada user_id solo una vez
            ->count('user_id');  // Cuenta el número de user_id únicos con sesiones activas
        

            $sucursalsinexceso = $totalsuc - $conteoMesAnterior;



            // log::info('Laboratorios contados: ' . $usuariosactivos);
           
            $totalActiveSessions = Sesion::whereNotNull('user_id')->count();

            // Contar el número total de sesiones activas
        
                // $sesiones = Session::count(); // Obtener todas las sesiones activas

            $laboratorioscontar = Laboratorio::whereNotNull('base_de_datos_id')->count();
            $laboratoriossinexceso =$laboratorioscontar - $conteoMesAnterior;

            if ($request->ajax()) {
                return response()->json([
                    
                //     'sucursales' => $sucursales,
                    
                //     'activadosCount' => $activadosCount,
                //     'desactivadosCount' => $desactivadosCount,
                //     'conteosPorCFDi' => $conteosPorCFDi,
                //   'totalsuc' => $totalsuc,
                //     'totalSucursales' => $totalSucursales,
                //     'cfdisValidosCount' => $cfdisValidosCount,
                //     'laboratorioscontar'   => $laboratorioscontar,
                //     'totalActiveSessions' => $totalActiveSessions,
                //     'usuariosactivos' => $usuariosactivos,
                ]);
            }
    
            return view('dashboard', [
            
                'VETERINARIASI' => $VETERINARIASI,
                'activadosCount' => $activadosCount,
                'desactivadosCount' => $desactivadosCount,
                'conteosPorCFDi' => $conteosPorCFDi,
                'totalsuc' => $totalsuc,
                'totalSucursales' => $totalSucursales,
                'cfdisValidosCount' => $cfdisValidosCount,
                'laboratorioscontar'   => $laboratorioscontar,
                'totalActiveSessions' => $totalActiveSessions,
                'usuariosactivos' => $usuariosactivos,
                'conteoMesAnterior' => $conteoMesAnterior,
                // 'laboratoriossinexceso' => $laboratoriossinexceso,
                'sucursalsinexceso' => $sucursalsinexceso,
                
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error al procesar la solicitud de laboratorios: ' . $e->getMessage());
            // return response()->json(['error' => 'Se ha producido un error. Por favor, inténtelo de nuevo más tarde.'], 500);
            return view('dashboard', [
            

            ]);

        }
    }
    

}
