<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
//use App\Personalizado\Conexion;
use Illuminate\Support\Facades\Log;


use Laravel\Fortify\Features;

use App\Services\GestorBasesDeDatos;

use Illuminate\Support\Facades\Session;

use Exception;
use Carbon\Carbon;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Datatable;
use PDF;



use Yajra\DataTables\Facades\DataTables; // *! Important importar esta libreria e instalarla en el sistema

// use Yajra\DataTables\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Remoto;
use App\Models\Laboratorio;
use App\Models\Bitacora;
use App\Models\User;
use App\Models\Sesion;
// use DataTables;
use Illuminate\Support\Facades\DB;
use App\Personalizado\Conexion;
class ConexionFallidaException extends Exception {}

class CredencialesInvalidasException extends Exception {}

class BaseDeDatosNoEncontradaException extends Exception {}
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

//use Illuminate\Support\Facades\Config;





class LaboratorioController extends Controller
{
    /**
     * Display a listing of the resource.
     */




    public function index(Request $request)
    {
          
          return view('laboratorios.laboratorioPrueba');
    }



    public function show($id)
    {
        try {
            // $id = $request->input('Idlaboratorio');
            $laboratorio = Laboratorio::findOrFail($id);
            
            return response()->json([
                'success' => true,
                // 'idlaboratorio' => $laboratorio->id,
                'VerNombre' => $laboratorio->nombre,
                'VerNotas' => $laboratorio->notas,
                // Agregar más campos según sea necesario
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener datos del laboratorio: ' . $e->getMessage(),
            ], 500);
        }
    }




    public function editar($id)
    {
        try {
            // $id = $request->input('Idlaboratorio');
            $laboratorio = Laboratorio::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'idlaboratorio' => $laboratorio->id,
                'nombre' => $laboratorio->nombre,
                'notas' => $laboratorio->notas,
                // Agregar más campos según sea necesario
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener datos del laboratorio: ' . $e->getMessage(),
            ], 500);
        }
    }
    



    public function fechaDefault(Request $request)
    
    {
        try {
            
            $fechaActual = Carbon::now();
                $primerDiaDelMesActual = $fechaActual->copy()->startOfMonth();
                // $ultimoDiaDelMesActual = $fechaActual->copy()->endOfMonth();
                // $primerDiaDelAnioActual = $fechaActual->copy()->startOfYear();
                // $ultimoDiaDelAnioActual = $fechaActual->copy()->endOfYear();
                // $ultimoDiaDelMesAnterior = $primerDiaDelMesActual->copy()->subDay();
                // $primerDiaDelMesAnterior = $ultimoDiaDelMesAnterior->copy()->startOfMonth();

                // Formatea las fechas para SQL
                $fechaHoy = $fechaActual->toDateString();
                $primerDiaMesActual = $primerDiaDelMesActual->toDateString();
                // $ultimoDiaMesActual = $ultimoDiaDelMesActual->toDateString();
                // $primerDiaAnioActual = $primerDiaDelAnioActual->toDateString();
                // $ultimoDiaAnioActual = $ultimoDiaDelAnioActual->toDateString();
                // $primerDiaMesAnterior = $primerDiaDelMesAnterior->toDateString();
                // $ultimoDiaMesAnterior = $ultimoDiaDelMesAnterior->toDateString();

            
            return response()->json([
                'success' => true,
                'fechaFin'   => $fechaHoy,
                'fechaInicio'  => $primerDiaMesActual,

                // Agregar más campos según sea necesario
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener datos del laboratorio: ' . $e->getMessage(),
            ], 500);
        }
    }




    
    public function editarConexion($id)
    {
        try {
            // $id = $request->input('Idlaboratorio');
            $laboratorio = Laboratorio::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'id' => $laboratorio->id,
                'BDs' => $laboratorio->bases_de_datos->base_de_datos ,
                'servidor' => $laboratorio->bases_de_datos->servidor_sql ,
                'contrasena' => $laboratorio->bases_de_datos->password_sql ,
                'usuario_sql' => $laboratorio->bases_de_datos->usuario_sql ,

                // Agregar más campos según sea necesario
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener datos del laboratorio: ' . $e->getMessage(),
            ], 500);
        }
    }


