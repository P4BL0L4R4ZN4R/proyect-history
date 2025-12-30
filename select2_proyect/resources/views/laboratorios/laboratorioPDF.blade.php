{{-- resources/views/laboratorios/laboratorioPDF.blade.php --}}

<!DOCTYPE html>
    <html lang="es">
        <head>
            <meta charset="UTF-8">
        
            
            <title>PanelLab | Administrador de laboratorios</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
            <style>
                /* Estilos CSS adicionales para la tabla */
                table {
                    width: 100%;

                    margin-top: 20px;
                }
                th, td {
                    border: 1px solid black;
                    padding: 8px;
                    text-align: center;
                }
                th {
                    background-color: #f2f2f2;
                }
                .text-left {
                    text-align: left;
                }
            </style>

        </head>
            <body>
                <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">

                {{-- <img src="vendor/adminlte/dist/img/logo.png" alt="" > --}}
                
                <div style="display: flex; align-items: center;">
                    <img src="{{ asset('vendor/adminlte/dist/img/logo.png') }}" 
                         style="width: 7%; height: auto; margin-right: 10px;" >
                    <div style="display: flex; flex-direction: column;">
                        <h1 style="text-align: center">PanelLab</h1>
                        <p style="text-align: center">Listado de laboratorios</p>
                    </div>
                </div>
                    
                        <h3>Reporte de consumos mensuales:</h3>

                            <h5 style="font-size: 100%; text-align:right;">Fecha y hora: <p style="text-align:right;">{{ $fecha ?? '' }}</p> 
                            </h5>






                    <table id="laboratorios" class="table-bordered">
                        <thead style="background-color: lightblue">
                            <tr>
                                

                                    <th rowspan="2" style="width: 16%; text-align: center; background-color: lightblue ">Laboratorio</th>
                                    <th rowspan="2" style="width: 12%; text-align: center; background-color: lightblue">Sucursal</th>
                                    <th rowspan="2" style="width: 10%; text-align: center; background-color: lightblue">Versión</th>
                                    <th rowspan="2" style="width: 10%; text-align: center; background-color: lightblue">Veterinaria</th>
                                    <th rowspan="2" style="width: 15%; text-align: center; background-color: lightblue">Última Actualización</th>
                                    <th colspan="5" style="width: 27%; text-align: center; background-color: lightblue">Órdenes</th>
                                    <th rowspan="2" style="width: 10%; text-align: center; background-color: lightblue">Suscripción</th>
                                
                            
                            </tr>
                            <tr>
                            
                                <th style="text-align: center; background-color: lightblue">Día</th>
                                <th style="text-align: center; background-color: lightblue">Mes</th>
                                <th style="text-align: center; background-color: lightblue">Año</th>
                                <th style="text-align: center; background-color: lightblue">Mes antes</th>
                                <th style="text-align: center; background-color: lightblue">Mes max</th>

                                
                            </tr>
                        </thead>

                        
                        <tbody>
                            @foreach ($laboratorios as $laboratorio)
                                @php
                                    $cfdis_laboratorio = $cfdis[$laboratorio->id] ?? [];
                                    $rowspan = count($cfdis_laboratorio);


                                @endphp
                                @foreach ($cfdis_laboratorio as $key => $cfdi)
                                    <tr>
                                        @if ($key === 0)
                                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle; font-weight: bold;">{{ $laboratorio->nombre }}</td>
                                        @endif
                                        <td>{{ $cfdi->descripcion_sucursal ?? '' }}</td>
                                        <td>{{ $cfdi->VERSION ?? '' }}</td>
                                        @if ($cfdi->VETERINARIA == 1 ?? '')
                                        <td>Veterinaria</td>
                                        @elseif($cfdi->VETERINARIA == 0 ?? '')
                                        <td>Laboratorio</td>
                                        @else
                                        <td></td>
                                        @endif
                                        <td>{{ $conteosPorCFDi[$laboratorio->id][$cfdi->id]['lastUpdate'] ?? '' }}</td>
                                        <td>{{ $conteosPorCFDi[$laboratorio->id][$cfdi->id]['conteoPorDia'] ?? '' }}</td>
                                        <td>{{ $conteosPorCFDi[$laboratorio->id][$cfdi->id]['conteoPorMes'] ?? '' }}</td>
                                        <td>{{ $conteosPorCFDi[$laboratorio->id][$cfdi->id]['conteoPorAnio'] ?? '' }}</td>
                                        <td>{{ $conteosPorCFDi[$laboratorio->id][$cfdi->id]['conteoMesAnterior'] ?? '' }}</td>
                                        <td>{{ $cfdi->flag_sucursales ?? ''}}</td>
                                        <td>{{ $cfdi->SUSCRIPCION ?? '' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>




            </body>



    </html>