// Este es el service para la conexion
    private $gestorBD;

    public function __construct(GestorBasesDeDatos $gestorBD)
    {
        $this->gestorBD = $gestorBD;
    }



    public function LaboratorioIndex(Request $request)
    {




        // $length = $request->input('length', 10); // Número de registros por página
        // $start = $request->input('start', 0); // Inicio de la página actual
        
        // log::info('length: ' . $length);
        // log::info('start: ' . $start);


        // $paginacion = $request->input('lengthMenu', 1);
        

        // $query = Laboratorio::query()->whereNotNull('base_de_datos_id')->orderBy('created_at', 'desc');


        



        // $laboratorios = $query->skip($start)->take($length)->get();

        // log::info('Paginacion: ' . $paginacion);

        


        try {

            // $inicioTotal = microtime(true);
            // Log::info('Tiempo de inicio: ' . $inicioTotal . ' segundos');

            // $inicioLaboratorios = microtime(true);
            // // Obtener laboratorios que tienen asignada una base de datos
            $laboratorios = Laboratorio::whereNotNull('base_de_datos_id')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                        // ->paginate(1);
    
    // $finLaboratorios = microtime(true);
    // Log::info('Tiempo para obtener laboratorios: ' . ($finLaboratorios - $inicioLaboratorios) . ' segundos');


            // Array para almacenar datos
            $data = [];
    
            // Configurar conexiones externas generales
            $this->gestorBD->conectarBD();
    
            foreach ($laboratorios as $laboratorio) {

                // $inicioLaboratorio = microtime(true);

                try {
                    // Configurar conexión externa dinámicamente
                    $connectionName = 'externo_' . $laboratorio->id;
    
                    // Obtener conexión del gestor de bases de datos
                    $conexion = $this->gestorBD->obtenerConexion($connectionName);


                $fechaActual = Carbon::now();
                $primerDiaDelMesActual = $fechaActual->copy()->startOfMonth();
                $ultimoDiaDelMesActual = $fechaActual->copy()->endOfMonth();
                $primerDiaDelAnioActual = $fechaActual->copy()->startOfYear();
                $ultimoDiaDelAnioActual = $fechaActual->copy()->endOfYear();
                $ultimoDiaDelMesAnterior = $primerDiaDelMesActual->copy()->subDay();
                $primerDiaDelMesAnterior = $ultimoDiaDelMesAnterior->copy()->startOfMonth();




                // Formatea las fechas para SQL
                $fechaHoy = $fechaActual->toDateString();
                $primerDiaMesActual = $primerDiaDelMesActual->toDateString();
                $ultimoDiaMesActual = $ultimoDiaDelMesActual->toDateString();
                $primerDiaAnioActual = $primerDiaDelAnioActual->toDateString();
                $ultimoDiaAnioActual = $ultimoDiaDelAnioActual->toDateString();
                $primerDiaMesAnterior = $primerDiaDelMesAnterior->toDateString();
                $ultimoDiaMesAnterior = $ultimoDiaDelMesAnterior->toDateString();



                // log::info('Fecha actual: ' . $fechaActual->toDateString());
                // log::info('Primer día del mes actual: ' . $primerDiaDelMesActual->toDateString());
                // log::info('Último día del mes actual: ' . $ultimoDiaDelMesActual->toDateString());
                // log::info('Primer día del mes anterior: ' . $primerDiaDelMesAnterior->toDateString());
                // log::info('Último día del mes anterior: ' . $ultimoDiaDelMesAnterior->toDateString());
                // log::info('Primer día del año actual: ' . $primerDiaDelAnioActual->toDateString());
                // log::info('Último día del año actual: ' . $ultimoDiaDelAnioActual->toDateString());

                // $inicioConsulta = microtime(true);

                    $sucursales = $conexion
                    ->table('sucursales as s')
                    ->select([
                        'c.CFDISUCURSAL',
                        'c.id as CFDIid',
                        'c.VETERINARIA',
                        'c.TIMESESION',
                        'c.SUSCRIPCION',
                        'c.SESIONESLIMITE',
                        'c.VERSION',
                        'c.flag_sucursales',
                        's.descripcion as descripcion_sucursal',
                        's.idSucursal',


                        \DB::raw('(SELECT MAX(updated_at) 
                                    FROM solicitud 
                                    WHERE sucursal = s.idSucursal) AS lastUpdate'),

                        \DB::raw("(SELECT COUNT(*) 
                           FROM solicitud 
                           WHERE sucursal = s.idSucursal
                           AND (cotizacion != 1 
                           OR cotizacion IS NULL
                           OR cotizacion = '')
                           AND fecha BETWEEN '$fechaHoy 00:00:00' AND '$fechaHoy 23:59:59') AS conteoPorDia"),

                        \DB::raw("(SELECT COUNT(*) 
                           FROM solicitud 
                           WHERE sucursal = s.idSucursal
                           AND (cotizacion != 1 
                           OR cotizacion IS NULL
                           OR cotizacion = '')
                           AND fecha BETWEEN '$primerDiaMesActual 00:00:00' AND '$ultimoDiaMesActual 23:59:59') AS conteoPorMes"),

                           \DB::raw("(SELECT COUNT(*) 
                           FROM solicitud 
                           WHERE sucursal = s.idSucursal
                           AND (cotizacion != 1 
                           OR cotizacion IS NULL
                           OR cotizacion = '')
                           AND fecha BETWEEN '$primerDiaAnioActual 00:00:00' AND '$ultimoDiaAnioActual 23:59:59') AS conteoPorAnio"),
               

                        \DB::raw("(SELECT COUNT(*) 
                                FROM solicitud 
                                WHERE sucursal = s.idSucursal
                                AND (cotizacion != 1 
                                OR cotizacion IS NULL
                                OR cotizacion = '')
                                AND fecha BETWEEN '$primerDiaMesAnterior 00:00:00' AND '$ultimoDiaMesAnterior 23:59:59') AS conteoMesAnterior"),
                    
                    
                                ])
                    ->leftJoin('cfdi_parametros as c', 's.idSucursal', '=', 'c.CFDISUCURSAL')
                    ->whereNotNull('c.CFDISUCURSAL')
                    ->get();


                    // $finConsulta = microtime(true);
                    // Log::info('Tiempo para consultar sucursales del laboratorio ' . $laboratorio->id . ': ' . ($finConsulta - $inicioConsulta) . ' segundos');


                    // Transformar y agregar datos al array $data
                    foreach ($sucursales as $sucursal) {
                       

                        $data[] = [
                            'nombre' => $laboratorio->id . '|' . ($laboratorio->nombre ?? ''),
                            'laboratorio' => $laboratorio->nombre ?? '',
                            'VETERINARIA' => $sucursal->VETERINARIA === '1' ? 'Veterinaria' : ($sucursal->VETERINARIA === '0' ? 'Laboratorio' : ''), 
                            'TIMESESION' => $sucursal->TIMESESION ?? '',
                            'SUSCRIPCION' => $sucursal->SUSCRIPCION ?? '',
                            'SESIONESLIMITE' => $sucursal->SESIONESLIMITE ?? '',
                            'VERSION' => $sucursal->VERSION ?? '',
                            'flag_sucursales' => $sucursal->flag_sucursales ?? '',
                            'descripcion_sucursal' => $sucursal->descripcion_sucursal ?? '',
                            'conteoPorDia' => $sucursal->conteoPorDia ?? '',
                            'conteoPorMes' => $sucursal->conteoPorMes ?? '',
                            'conteoPorAnio' => $sucursal->conteoPorAnio ?? '',
                            'conteoMesAnterior' => $sucursal->conteoMesAnterior ?? '',
                            'lastUpdate' => $sucursal->lastUpdate ?? '',
                            
                            // 'created_at' => $laboratorio->created_at ?? '',
                            
                            // 'created_at' => $laboratorio->created_at->toDateString() ?? '',

                            
                            
                            
                            // Datos semiopcionales
                            
                            'CFDISUCURSAL' => $sucursal->CFDISUCURSAL ?? '',
                            'Idlaboratorio' => $laboratorio->id ?? '',
                            'CFDIid' => $sucursal->CFDIid ?? '',
                            'idSucursal' => $sucursal->idSucursal ?? '',
                        ];
                    }
                    
                    // $finLaboratorio = microtime(true);



                    // log::info($data);


                } catch (\PDOException $pdoException) {
                    // Capturar error de PDO (por ejemplo, autenticación incorrecta)
                    Log::error('Error de conexión a la base de datos para el laboratorio ' . $laboratorio->id . ': ' . $pdoException->getMessage());
    
                    // Registrar solo el nombre del laboratorio afectado
                    $data[] = [
                        'nombre' => $laboratorio->id . '|' . ($laboratorio->nombre ?? '' ). ' ¡¡¡Error en la base de datos!!!',
                        'laboratorio' => $laboratorio->nombre ?? '',
                        'Idlaboratorio' =>  '¡¡¡Error en la base de datos!!!',
                        'CFDISUCURSAL' =>  '¡¡¡Error en la base de datos!!!',
                        'CFDIid' => '¡¡¡Error en la base de datos!!!',
                        'VETERINARIA' => '¡¡¡Error en la base de datos!!!', 
                        'TIMESESION' => '¡¡¡Error en la base de datos!!!',
                        'SUSCRIPCION' =>  '¡¡¡Error en la base de datos!!!',
                        'SESIONESLIMITE' => '¡¡¡Error en la base de datos!!!',
                        'VERSION' => '¡¡¡Error en la base de datos!!!',
                        'flag_sucursales' =>  '¡¡¡Error en la base de datos!!!',
                        'descripcion_sucursal' =>  '¡¡¡Error en la base de datos!!!',
                        'idSucursal' =>  '¡¡¡Error en la base de datos!!!',
                        'conteoPorDia' => '¡¡¡Error en la base de datos!!!',
                        'conteoPorMes' => '¡¡¡Error en la base de datos!!!',
                        'conteoPorAnio' => '¡¡¡Error en la base de datos!!!',
                        'conteoMesAnterior' => '¡¡¡Error en la base de datos!!!',
                        'lastUpdate' => '¡¡¡Error en la base de datos!!!',
                        'error' => 'Error de conexión a la base de datos. Verificar configuración.',
                ];
                    
                } catch (\Exception $e) {
                    // Loggear error genérico al obtener datos del laboratorio
                    Log::error('Error al obtener datos del laboratorio ' . $laboratorio->id . ': ' . $e->getMessage());
                    // Continuar con el siguiente laboratorio en caso de error
                    continue;
                }
            }
    
            // Devolver los datos para DataTables
            if ($request->ajax()) {
                return DataTables::of($data)->toJson();
            }

            
            // $finTotal = microtime(true);
            // Log::info('Tiempo total del proceso: ' . ($finTotal - $inicioTotal) . ' segundos');


        } catch (\Exception $e) {
            // Manejo de errores generales
            Log::error('Error al procesar la solicitud de laboratorios: ' . $e->getMessage());
    
            if ($request->ajax()) {
                return response()->json(['error' => 'Se ha producido un error. Por favor, inténtelo de nuevo más tarde.'], 500);
            }
        }
    }
    



    
    
    public function ConsumosXPeriodo(Request $request, $id)
    {
        try {
            // Obtener el laboratorio
            $laboratorio = Laboratorio::findOrFail($id);

            // Configurar la conexión externa
            $this->gestorBD->conectarBDyEditar($id);

            // Obtener conexión externa
            $connectionName = 'externo_' . $laboratorio->id;
            $conexion = $this->gestorBD->obtenerConexion($connectionName);

            // Obtener parámetros de la solicitud
            $idSucursal = $request->input('idSucursal');
            $fechaInicio = $request->input('fechaInicio');
            $fechaFin = $request->input('fechaFin');


                        $fechaActual = Carbon::now();
                        $fechaHoy = $fechaActual->toDateString();


                        $resultado = $conexion
                        ->table('solicitud')
                        ->where('sucursal', $idSucursal)
                        ->whereBetween('fecha', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                         ->where(function($query) {
                                    $query->where('cotizacion', '!=', 1)
                                        ->orWhereNull('cotizacion')
                                        ->orWhere('cotizacion', '');
                                                })
                        ->count();

                        // concatenar texto si se necesita
                        $resultados = $resultado ;       
                  

                        if (is_null($fechaInicio) || is_null($fechaFin)) {
                           
                        
                                    $resultado = $conexion
                                        ->table('solicitud')
                                        ->where('sucursal', $idSucursal)
                                        ->whereBetween('fecha', [$fechaHoy . ' 00:00:00', $fechaHoy . ' 23:59:59'])
                                         ->where(function($query) {
                                            $query->where('cotizacion', '!=', 1)
                                                ->orWhereNull('cotizacion')
                                                ->orWhere('cotizacion', '');
                                                    })
                                        ->count();

                                $resultados = 'Hoy: ' .$resultado ;       
                            }

                    
                if (is_null($resultados == 0)) {
                    $resultados = 0;
                 }


                // log::info($fechaInicio);
                // log::info($fechaFin);

                
            // Devolver los resultados como JSON
            return response()->json([
                'success' => true,
                'resultados' => $resultados ,
            ]);



        } catch (\Exception $e) {
            // Log de error
            Log::error('Error al procesar la solicitud de consumos: ' . $e->getMessage());

            // Respuesta de error
            return response()->json(['error' => 'Se ha producido un error. Por favor, inténtelo de nuevo más tarde.'], 500);
        }
    }



// Función para obtener los conteos de una sucursal
// public function obtenerConteosPorSucursal($conexion, $idSucursal) 
// {

//     $fechaMesAnterior = now()->subMonth();

//     $conteos = $conexion
//                  ->table('solicitud')
//                  ->selectRaw('
//                      COUNT(CASE WHEN DATE(fecha) = CURDATE() THEN 1 END) AS conteoPorDia,
//                      COUNT(CASE WHEN YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE()) THEN 1 END) AS conteoPorMes,
//                      COUNT(CASE WHEN YEAR(fecha) = YEAR(CURDATE()) THEN 1 END) AS conteoPorAnio,
//                      COUNT(CASE WHEN YEAR(fecha) = ? AND MONTH(fecha) = ? THEN 1 END) AS conteoMesAnterior,
//                      MAX(updated_at) AS lastUpdate
//                  ', [$fechaMesAnterior->year, $fechaMesAnterior->month])
//                  ->where('sucursal', $idSucursal)
//                  ->first();

//     return $conteos;
// }



    public function mostrarCFDI(Request $request, $id)
    {
        try {
            // Obtener todos los laboratorios
            $laboratorio = Laboratorio::findorfail($id);

            $IDcfdi=$request->input('CFDIid');

            // Array para almacenar datos
            $sucursal = [];

            // Configurar una conexión externa general
            $this->gestorBD->conectarBDyEditar($id);

    
                try {
                    // Configurar conexión externa dinámicamente
                    $connectionName = 'externo_' . $laboratorio->id;

                    // Obtener conexión del gestor de bases de data
                    $conexion = $this->gestorBD->obtenerConexion($connectionName);

                    // Obtener data del laboratorio usando la conexión externa
                    $sucursal = $conexion
                        ->table('sucursales as s')
                        ->leftJoin('cfdi_parametros as c', 's.idSucursal', '=', 'c.CFDISUCURSAL')
                        ->select(
                            'c.CFDISUCURSAL',
                            'c.id as CFDIid',
                            'c.VETERINARIA',
                            'c.TIMESESION',
                            'c.SUSCRIPCION',
                            'c.SESIONESLIMITE',
                            'c.VERSION',
                            'c.flag_sucursales',
                            's.descripcion as descripcion_sucursal',
                            's.idSucursal'
                        )
                        ->where('c.id', $IDcfdi)
                        ->whereNotNull('c.CFDISUCURSAL')
                        ->get();

                        


                    // Transformar y agregar data al array $data

                } catch (\Exception $e) {
                    // Loggear error al obtener data del laboratorio
                    Log::error('Error al obtener data del laboratorio ' . $laboratorio->id . ': ' . $e->getMessage());
                    // Puedes optar por continuar con el siguiente laboratorio en caso de error
                   
                }
            

            // Devolver los datos para DataTables
            return response()->json([
                'success' => true,
                // 'CFDISUCURSAL' => $sucursal->pluck('CFDISUCURSAL'), 
                'CFDIid' => $sucursal->pluck('CFDIid'),
                'idSucursal' => $sucursal->pluck('idSucursal'),
                'sucursal' => $sucursal->pluck('descripcion_sucursal'),
                'mesmax' => $sucursal->pluck('flag_sucursales'),
                'version' => $sucursal->pluck('VERSION'),
                'sesionlimite' => $sucursal->pluck('SESIONESLIMITE'),
                'timesession' => $sucursal->pluck('TIMESESION'),

            ]);

        } catch (\Exception $e) {
            // Manejo de errores generales
            Log::error('Error al procesar la solicitud de laboratorios: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => 'Se ha producido un error. Por favor, inténtelo de nuevo más tarde.'], 500);
            }
        }
    }




        public function exportPDF(Request $request)
    {
        try {
            // Obtener los datos filtrados una sola vez usando el método optimizado
            $datos = $this->filterCFDI($request);

            // Renderizar la vista de PDF utilizando los datos obtenidos
            $html = view('laboratorios.laboratorioPDF', [
                'laboratorios' => $datos['laboratoriosData'],
                'sucursales' => $datos['sucursales'],
                'cfdis' => $datos['cfdis'],
                'conteosPorCFDi' => $datos['conteosPorCFDi'],
                'fecha' => $datos['fecha'],
            ])->render();

            // Configurar y renderizar el PDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();


            $usuario = $request->input('usuario');

            // Llama a Bitacorastore para registrar la acción en la bitácora
            $this->Bitacorastore(null, [
                'usuario' => $usuario, 
                'accion' => 'Generación de PDF de laboratorio',
            ]);

            // Retornar el PDF generado para ser descargado
            return $dompdf->stream('laboratorioPDF.pdf', ['Attachment' => false]);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que ocurra durante la generación del PDF
            return response()->json(['error' => 'Error al exportar el PDF: ' . $e->getMessage()]);
        }
    }



    // public function filterCFDI(Request $request)
    // {
    //     $errores = [];
    //     $laboratoriosData = [];
    //     $sucursales = [];
    //     $cfdis = [];
    //     $conteosPorCFDi = [];

    //     try {
    //         $laboratorios = Laboratorio::all();
    //         $this->gestorBD->conectarBD();

    //         // Fechas
    //         $fechaActual = Carbon::now();
    //         $primerDiaDelMesActual = $fechaActual->copy()->startOfMonth();
    //         $ultimoDiaDelMesActual = $fechaActual->copy()->endOfMonth();
    //         $primerDiaDelAnioActual = $fechaActual->copy()->startOfYear();
    //         $ultimoDiaDelAnioActual = $fechaActual->copy()->endOfYear();
    //         $ultimoDiaDelMesAnterior = $primerDiaDelMesActual->copy()->subDay();
    //         $primerDiaDelMesAnterior = $ultimoDiaDelMesAnterior->copy()->startOfMonth();

    //         // Formatea las fechas para SQL
    //         $fechaHoy = $fechaActual->toDateString();
    //         $primerDiaMesActual = $primerDiaDelMesActual->toDateString();
    //         $ultimoDiaMesActual = $ultimoDiaDelMesActual->toDateString();
    //         $primerDiaAnioActual = $primerDiaDelAnioActual->toDateString();
    //         $ultimoDiaAnioActual = $ultimoDiaDelAnioActual->toDateString();
    //         $primerDiaMesAnterior = $primerDiaDelMesAnterior->toDateString();
    //         $ultimoDiaMesAnterior = $ultimoDiaDelMesAnterior->toDateString();

    //         foreach ($laboratorios as $laboratorio) {
    //             try {
    //                 $connectionName = 'externo_' . $laboratorio->id;
    //                 $conexion = $this->gestorBD->obtenerConexion($connectionName);

    //                 // Consultar datos de sucursales
    //                 $sucursalesData = $conexion
    //                     ->table('sucursales')
    //                     ->select('idSucursal', 'descripcion')
    //                     ->get()
    //                     ->keyBy('idSucursal')
    //                     ->toArray();

    //                 $sucursales[$laboratorio->id] = $sucursalesData;

    //                 // Consultar datos de CFDis
    //                 $cfdis[$laboratorio->id] = $conexion
    //                     ->table('cfdi_parametros as c')
    //                     ->leftJoin('sucursales as s', 'c.CFDISUCURSAL', '=', 's.idSucursal')
    //                     ->select(
    //                         'c.CFDISUCURSAL',
    //                         'c.id',
    //                         'c.VETERINARIA',
    //                         'c.TIMESESION',
    //                         'c.SUSCRIPCION',
    //                         'c.SESIONESLIMITE',
    //                         'c.VERSION',
    //                         'c.flag_sucursales',
    //                         's.descripcion as descripcion_sucursal'
    //                     )
    //                     ->whereNotNull('s.idSucursal') // Filtrar solo registros con sucursal válida
    //                     ->get();

    //                 // Consultar conteos por CFDI
    //                 foreach ($cfdis[$laboratorio->id] as $cfdi) {
    //                     $IDsucursal = $cfdi->CFDISUCURSAL;

    //                     $conteos = $conexion
    //                         ->table('solicitud AS s')
    //                         ->selectRaw('
    //                             (SELECT MAX(updated_at) 
    //                             FROM solicitud 
    //                             WHERE sucursal = s.idSucursal) AS lastUpdate,
                                
    //                             (SELECT COUNT(*) 
    //                             FROM solicitud 
    //                             WHERE sucursal = s.idSucursal
    //                             AND fecha BETWEEN ? AND ?) AS conteoPorDia,
                        
    //                             (SELECT COUNT(*) 
    //                             FROM solicitud 
    //                             WHERE sucursal = s.idSucursal
    //                             AND fecha BETWEEN ? AND ?) AS conteoPorMes,
                        
    //                             (SELECT COUNT(*) 
    //                             FROM solicitud 
    //                             WHERE sucursal = s.idSucursal
    //                             AND fecha BETWEEN ? AND ?) AS conteoPorAnio,
                        
    //                             (SELECT COUNT(*) 
    //                             FROM solicitud 
    //                             WHERE sucursal = s.idSucursal
    //                             AND fecha BETWEEN ? AND ?) AS conteoMesAnterior',
    //                             [
    //                                 "$fechaHoy 00:00:00", "$fechaHoy 23:59:59",
    //                                 "$primerDiaMesActual 00:00:00", "$ultimoDiaMesActual 23:59:59",
    //                                 "$primerDiaAnioActual 00:00:00", "$ultimoDiaAnioActual 23:59:59",
    //                                 "$primerDiaMesAnterior 00:00:00", "$ultimoDiaMesAnterior 23:59:59"
    //                             ]
    //                         )
    //                         ->where('sucursal', $IDsucursal)
    //                         ->first();

    //                     if ($conteos) {
    //                         $conteosPorCFDi[$laboratorio->id][$cfdi->id] = [
    //                             'conteoPorDia' => $conteos->conteoPorDia ?? 0,
    //                             'conteoPorMes' => $conteos->conteoPorMes ?? 0,
    //                             'conteoPorAnio' => $conteos->conteoPorAnio ?? 0,
    //                             'conteoMesAnterior' => $conteos->conteoMesAnterior ?? 0,
    //                             'lastUpdate' => $conteos->lastUpdate ?? null,
    //                         ];
    //                     }
    //                 }

    //                 // Filtrar CFDis válidos
    //                 $cfdisValidos = [];

    //                 foreach ($conteosPorCFDi[$laboratorio->id] as $cfdiId => $conteo) {
    //                     $cfdi = $cfdis[$laboratorio->id]->firstWhere('id', $cfdiId);

    //                     if ($cfdi && $conteo['conteoMesAnterior'] > $cfdi->flag_sucursales) {
    //                         $cfdisValidos[] = $cfdi;
    //                     }
    //                 }

    //                 if (!empty($cfdisValidos)) {
    //                     $laboratoriosData[] = $laboratorio;
    //                     $cfdis[$laboratorio->id] = $cfdisValidos;
    //                 }

    //             } catch (\Exception $e) {
    //                 Log::error('Error al procesar laboratorio ' . $laboratorio->id . ': ' . $e->getMessage());
    //                 $errores[] = 'Error en laboratorio ' . $laboratorio->nombre . ': ' . $e->getMessage();
    //             }
    //         }

    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'laboratoriosData' => $laboratoriosData,
    //                 'sucursales' => $sucursales,
    //                 'cfdis' => $cfdis,
    //                 'conteosPorCFDi' => $conteosPorCFDi,
    //                 'errores' => $errores,
    //             ]);
    //         }

    //         return view('laboratorios.filtro', [
    //             'laboratoriosData' => $laboratoriosData,
    //             'sucursales' => $sucursales,
    //             'cfdis' => $cfdis,
    //             'conteosPorCFDi' => $conteosPorCFDi,
    //             'errores' => $errores,
    //             'fecha' => Carbon::now()->format('d/m/Y H:i:s'),
    //         ]);
            
    //     } catch (\Exception $e) {
    //         Log::error('Error al obtener los laboratorios: ' . $e->getMessage());
    //         return view('laboratorios.filtro', [
    //             'laboratoriosData' => [],
    //             'sucursales' => [],
    //             'cfdis' => [],
    //             'conteosPorCFDi' => [],
    //             'errores' => ['Error al obtener los laboratorios: ' . $e->getMessage()],
    //         ]);
    //     }
    // }

    

    public function filterCFDI(Request $request)
    {
        $errores = [];
        $laboratoriosData = [];
        $sucursales = [];
        $cfdis = [];
        $conteosPorCFDi = [];

        try {
            // Obtener todos los laboratorios
            $laboratorios = Laboratorio::all();
            $this->gestorBD->conectarBD();

            // Fechas
            $fechaActual = Carbon::now();
            $primerDiaDelMesActual = $fechaActual->copy()->startOfMonth();
            $ultimoDiaDelMesActual = $fechaActual->copy()->endOfMonth();
            $primerDiaDelAnioActual = $fechaActual->copy()->startOfYear();
            $ultimoDiaDelAnioActual = $fechaActual->copy()->endOfYear();
            $ultimoDiaDelMesAnterior = $primerDiaDelMesActual->copy()->subDay();
            $primerDiaDelMesAnterior = $ultimoDiaDelMesAnterior->copy()->startOfMonth();

            // Formatear fechas para SQL
            $fechaHoy = $fechaActual->toDateString();
            $primerDiaMesActual = $primerDiaDelMesActual->toDateString();
            $ultimoDiaMesActual = $ultimoDiaDelMesActual->toDateString();
            $primerDiaAnioActual = $primerDiaDelAnioActual->toDateString();
            $ultimoDiaAnioActual = $ultimoDiaDelAnioActual->toDateString();
            $primerDiaMesAnterior = $primerDiaDelMesAnterior->toDateString();
            $ultimoDiaMesAnterior = $ultimoDiaDelMesAnterior->toDateString();

            foreach ($laboratorios as $laboratorio) {
                try {
                    // Obtener conexión para el laboratorio
                    $connectionName = 'externo_' . $laboratorio->id;
                    $conexion = $this->gestorBD->obtenerConexion($connectionName);

                    // Consultar datos de sucursales
                    $sucursalesData = $conexion
                        ->table('sucursales')
                        ->select('idSucursal', 'descripcion')
                        ->get()
                        ->keyBy('idSucursal')
                        ->toArray();

                    $sucursales[$laboratorio->id] = $sucursalesData;

                    // Consultar datos de CFDis
                    $cfdis[$laboratorio->id] = $conexion
                        ->table('cfdi_parametros as c')
                        ->leftJoin('sucursales as s', 'c.CFDISUCURSAL', '=', 's.idSucursal')
                        ->select(
                            'c.CFDISUCURSAL',
                            'c.id',
                            'c.VETERINARIA',
                            'c.TIMESESION',
                            'c.SUSCRIPCION',
                            'c.SESIONESLIMITE',
                            'c.VERSION',
                            'c.flag_sucursales',
                            's.descripcion as descripcion_sucursal'
                        )
                        ->whereNotNull('s.idSucursal') // Filtrar solo registros con sucursal válida
                        ->get();

                    // Consultar conteos por CFDI
                    foreach ($cfdis[$laboratorio->id] as $cfdi) {
                        $IDsucursal = $cfdi->CFDISUCURSAL;

                        $conteos = $conexion
                        ->table('solicitud AS s')
                        ->selectRaw('
                            MAX(updated_at) AS lastUpdate,
                            COUNT(CASE WHEN fecha BETWEEN ? AND ? AND (cotizacion != 1 OR cotizacion IS NULL OR cotizacion = "") THEN 1 END) AS conteoPorDia,
                            COUNT(CASE WHEN fecha BETWEEN ? AND ? AND (cotizacion != 1 OR cotizacion IS NULL OR cotizacion = "") THEN 1 END) AS conteoPorMes,
                            COUNT(CASE WHEN fecha BETWEEN ? AND ? AND (cotizacion != 1 OR cotizacion IS NULL OR cotizacion = "") THEN 1 END) AS conteoPorAnio,
                            COUNT(CASE WHEN fecha BETWEEN ? AND ? AND (cotizacion != 1 OR cotizacion IS NULL OR cotizacion = "") THEN 1 END) AS conteoMesAnterior
                        ', [
                            $fechaHoy . ' 00:00:00', $fechaHoy . ' 23:59:59', // Conteo por día
                            $primerDiaMesActual . ' 00:00:00', $ultimoDiaMesActual . ' 23:59:59', // Conteo por mes
                            $primerDiaAnioActual . ' 00:00:00', $ultimoDiaAnioActual . ' 23:59:59', // Conteo por año
                            $primerDiaMesAnterior . ' 00:00:00', $ultimoDiaMesAnterior . ' 23:59:59' // Conteo del mes anterior
                        ])
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
                        }
                    }

                    // Filtrar CFDis válidos
                    $cfdisValidos = [];

                    foreach ($conteosPorCFDi[$laboratorio->id] as $cfdiId => $conteo) {
                        $cfdi = $cfdis[$laboratorio->id]->firstWhere('id', $cfdiId);

                        // Validar si el conteo del mes anterior es mayor que el flag de sucursales
                        if ($cfdi && $conteo['conteoMesAnterior'] > $cfdi->flag_sucursales) {
                            $cfdisValidos[] = $cfdi;
                        }
                    }

                    if (!empty($cfdisValidos)) {
                        $laboratoriosData[] = $laboratorio;
                        $cfdis[$laboratorio->id] = $cfdisValidos;
                    }

                } catch (\Exception $e) {
                    Log::error('Error al procesar laboratorio ' . $laboratorio->id . ': ' . $e->getMessage());
                    $errores[] = 'Error en laboratorio ' . $laboratorio->nombre . ': ' . $e->getMessage();
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'laboratoriosData' => $laboratoriosData,
                    'sucursales' => $sucursales,
                    'cfdis' => $cfdis,
                    'conteosPorCFDi' => $conteosPorCFDi,
                    'errores' => $errores,
                ]);
            }

            return view('laboratorios.filtro', [
                'laboratoriosData' => $laboratoriosData,
                'sucursales' => $sucursales,
                'cfdis' => $cfdis,
                'conteosPorCFDi' => $conteosPorCFDi,
                'errores' => $errores,
                'fecha' => Carbon::now()->format('d/m/Y H:i:s'),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener los laboratorios: ' . $e->getMessage());
            return view('laboratorios.filtro', [
                'laboratoriosData' => [],
                'sucursales' => [],
                'cfdis' => [],
                'conteosPorCFDi' => [],
                'errores' => ['Error al obtener los laboratorios: ' . $e->getMessage()],
            ]);
        }
    }


    
    public function Labsinuso(Request $request)
    {
        try {
            // Definir fechas usando Carbon
            $fechaActual = Carbon::now();
            $primerDiaDelMesActual = $fechaActual->copy()->startOfMonth();
            $ultimoDiaDelMesActual = $fechaActual->copy()->endOfMonth();
            $primerDiaDelAnioActual = $fechaActual->copy()->startOfYear();
            $ultimoDiaDelAnioActual = $fechaActual->copy()->endOfYear();
            $ultimoDiaDelMesAnterior = $primerDiaDelMesActual->copy()->subDay();
            $primerDiaDelMesAnterior = $ultimoDiaDelMesAnterior->copy()->startOfMonth();

            $fechaHoy = $fechaActual->toDateString();
            $primerDiaMesActual = $primerDiaDelMesActual->toDateString();
            $ultimoDiaMesActual = $ultimoDiaDelMesActual->toDateString();
            $primerDiaAnioActual = $primerDiaDelAnioActual->toDateString();
            $ultimoDiaAnioActual = $ultimoDiaDelAnioActual->toDateString();
            $primerDiaMesAnterior = $primerDiaDelMesAnterior->toDateString();
            $ultimoDiaMesAnterior = $ultimoDiaDelMesAnterior->toDateString();

            $laboratorios = Laboratorio::all();
            $errores = [];
            $laboratoriosData = [];
            $sucursales = [];
            $cfdis = [];
            $conteosPorCFDi = [];

            $this->gestorBD->conectarBD();

            foreach ($laboratorios as $laboratorio) {
                try {
                    $connectionName = 'externo_' . $laboratorio->id;
                    $conexion = $this->gestorBD->obtenerConexion($connectionName);

                    // Consultar datos de sucursales
                    $sucursalesData = $conexion
                        ->table('sucursales')
                        ->select('idSucursal', 'descripcion')
                        ->get()
                        ->keyBy('idSucursal')
                        ->toArray();

                    $sucursales[$laboratorio->id] = $sucursalesData;

                    // Consultar datos de CFDis
                    $cfdis[$laboratorio->id] = $conexion
                        ->table('cfdi_parametros as c')
                        ->leftJoin('sucursales as s', 'c.CFDISUCURSAL', '=', 's.idSucursal')
                        ->select(
                            'c.CFDISUCURSAL',
                            'c.id',
                            'c.VETERINARIA',
                            'c.TIMESESION',
                            'c.SUSCRIPCION',
                            'c.SESIONESLIMITE',
                            'c.VERSION',
                            'c.flag_sucursales',
                            's.descripcion as descripcion_sucursal'
                        )
                        ->whereNotNull('s.idSucursal') // Filtrar solo registros con sucursal válida
                        ->get();

                    // Consultar conteos por CFDI
                    foreach ($cfdis[$laboratorio->id] as $cfdi) {
                        $IDsucursal = $cfdi->CFDISUCURSAL;

                        $conteos = $conexion
                            ->table('solicitud')
                            ->selectRaw('
                                COUNT(CASE WHEN DATE(fecha) = ? THEN 1 END) AS conteoPorDia,
                                COUNT(CASE WHEN fecha BETWEEN ? AND ? THEN 1 END) AS conteoPorMes,
                                COUNT(CASE WHEN fecha BETWEEN ? AND ? THEN 1 END) AS conteoPorAnio,
                                COUNT(CASE WHEN fecha BETWEEN ? AND ? THEN 1 END) AS conteoMesAnterior,
                                MAX(updated_at) AS lastUpdate
                            ', [
                                $fechaHoy, // Conteo por día
                                $primerDiaMesActual . " 00:00:00", $ultimoDiaMesActual . " 23:59:59", // Conteo por mes
                                $primerDiaAnioActual . " 00:00:00", $ultimoDiaAnioActual . " 23:59:59", // Conteo por año
                                $primerDiaMesAnterior . " 00:00:00", $ultimoDiaMesAnterior . " 23:59:59" // Conteo del mes anterior
                            ])
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
                        }
                    }

                    // Filtrar CFDis válidos y almacenarlos
                    $cfdisValidos = [];

                    foreach ($conteosPorCFDi[$laboratorio->id] as $cfdiId => $conteo) {
                        $cfdi = $cfdis[$laboratorio->id]->firstWhere('id', $cfdiId);

                        if ($cfdi && $conteo['conteoPorDia'] <= 0) {
                            $cfdisValidos[] = $cfdi;
                        }
                    }

                    if (!empty($cfdisValidos)) {
                        $laboratoriosData[] = $laboratorio;
                        $cfdis[$laboratorio->id] = $cfdisValidos;
                    }

                } catch (\Exception $e) {
                    Log::error('Error al procesar laboratorio ' . $laboratorio->id . ': ' . $e->getMessage());
                    $errores[] = 'Error en laboratorio ' . $laboratorio->nombre . ': ' . $e->getMessage();
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'laboratoriosData' => $laboratoriosData,
                    'sucursales' => $sucursales,
                    'cfdis' => $cfdis,
                    'conteosPorCFDi' => $conteosPorCFDi,
                    'errores' => $errores,
                ]);
            }

            return view('laboratorios.labsinuso', [
                'laboratoriosData' => $laboratoriosData,
                'sucursales' => $sucursales,
                'cfdis' => $cfdis,
                'conteosPorCFDi' => $conteosPorCFDi,
                'errores' => $errores,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener los laboratorios: ' . $e->getMessage());
            return view('laboratorios.labsinuso', [
                'laboratoriosData' => [],
                'sucursales' => [],
                'cfdis' => [],
                'conteosPorCFDi' => [],
                'errores' => ['Error al obtener los laboratorios: ' . $e->getMessage()],
            ]);
        }
    }


    public function Cfdiversion(Request $request)
    {
        try {
            // Tiempo de inicio total
            $startTime = microtime(true);

            // Log::info('Inicio del método index');


            

            $laboratorios = Laboratorio::all();
            
            // Log::info('Laboratorios obtenidos: ', ['count' => $laboratorios->count()]);

            $errores = [];
            $sucursales = [];
            $cfdis = [];
            
            $connections = [];

            $this->gestorBD->conectarBD();
            


            foreach ($laboratorios as $laboratorio) {
                try {

                    $connectionName = 'externo_' . $laboratorio->id;
                    $conexion = $this->gestorBD->obtenerConexion($connectionName);

                        // Obtener y almacenar datos de sucursales
                        $sucursalesData = $conexion
                            ->table('sucursales as s')
                            ->select('idSucursal', 'descripcion')
                            ->get()
                            ->keyBy('idSucursal')
                            ->toArray();

                        $sucursales[$laboratorio->id] = $sucursalesData;

                        // Realizar la consulta para obtener los datos de cfdi_parametros y la descripción de la sucursal
                        $cfdis[$laboratorio->id] = $conexion
                            ->table('cfdi_parametros as c')
                            ->leftJoin('sucursales as s', 'c.CFDISUCURSAL', '=', 's.idSucursal')
                            ->select(
                                'c.CFDISUCURSAL',
                                'c.id',
                                'c.VERSION',
                                's.descripcion as descripcion_sucursal'
                            )
                            ->whereNotNull('s.idSucursal') // Filtrar solo registros con sucursal válida
                            ->get();

                } catch (\Exception $e) {
                    Log::error('Error al procesar laboratorio ' . $laboratorio->id . ': ' . $e->getMessage());
                    $errores[] = 'Error en laboratorio ' . $laboratorio->nombre . ': ' . $e->getMessage();
                }
            }

            $endTime = microtime(true);
            // Log::info('Tiempo total de ejecución: ' . ($endTime - $startTime) . ' segundos');

            if ($request->ajax()) {
                return response()->json([
                    'laboratorios' => $laboratorios,
                    'sucursales' => $sucursales,
                    'cfdis' => $cfdis,
                    
                    'errores' => $errores,
                ]);
            }

            return view('laboratorios.version', [
                'laboratorios' => $laboratorios,
                
                'sucursales' => $sucursales,
                'cfdis' => $cfdis,
                
                'errores' => $errores,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al procesar la solicitud de laboratorios: ' . $e->getMessage());
            return response()->json(['error' => 'Se ha producido un error. Por favor, inténtelo de nuevo más tarde.'], 500);
        }
    }


    public function Cfdisuscripcion(Request $request)
    {
        try {
            // Tiempo de inicio total
            // $startTime = microtime(true);

            // Log::info('Inicio del método index');
            $laboratorios = Laboratorio::all();
            
            // Log::info('Laboratorios obtenidos: ', ['count' => $laboratorios->count()]);

            $errores = [];
            $sucursales = [];
            $cfdis = [];
            
            $connections = [];


            $this->gestorBD->conectarBD();


            foreach ($laboratorios as $laboratorio) {
                try {
                    
 
                        $connectionName = 'externo_' . $laboratorio->id;
                        $conexion = $this->gestorBD->obtenerConexion($connectionName);

                        // Obtener y almacenar datos de sucursales
                        $sucursalesData = $conexion
                            ->table('sucursales as s')
                            ->select('idSucursal', 'descripcion')
                            ->get()
                            ->keyBy('idSucursal')
                            ->toArray();

                        $sucursales[$laboratorio->id] = $sucursalesData;

                        // Realizar la consulta para obtener los datos de cfdi_parametros y la descripción de la sucursal
                        $cfdis[$laboratorio->id] = $conexion
                            ->table('cfdi_parametros as c')
                            ->leftJoin('sucursales as s', 'c.CFDISUCURSAL', '=', 's.idSucursal')
                            ->select(
                                'c.CFDISUCURSAL',
                                'c.id',
                                // 'c.VERSION',
                                // 'c.VETERINARIA',
                                'c.SUSCRIPCION',
                                's.descripcion as descripcion_sucursal'
                            )
                            ->whereNotNull('s.idSucursal') // Filtrar solo registros con sucursal válida
                            ->get();

                } catch (\Exception $e) {
                    Log::error('Error al procesar laboratorio ' . $laboratorio->id . ': ' . $e->getMessage());
                    $errores[] = 'Error en laboratorio ' . $laboratorio->nombre . ': ' . $e->getMessage();
                }
            }

            // $endTime = microtime(true);
            // Log::info('Tiempo total de ejecución: ' . ($endTime - $startTime) . ' segundos');

            if ($request->ajax()) {
                return response()->json([
                    'laboratorios' => $laboratorios,
                    'sucursales' => $sucursales,
                    'cfdis' => $cfdis,
                    
                    'errores' => $errores,
                ]);
            }

            return view('laboratorios.suscripcion', [
                'laboratorios' => $laboratorios,
                
                'sucursales' => $sucursales,
                'cfdis' => $cfdis,
                
                'errores' => $errores,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al procesar la solicitud de laboratorios: ' . $e->getMessage());
            return response()->json(['error' => 'Se ha producido un error. Por favor, inténtelo de nuevo más tarde.'], 500);
        }
    }


    public function Cfdiveterinaria(Request $request)
    {
        try {
            // Tiempo de inicio total
            // $startTime = microtime(true);

            // Log::info('Inicio del método index');
            $laboratorios = Laboratorio::all();
            
            // Log::info('Laboratorios obtenidos: ', ['count' => $laboratorios->count()]);

            $errores = [];
            $sucursales = [];
            $cfdis = [];
            
            $connections = [];

            $this->gestorBD->conectarBD();

            foreach ($laboratorios as $laboratorio) {
                try {

                    $connectionName = 'externo_' . $laboratorio->id;
                    $conexion = $this->gestorBD->obtenerConexion($connectionName);

                        // Obtener y almacenar datos de sucursales
                        $sucursalesData = $conexion
                            ->table('sucursales as s')
                            ->select('idSucursal', 'descripcion')
                            ->get()
                            ->keyBy('idSucursal')
                            ->toArray();

                        $sucursales[$laboratorio->id] = $sucursalesData;

                        // Realizar la consulta para obtener los datos de cfdi_parametros y la descripción de la sucursal
                        $cfdis[$laboratorio->id] = $conexion
                        ->table('cfdi_parametros as c')
                        ->leftJoin('sucursales as s', 'c.CFDISUCURSAL', '=', 's.idSucursal')
                        ->select(
                            'c.CFDISUCURSAL',
                            'c.id',
                            'c.VETERINARIA',
                            's.descripcion as descripcion_sucursal'
                        )
                        ->whereNotNull('s.idSucursal') // Filtrar solo registros con sucursal válida
                        // ->where('c.VETERINARIA', 1) // Filtrar registros donde VETERINARIA sea igual a 1
                        ->where('c.VETERINARIA', 1)
                        ->get();

                } catch (\Exception $e) {
                    Log::error('Error al procesar laboratorio ' . $laboratorio->id . ': ' . $e->getMessage());
                    $errores[] = 'Error en laboratorio ' . $laboratorio->nombre . ': ' . $e->getMessage();
                }
            }

         


            // $endTime = microtime(true);
         

            if ($request->ajax()) {
                return response()->json([
                    'laboratorios' => $laboratorios,
                    'sucursales' => $sucursales,
                    'cfdis' => $cfdis,
                    'errores' => $errores,
                ]);
            }

            return view('laboratorios.veterinaria', [
                'laboratorios' => $laboratorios,
                'sucursales' => $sucursales,
                'cfdis' => $cfdis,
                'errores' => $errores,
          
            ]);
        } catch (\Exception $e) {
            Log::error('Error al procesar la solicitud de laboratorios: ' . $e->getMessage());
            return response()->json(['error' => 'Se ha producido un error. Por favor, inténtelo de nuevo más tarde.'], 500);
        }
    }







            public function update(Request $request, $id)
            {


                // $id= $request->input('idlaboratorio');
                
                // Obtener el laboratorio existente
                $laboratorio = Laboratorio::findOrFail($id);

                // Guardar los valores actuales antes de la actualización
                $nombreAnterior = $laboratorio->nombre;
                $notasAnteriores = $laboratorio->notas;

                // Actualizar los campos del laboratorio con los nuevos valores
                $laboratorio->nombre = $request->input('editNombre');
                $laboratorio->notas = $request->input('editNotas');
                $laboratorio->save();

                // Obtener los valores actualizados después de la actualización
                $nombreNuevo = $laboratorio->nombre;
                $notasNuevas = $laboratorio->notas;

                // Comparar los valores para determinar los cambios realizados
                $cambios = [];
                if ($nombreAnterior !== $nombreNuevo) {
                    $cambios[] = 'Nombre de "' . $nombreAnterior . '" a "' . $nombreNuevo . '"';
                }
                if ($notasAnteriores !== $notasNuevas) {
                    $cambios[] = 'Notas actualizadas';
                }

                // Si no hubo cambios
                if (empty($cambios)) {
                    
                    $usuario = $request->input('usuario');
                    $this->Bitacorastore($laboratorio->id, [
                        'usuario' => $usuario,
                        'accion' => 'Se acciono el formulario para editar laboratorio pero no se realizaron cambios en el laboratorio',
                    ]);
                } else {
                    // Llama a Bitacorastore para registrar la acción en la bitácora
                    $usuario = $request->input('usuario');
                    $this->Bitacorastore($laboratorio->id, [
                        'usuario' => $usuario,
                        'accion' => 'Actualización de laboratorio: ' . implode(', ', $cambios),
                    ]);
                }


            if ($request->ajax()) {
                // Actualizar el registro en la base de datos
                return response()->json(['success' => true]);
            }
                // Redireccionar a la vista principal de laboratorios
                return redirect()->route('laboratorio.index');
        }



    
    public function Bitacorastore($id, $data)
    {
        $bitacora = new Bitacora;
        $bitacora->usuario = $data['usuario'];
        $bitacora->accion = $data['accion'];        
        $bitacora->save();
        
        // Log::info('Registro en bitácora guardado: ', [$bitacora]);
        
        return redirect()->route('laboratorio.index');
    }
    

    public function Bitacoraindex()
        {
            $bitacoras = Bitacora::orderBy('created_at', 'desc')->get();

            // Retornar la vista 'bitacora' con las bitácoras ordenadas
            return view('laboratorios.bitacora', [
                'bitacoras' => $bitacoras,
            ]);
        }






    public function configsave(Request $request, $id)
    {
        // Obtener el laboratorio
        $laboratorio = Laboratorio::findOrFail($id);

        // Guardar valores actuales antes de la actualización
        $anterior = [
            'servidor_sql' => $laboratorio->bases_de_datos->servidor_sql,
            'base_de_datos' => $laboratorio->bases_de_datos->base_de_datos,
            'usuario_sql' => $laboratorio->bases_de_datos->usuario_sql,
            'password_sql' => $laboratorio->bases_de_datos->password_sql,
        ];

        // Decodificar los datos JSON recibidos
        $formData = json_decode($request->getContent(), true);

        // Actualizar las credenciales
        $laboratorio->bases_de_datos->servidor_sql = $formData['servidor_sql'] ?? $anterior['servidor_sql'];
        $laboratorio->bases_de_datos->base_de_datos = $formData['base_de_datos'] ?? $anterior['base_de_datos'];
        $laboratorio->bases_de_datos->usuario_sql = $formData['usuario_sql'] ?? $anterior['usuario_sql'];
        $laboratorio->bases_de_datos->password_sql = $formData['password_sql'] ?? $anterior['password_sql'];
        $laboratorio->bases_de_datos->save();

        // Guardar valores nuevos después de la actualización
        $nuevo = [
            'servidor_sql' => $laboratorio->bases_de_datos->servidor_sql,
            'base_de_datos' => $laboratorio->bases_de_datos->base_de_datos,
            'usuario_sql' => $laboratorio->bases_de_datos->usuario_sql,
            'password_sql' => $laboratorio->bases_de_datos->password_sql,
        ];

        // Comparar cada valor y generar mensajes de cambio
        $cambios = [];
        if ($anterior['servidor_sql'] !== $nuevo['servidor_sql']) {
            $cambios[] = '|servidor SQL de: "' . $anterior['servidor_sql'] . '" a "' . $nuevo['servidor_sql'] . '" |' ;
        }
        if ($anterior['base_de_datos'] !== $nuevo['base_de_datos']) {
            $cambios[] = '|base de datos de: "' . $anterior['base_de_datos'] . '" a "' . $nuevo['base_de_datos'] . '" |';
        }
        if ($anterior['usuario_sql'] !== $nuevo['usuario_sql']) {
            $cambios[] = '|usuario SQL de: "' . $anterior['usuario_sql'] . '" a "' . $nuevo['usuario_sql'] . '" |';
        }
        if ($anterior['password_sql'] !== $nuevo['password_sql']) {
            $cambios[] = '|password SQL cambiado|';
        }

        $nombre = $formData['nombre'] ?? $laboratorio->nombre;

        // Si no hubo cambios
        if (empty($cambios)) {
            $mensaje = 'Se accionó el formulario para actualizar las credenciales de la base de datos pero no se realizaron cambios';
        } else {
            $nombre_bd = $anterior['base_de_datos'];
            // Loggear la actualización
            $mensaje = ' Se actualizaron las credenciales de la base de datos: "' . $nombre_bd .'": '. implode(', ', $cambios). ' Laboratorio: "'.$nombre .'"' ;
            // Log::info('Credenciales de base de datos actualizadas', [
            //     'id_laboratorio' => $laboratorio->id,
            //     'anterior' => $anterior,
            //     'nuevo' => $nuevo,
            // ]);
        }

        // Registrar en la bitácora
        $usuario = $formData['usuario'] ?? 'Desconocido';
        $this->Bitacorastore($laboratorio->id, [
            'usuario' => $usuario, 
            'accion' => $mensaje,
        ]);

        // Devolver una respuesta JSON
        return response()->json(['success' => true]);
    }



        public function borrarCFDI(Request $request, $id)
    {
        try {

            $id_laboratorio=$request->input('laboratorio_id');

            // Obtener el laboratorio correspondiente al ID proporcionado
            $laboratorio = Laboratorio::findOrFail($id_laboratorio);

            // Conectar a la base de datos externa y editar
            $this->gestorBD->conectarBDyBorrar($id_laboratorio);
            $connectionName = 'externo_' . $laboratorio->id;
            $conexion = $this->gestorBD->obtenerConexion($connectionName);

            $cfdiId = $id;

                // Realizar la actualización en la base de datos remota solo para el CFDI específico
               $cfdis = $conexion
                    ->table('cfdi_parametros')
                    ->where('id', $cfdiId)
                    ->delete();

                    // Log::info('CFDI , lineas afectadas: ', [$cfdis]);
                    // Log::info('CFDI id, lineas afectadas: ', [$cfdiId]);
                    // Log::info('laboratorio , lineas afectadas: ', [$laboratorio]);

                    // Registra los datos que estás tratando de alterar
                    // Log::info('Datos del CFDI que se está tratando de eliminar:', [
                    //     'id' => $cfdiId,
                    // ]);


                    // Registra la consulta y el resultado
                    // Log::info('Consulta para borrar el CFDI:', [
                    //     'consulta' => 'DELETE FROM cfdi_parametros WHERE id = ?',
                    //     'id' => $cfdiId,
                    //     'resultado' => $cfdis,
                    // ]);

                    // Registra los detalles del laboratorio
                    // Log::info('Detalles del laboratorio:', [
                    //     'id' => $laboratorio->id,
                    //     'nombre' => $laboratorio->nombre,
                    //     // Agrega más campos según sea necesario
                    // ]);

                    $usuario = $request->input('usuario');
                    $sucursal = $request->input('sucursal');
                    $nombreLab = $request->input('laboratorio_nombre');
                    $this->Bitacorastore($laboratorio->id, [
                        'usuario' => $usuario, // Utiliza el nuevo nombre actualizado
                        'accion' => 'Se ha eliminado el registro de cfdi_parametros del laboratorio: "'. $nombreLab . '"' ,
                        // 'accion' => 'Se ha eliminado la sucursal "' . $sucursal . '" y registro de cfdi_parametros del laboratorio: "'. $nombreLab . '"' ,
                    ]);


                if ($request->ajax()) {
                    // Si es una solicitud AJAX, devolver una respuesta JSON con un mensaje de éxito
                    return response()->json(['success' => true, 'message' => 'CFDI actualizado correctamente']);
                } else {
                    // Si no es una solicitud AJAX, redirigir a la página de laboratorios con un mensaje de éxito
                    // Log::info('CFDI , lineas afectadas: ', [$cfdis]);
                    // Log::info('CFDI id, lineas afectadas: ', [$cfdiId]);
                    // Log::info('laboratorio , lineas afectadas: ', [$laboratorio]);
                    // return redirect()->route('laboratorio.index')->with('success', 'CFDI actualizado correctamente');
                }
            
        } catch (\Exception $e) {
            // Registrar el error en los logs
            Log::error('Error al Borrar datos en la base de datos remota: ' . $e->getMessage());
            if ($request->ajax()) {
                // Si es una solicitud AJAX, devolver una respuesta JSON con un mensaje de error
                return response()->json(['success' => false, 'message' => 'Error al Borrar datos en la base de datos remota: ' . $e->getMessage()]);
            } else {
                // Si no es una solicitud AJAX, redirigir a la página de laboratorios con un mensaje de error
                return redirect()->route('laboratorio.index')->with('error', 'Error al Borrar datos en la base de datos remota: ' . $e->getMessage());
            }
        }
    }


    public function store(Request $request)
    {
        // Crear un nuevo laboratorio
        $laboratorio = new Laboratorio;
        $laboratorio->nombre = $request->input('nombre');
        $laboratorio->save();
    
        // Crear una nueva conexión y asociarla al laboratorio
        $base_de_datos = new Remoto();
        $base_de_datos->servidor_sql = $request->input('servidor_sql');
        $base_de_datos->base_de_datos = $request->input('base_de_datos');
        $base_de_datos->usuario_sql = $request->input('usuario_sql');
        $base_de_datos->password_sql = $request->input('password_sql');
        $base_de_datos->save();
    
        $laboratorio->base_de_datos_id = $base_de_datos->id;
        $laboratorio->save();
    
        // Agregar registros a la bitácora
        $usuario = $request->input('usuario');
        $this->Bitacorastore($laboratorio->id, [
            'usuario' => $usuario,
            'accion' => 'Se agregó un nuevo laboratorio: "' . $laboratorio->nombre . '" y una conexión a la base de datos: "' . $base_de_datos->servidor_sql . '"',
        ]);
    
        // Redirigir a una ruta específica después de guardar
        // return redirect()->route('laboratorio.index');
    }
    






    public function newconexion(Request $request)
    {
        // Crear un nuevo registro en la tabla "bases_de_datos"
        $base_de_datos = new Remoto();
        $base_de_datos->servidor_sql = $request->input('servidor_sql');
        $base_de_datos->base_de_datos = $request->input('base_de_datos');
        $base_de_datos->usuario_sql = $request->input('usuario_sql');
        $base_de_datos->password_sql = $request->input('password_sql');
        $base_de_datos->save();

        // Actualizar el campo "base_de_datos_id" en el modelo "Laboratorio" con el ID del nuevo registro de "bases_de_datos"
        $id = $request->input('id');
        $laboratorio = Laboratorio::findOrFail($id);
        $laboratorio->base_de_datos_id = $base_de_datos->id;
        $laboratorio->save();

        // Log::info('function newconexion: Actualizado base_de_datos', [$base_de_datos]);
        // Log::info('function newconexion: Actualizado base_de_datos', [$base_de_datos]);

        $usuario = $request->input('usuario');
        $this->Bitacorastore($laboratorio->id, [
               'usuario' => $usuario, // Utiliza el nuevo nombre actualizado
               'accion' => 'Se agrego una nueva conexion a base de datos: "'. $base_de_datos->servidor_sql. '"',
           ]);



        // Redirigir a una ruta específica después de guardar
        return redirect()->route('laboratorio.index');



    }



    public function destroy( Request $request, $id)
    {
        $laboratorio = Laboratorio::findOrFail($id);
        $laboratorio->delete();
    
        $BDid = $laboratorio->base_de_datos_id;
        $base_de_datos = Remoto::findOrFail($BDid);
        $base_de_datos->delete();


        $usuario = $request->input('usuario');
        $this->Bitacorastore($laboratorio->id, [
            'usuario' => $usuario, 
            'accion' => 'Se eliminó el laboratorio: "'. $laboratorio->nombre .'"',
        ]);
    
        return response()->json(['success' => true]);
    }
    
    





public function actualizarSuscripcion(Request $request)
{
    try {
        // Obtener datos del request
        $SUSCRIPCION = $request->input('SUSCRIPCION');
        $nombre = $request->input('nombre');
        $laboratorio_id = $request->input('laboratorio_id');
        $cfdi_id = $request->input('cfdi_id');
        $sucursal = $request->input('sucursal');
        $usuario = $request->input('usuario');

        // Log de datos recibidos
        // Log::info('Datos recibidos:');
        // Log::info('SUSCRIPCION: ' . $SUSCRIPCION);
        // Log::info('Laboratorio ID: ' . $laboratorio_id);
        // Log::info('CFDI ID: ' . $cfdi_id);
        // Log::info('Sucursal: ' . $sucursal);
        // Log::info('Usuario: ' . $usuario);

        // Validar ID del laboratorio
        if (!is_numeric($laboratorio_id) || $laboratorio_id <= 0) {
            Log::error('ID de laboratorio no válido: ' . $laboratorio_id);
            return response()->json(['success' => false, 'message' => 'ID de laboratorio no válido: ' . $laboratorio_id]);
        }

        // Obtener instancia del laboratorio
        $laboratorio = Laboratorio::findOrFail($laboratorio_id);

        $this->gestorBD->conectarBD();


        // Verificar conexión a base de datos remota
        $connectionName = 'externo_' . $laboratorio->id;
        $conexion = $this->gestorBD->obtenerConexion($connectionName);

            // Realizar actualización en la base de datos remota
            $affectedRows = $conexion
                ->table('cfdi_parametros')
                ->where('id', $cfdi_id)
                ->update([
                    'SUSCRIPCION' => $SUSCRIPCION,
                ]);

            // Log de la cantidad de filas afectadas
            // Log::info('Número de filas actualizadas en la base de datos remota: ' . $affectedRows);

            // Registrar acción en la bitácora
            $this->Bitacorastore($laboratorio->id, [
                'usuario' => $usuario,
                'accion' => 'Se ha "' . $SUSCRIPCION . '" la suscripción de la sucursal: "' . $sucursal . '" del laboratorio "' .$nombre. '"',
            ]);

            // Si es una solicitud AJAX, retornar respuesta JSON
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'CFDI actualizado correctamente']);
            } else {
                // Si no es AJAX, redirigir con mensaje de éxito
                return redirect()->route('laboratorio.index')->with('success', 'CFDI actualizado correctamente');
            }

    } catch (\Exception $e) {
        // Capturar y loggear errores
        Log::error('Error al actualizar datos en la base de datos remota: ' . $e->getMessage());
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar datos en la base de datos remota: ' . $e->getMessage()]);
        } else {
            return redirect()->route('laboratorio.index')->with('error', 'Error al actualizar datos en la base de datos remota: ' . $e->getMessage());
        }
    }
}



public function actualizarVeterinaria(Request $request)
{
    try {

        $veterinaria = $request->input('VETERINARIA');
        $nombre = $request->input('nombre');
        $laboratorio_id = $request->input('laboratorio_id');

        $laboratorio = Laboratorio::findOrFail($laboratorio_id);
        $cfdi_id = $request->input('cfdi_id');

        if (!is_numeric($laboratorio_id) || $laboratorio_id <= 0) {
            // Devolver una respuesta de error si el ID no es válido
            return response()->json(['success' => false, 'message' => 'ID de laboratorio no válido: ' .$laboratorio_id]);
        }

        // Log::info('laboratorio_id ', [$laboratorio_id]);
        // Log::info('laboratorio ', [$laboratorio]);

        // Verificar si el laboratorio tiene una base de datos remota asociada
        $this->gestorBD->conectarBD();


        // Verificar conexión a base de datos remota
        $connectionName = 'externo_' . $laboratorio->id;
        $conexion = $this->gestorBD->obtenerConexion($connectionName);





            // Obtener el ID del CFDI que se va a actualizar




            // Realizar la actualización en la base de datos remota solo para el CFDI específico
           $cfdis = $conexion
                ->table('cfdi_parametros')
                ->where('id', $cfdi_id)
                ->update([
                    'VETERINARIA' => $veterinaria,
                ]);

                $valordefinido = '';
            if ($veterinaria == 1){
                $valordefinido = 'Veterinaria';
            } elseif ($veterinaria == 0){
                $valordefinido = 'Laboratorio';
            } else {
                // Aquí puedes manejar el caso cuando $veterinaria no es ni 0 ni 1
                $valordefinido = 'Valor no definido';
            }

                $sucursal = $request->input('sucursal');
                $usuario = $request->input('usuario');
                $this->Bitacorastore($laboratorio->id, [
                       'usuario' => $usuario, 
                       'accion' => 'Se cambio el estado de veterinaria de la sucursal: "'. $sucursal .'"'. ' a '. '"' . $valordefinido. '" del
                       laboratorio "'. $nombre .'"',
                   ]);

                // Log::info('VETERINARIA, lineas afectadas: ', [$cfdis]);
                // Log::info('CFDI id ', [$cfdi_id]);

            if ($request->ajax()) {
                // Si es una solicitud AJAX, devolver una respuesta JSON con un mensaje de éxito
                return response()->json(['success' => true, 'message' => 'CFDI actualizado correctamente']);
            } else {
                // Si no es una solicitud AJAX, redirigir a la página de laboratorios con un mensaje de éxito
                return redirect()->route('laboratorio.index')->with('success', 'CFDI actualizado correctamente');
            }

    } catch (\Exception $e) {
        // Registrar el error en los logs
        Log::error('Error al actualizar datos en la base de datos remota: ' . $e->getMessage());
        if ($request->ajax()) {
            // Si es una solicitud AJAX, devolver una respuesta JSON con un mensaje de error
            return response()->json(['success' => false, 'message' => 'Error al actualizar datos en la base de datos remota: ' . $e->getMessage()]);
        } else {
            // Si no es una solicitud AJAX, redirigir a la página de laboratorios con un mensaje de error
            return redirect()->route('laboratorio.index')->with('error', 'Error al actualizar datos en la base de datos remota: ' . $e->getMessage());
        }
    }
}



public function actualizarCFDI(Request $request, $id)
{
    try {
        // Obtener el laboratorio
        $laboratorio = Laboratorio::findOrFail($id);

        // Conectar a la base de datos externa y editar
        $this->gestorBD->conectarBDyEditar($id);
        $connectionName = 'externo_' . $laboratorio->id;
        $conexion = $this->gestorBD->obtenerConexion($connectionName);

        // Obtener ID y datos antes de la actualización
        $IDcfdi = $request->input('CFDIid');
        $idSucursal = $request->input('idSucursal');

        // Verificar datos de entrada
        // Log::info('ID CFDI: ' . $IDcfdi);
        // Log::info('ID Sucursal: ' . $idSucursal);

        $cfdiAnterior = $conexion
            ->table('cfdi_parametros')
            ->where('id', $IDcfdi)
            ->first();

        $sucursalAnterior = $conexion
            ->table('sucursales')
            ->where('idSucursal', $idSucursal)
            ->first();

        // Log::info('CFDI Anterior: ' . json_encode($cfdiAnterior));
        // Log::info('Sucursal Anterior: ' . json_encode($sucursalAnterior));

        // Actualizar datos de CFDI
        $cfdiUpdate = $conexion
            ->table('cfdi_parametros')
            ->where('id', $IDcfdi)
            ->update([
                'VERSION' => $request->input('editVersion'),
                'TIMESESION' => $request->input('editTimesession'),
                'SESIONESLIMITE' => $request->input('editsesioneslimite'),
                'flag_sucursales' => $request->input('editMesMax'),
                // Agregar más campos y valores para actualizar según sea necesario
            ]);

        // Verificar si la actualización fue exitosa
        // if ($cfdiUpdate === 0) {
        //     Log::warning('No se actualizaron registros de CFDI. ID CFDI: ' . $IDcfdi);
        // }

        // Actualizar datos de sucursal
        $sucursalUpdate = $conexion
            ->table('sucursales')
            ->where('idSucursal', $idSucursal)
            ->update([
                'descripcion' => $request->input('editSucursal'),
                // Agregar más campos y valores para actualizar según sea necesario
            ]);

        // Verificar si la actualización fue exitosa
        // if ($sucursalUpdate === 0) {
        //     Log::warning('No se actualizaron registros de Sucursal. ID Sucursal: ' . $idSucursal);
        // }

        // Obtener datos actualizados después de la actualización
        $cfdiActualizado = $conexion
            ->table('cfdi_parametros')
            ->where('id', $IDcfdi)
            ->first();

        $sucursalActualizada = $conexion
            ->table('sucursales')
            ->where('idSucursal', $idSucursal)
            ->first();

        // // Verificar los datos actualizados
        // log::info('laboratorioID: ' . $id);
        // Log::info('CFDI Anterior: ' . json_encode($cfdiAnterior));
        // Log::info('Sucursal Anerior: ' . json_encode($sucursalAnterior));
        // Log::info('CFDI Actualizado: ' . json_encode($cfdiActualizado));
        // Log::info('Sucursal Actualizada: ' . json_encode($sucursalActualizada));

        // Llamar a CFDIbitacora para registrar los cambios y enviar los datos de usuario y sucursal
        $usuario = $request->input('usuario');
        $this->CFDIbitacora($request, $id, $cfdiAnterior, $sucursalAnterior, $usuario, $sucursalActualizada, $cfdiActualizado);

        // Devolver respuesta JSON de éxito
        return response()->json([
            'success' => true,
            // Puedes devolver más datos si es necesario
        ]);

    } catch (\Exception $e) {
        // Manejo de errores generales
        Log::error('Error al procesar la solicitud de actualización de CFDI: ' . $e->getMessage());

        // Retornar error si es una solicitud Ajax
        if ($request->ajax()) {
            return response()->json(['error' => 'Se ha producido un error. Por favor, inténtelo de nuevo más tarde.'], 500);
        }

        // Lanzar excepción si no es solicitud Ajax
        throw $e;
    }
}








public function CFDIbitacora(Request $request, $id, $cfdiAnterior, $sucursalAnterior, $usuario, $sucursalActualizada, $cfdiActualizado)
{
    try {
        // Iniciar arreglo para almacenar los cambios detectados
        $cambios = [];

        // Comparar y registrar cambios para CFDI
        if ($cfdiAnterior->VERSION !== $cfdiActualizado->VERSION) {
            $cambios[] = 'Versión de "' . $cfdiAnterior->VERSION . '" a "' . $cfdiActualizado->VERSION . '"';
        }
        if ($cfdiAnterior->TIMESESION !== $cfdiActualizado->TIMESESION) {
            $cambios[] = 'Tiempo de sesión de "' . $cfdiAnterior->TIMESESION . '" a "' . $cfdiActualizado->TIMESESION . '"';
        }
        if ($cfdiAnterior->SESIONESLIMITE !== $cfdiActualizado->SESIONESLIMITE) {
            $cambios[] = 'Límite de sesiones de "' . $cfdiAnterior->SESIONESLIMITE . '" a "' . $cfdiActualizado->SESIONESLIMITE . '"';
        }
        if ($cfdiAnterior->flag_sucursales !== $cfdiActualizado->flag_sucursales) {
            $cambios[] = 'Límite mensual "' . $cfdiAnterior->flag_sucursales . '" a "' . $cfdiActualizado->flag_sucursales . '"';
        }

        // Comparar y registrar cambios para Sucursal
        if ($sucursalAnterior->descripcion !== $sucursalActualizada->descripcion) {
            $cambios[] = 'Sucursal de "' . $sucursalAnterior->descripcion . '" a "' . $sucursalActualizada->descripcion . '"';
        }

        // Si no hubo cambios registrados
        if (empty($cambios)) {
            $this->Bitacorastore($id, [
                'usuario' => $usuario,
                'accion' => 'Se accionó el formulario de actualizar los registros en cfdi_parametros pero no se realizaron cambios',
            ]);
        } else {
            // Si hubo cambios, registrar en la bitácora
            $this->Bitacorastore($id, [
                'usuario' => $usuario,
                'accion' => 'Actualización de CFDI en la sucursal: "' . $sucursalActualizada->descripcion . '" Cambios: ' . implode(', ', $cambios),
            ]);
        }

    } catch (\Exception $e) {
        // Manejo de errores
        Log::error('Error en CFDIbitacora: ' . $e->getMessage());
        throw $e;
    }
}





public function editarCFDI(Request $request, $id)
{
    try {
        // Obtener todos los laboratorios
        $laboratorio = Laboratorio::findorfail($id);

        $IDcfdi=$request->input('CFDIid');

        // Array para almacenar datos
        $sucursal = [];

        // Configurar una conexión externa general
        $this->gestorBD->conectarBDyEditar($id);


            try {
                // Configurar conexión externa dinámicamente
                $connectionName = 'externo_' . $laboratorio->id;

                // Obtener conexión del gestor de bases de data
                $conexion = $this->gestorBD->obtenerConexion($connectionName);

                // Obtener data del laboratorio usando la conexión externa
                $sucursal = $conexion
                    ->table('sucursales as s')
                    ->leftJoin('cfdi_parametros as c', 's.idSucursal', '=', 'c.CFDISUCURSAL')
                    ->select(
                        'c.CFDISUCURSAL',
                        'c.id as CFDIid',
                        'c.VETERINARIA',
                        'c.TIMESESION',
                        'c.SUSCRIPCION',
                        'c.SESIONESLIMITE',
                        'c.VERSION',
                        'c.flag_sucursales',
                        's.descripcion as descripcion_sucursal',
                        's.idSucursal'
                    )
                    ->where('c.id', $IDcfdi)
                    ->whereNotNull('c.CFDISUCURSAL')
                    ->get();

                    


                // Transformar y agregar data al array $data

            } catch (\Exception $e) {
                // Loggear error al obtener data del laboratorio
                Log::error('Error al obtener data del laboratorio ' . $laboratorio->id . ': ' . $e->getMessage());
                // Puedes optar por continuar con el siguiente laboratorio en caso de error
               
            }
        

        // Devolver los datos para DataTables
        return response()->json([
            'success' => true,
            // 'CFDISUCURSAL' => $sucursal->pluck('CFDISUCURSAL'), 
            'CFDIid' => $sucursal->pluck('CFDIid'),
            'idSucursal' => $sucursal->pluck('idSucursal'),
            'sucursal' => $sucursal->pluck('descripcion_sucursal'),
            'mesmax' => $sucursal->pluck('flag_sucursales'),
            'version' => $sucursal->pluck('VERSION'),
            'sesionlimite' => $sucursal->pluck('SESIONESLIMITE'),
            'timesession' => $sucursal->pluck('TIMESESION'),
            'idLab' => $id,
        ]);

    } catch (\Exception $e) {
        // Manejo de errores generales
        Log::error('Error al procesar la solicitud de laboratorios: ' . $e->getMessage());

        if ($request->ajax()) {
            return response()->json(['error' => 'Se ha producido un error. Por favor, inténtelo de nuevo más tarde.'], 500);
        }
    }
}



private function probarConexion($servidor, $baseDatos, $usuario, $contrasena)
{
    try {
        // Intentar realizar la conexión
        $pdo = new \PDO("mysql:host=$servidor;dbname=$baseDatos", $usuario, $contrasena);

        // La conexión fue exitosa
        return true;
    } catch (\PDOException $e) {
        // Manejar errores PDO
        $errorCode = $e->getCode();

        if ($errorCode === '1045') {
            // Código de error 1045: credenciales incorrectas
            throw new CredencialesInvalidasException("Las credenciales proporcionadas son incorrectas");
        } elseif ($errorCode === '1049') {
            // Código de error 1049: base de datos no encontrada
            throw new BaseDeDatosNoEncontradaException("La base de datos especificada no existe");
        } else {
            // Otro error de conexión
            throw new ConexionFallidaException("La conexión a la base de datos falló: " . $e->getMessage());
        }
    }
}

public function testNewConexion(Request $request)
{
    $servidor = $request->input('servidor_sql');
    $baseDatos = $request->input('base_de_datos');
    $usuario = $request->input('usuario_sql');
    $contrasena = $request->input('password_sql');

    try {
        $conexionExitosa = $this->probarConexion($servidor, $baseDatos, $usuario, $contrasena);
        if ($conexionExitosa) {
            return response()->json(['success' => true, 'message' => 'Conexión exitosa a la base de datos']);
        } else {
            return response()->json(['success' => false, 'message' => 'Error de conexión: No se pudo conectar a la base de datos']);
        }
    } catch (ConexionFallidaException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    } catch (CredencialesInvalidasException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    } catch (BaseDeDatosNoEncontradaException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    } catch (\PDOException $e) {
        return response()->json(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    }
}


public function testConexion(Request $request)
{
    $servidor = $request->input('Editservidor_sql');
    $baseDatos = $request->input('Editbase_de_datos');
    $usuario = $request->input('Editusuario_sql');
    $contrasena = $request->input('Editcontrasena_sql');

    // log::info($servidor);
    // log::info($baseDatos);
    // log::info($usuario);
    // log::info($contrasena);




    try {
        $conexionExitosa = $this->probarConexion($servidor, $baseDatos, $usuario, $contrasena);
        if ($conexionExitosa) {
            return response()->json(['success' => true, 'message' => 'Conexión exitosa a la base de datos']);
        } else {
            return response()->json(['success' => false, 'message' => 'Error de conexión: No se pudo conectar a la base de datos']);
        }
    } catch (ConexionFallidaException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    } catch (CredencialesInvalidasException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    } catch (BaseDeDatosNoEncontradaException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    } catch (\PDOException $e) {
        return response()->json(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    }
}




public function revisarYGuardar(Request $request, $id)
{
    try {
        $conexionExitosa = $this->probarConexion($request->servidor_sql, $request->base_de_datos, $request->usuario_sql, $request->password_sql);
        if ($conexionExitosa) {
            // Guardar la información de conexión
            // ...
            return response()->json(['success' => true, 'message' => 'Conexión exitosa a la base de datos MySQL']);
        } else {
            return response()->json(['success' => false, 'message' => 'Error de conexión: No se pudo conectar a la base de datos']);
        }
    } catch (ConexionFallidaException $e) {
        return response()->json(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    } catch (CredencialesInvalidasException $e) {
        return response()->json(['success' => false, 'message' => 'Error de conexión: La contraseña es incorrecta']);
    } catch (BaseDeDatosNoEncontradaException $e) {
        return response()->json(['success' => false, 'message' => 'Error de conexión: La base de datos no fue encontrada']);
    } catch (\PDOException $e) {
        return response()->json(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    }
}



}
