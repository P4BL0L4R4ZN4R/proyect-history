@include('layouts.plantilla')

@extends('adminlte::page')

@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)

@section('title', 'PanelLab | Administrador de laboratorios')

@section('content_header')




@endsection
@section('content')





<h2>Laboratorios sin uso</h2>

    <div class="d-flex justify-content-end align-items-center gap-3" style="margin-right: 10px; margin-bottom:10px">

        <div class="tooltip-container">
            <a type="button" href="{{ route('laboratorio.index') }}" class="comic-button-info-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
                <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                <i class="fas fa-flask" style="color: white;"></i> 
            </a>
                <span class="tooltip-md bg-info">Listado de laboratorios</span>
                <span class="text"></span>
            </div>
            
            <div class="tooltip-container">
                <a type="button" href="{{ route('laboratorio.Cfdiveterinaria') }}"class="comic-button-warning-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
                    <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                        <i class="fas fa-paw" style="color: white;"></i> 
                    </a>
                        <span class="tooltip-md bg-warning">Veterinarias</span>
                        <span class="text"></span>
        </div>
        
        <div class="tooltip-container">
            <a type="button" href="{{ route('laboratorio.Cfdisuscripcion') }}" title="" class="comic-button-danger-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
                    <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                        <i class="fas fa-power-off" style="color: white;"></i> 
                    </a>
                <span class="tooltip-md bg-danger">Estado de suscripciones</span>
                <span class="text"></span>
        </div>
        
        <div class="tooltip-container">
            <a type="button" href="{{ route('laboratorio.Cfdiversion') }}"  class="comic-button-success-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
                <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                    <i class="fab fa-windows"></i>
                </a>
            
                <span class="tooltip-md bg-success">Revisar Version</span>
                <span class="text"></span>
        </div>
        
        
        <div class="tooltip-containerBT">
            <a type="button" href="{{ route('laboratorio.filterCFDI') }}" class="comic-button-secondary-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
                <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                    <i class="fas fa-file-alt" ></i> 
                </a>    
            
                    <span class="tooltipBT bg-secondary">Reporte de <br> consumos <br> mensuales</span>
                    <span class="text"></span>
        </div>

    </div>

        <div class="container-fluid" style="  width: 100%; overflow-x: auto; margin: auto; ">

        {{-- Tabla de laboratorios --}}
            <table id="laboratorios" class="table-borderless">
                <thead>
                    <tr>
                        <th rowspan="2" class="no-border-bottom" style="width: 1%; height: 10%; text-align: center;">Nombre</th>
                        <th rowspan="2" class="no-border-bottom" style="width: 20%; height: 10%; text-align: center;">Laboratorio</th>
                        <th rowspan="2" class="no-border-bottom" style="width: 10%; height: 10%; text-align: center;">Versión</th>
                        <th rowspan="2" class="no-border-bottom" style="width: 10%; height: 10%; text-align: center;">Veterinaria</th>
                        <th rowspan="2" class="no-border-bottom" style="width: 12%; height: 10%; text-align: center;">Última Actualización</th>
                        <th style="width: 32%; height: 10%; text-align: center;" colspan="5" data-dt-order="disable" >Órdenes</th>
                        <th rowspan="2" class="no-border-bottom" style="width: 10%; height: 10%; text-align: center;">Suscripción</th>
                    </tr>
                    <tr>
                        <th class="tooltip-containerTH">Dia 
                            <i class="fas fa-file-medical-alt" style="color:  #007BFF;  height: auto;" ></i> 
                                <span class="tooltipTH bg-primary">Ordenes por dia</span>    
                        </th>


                        <th class="tooltip-containerTH">Mes 
                            <i class="fas fa-clipboard-check" style="color: #28A745;  height: auto;"></i>
                                <span class="tooltipTH bg-success">Ordenes por mes</span>  
                        </th>

                        <th class="tooltip-containerTH">Año 
                            <i class="fas fa-clipboard-list"  style="color: #DC3545;  height: auto;" ></i>
                                <span class="tooltipTH bg-danger">Ordenes por año</span>  
                        </th>

                        <th class="tooltip-containerTH" style="text-align: center;">Mes antes  
                            <br>
                            <i class="fas fa-calendar-day" style="color: #6c757d; height: auto; text-align:center"></i>
                                <span class="tooltipTH bg-secondary">Ordenes del mes anterior</span>  
                        </th>

                        <th class="tooltip-containerTH" style="text-align: center;" title="maximo de ordenes por mes">mes max 
                            <br>
                            <i class="fas fa-exclamation-circle"  style="color: #17A2B8;  height: auto; text-align:center"  ></i>
                                <span class="tooltipTH bg-info">Máximo de ordenes por mes</span>  
                        </th> 
                </tr>  
                </thead>
                <tbody id="laboratorio-tbody">
                    @foreach ($laboratoriosData as $laboratorio)
                        @php
                            $cfdis_laboratorio = $cfdis[$laboratorio->id] ?? [];
                            $conectado_bd = $laboratorio->base_de_datos_id ? true : false;
                        @endphp
                        @foreach ($cfdis_laboratorio as $cfdi)
                            @php
                                $conteo = $conteosPorCFDi[$laboratorio->id][$cfdi->id] ?? [];
                            @endphp
                            <tr>
                                <td style="height: 1px !important; text-align: center;">
                                    <div style="font-weight: bold; display: inline-block; width:60%;">{{ $laboratorio->nombre }}</div>
                                </td>
                                <td style="text-indent: 2em; text-align: center;">{{ $cfdi->descripcion_sucursal ?? '' }}</td>
                                <td style="text-align: center;">{{ $cfdi->VERSION ?? '' }}</td>
                                @if ($cfdi->VETERINARIA == 1 ?? '')
                                <td style="text-align: center;">Veterinaria</td>
                                @elseif($cfdi->VETERINARIA == 0 ?? '')
                                <td style="text-align: center;">Laboratorio</td>
                                @else
                                <td></td>
                                @endif
                                <td style="text-align: center;">{{ $conteo ? $conteo['lastUpdate'] : '' }}</td>
                                <td style="text-align: center;">{{ $conteo ? $conteo['conteoPorDia'] : '' }}</td>
                                <td style="text-align: center;">{{ $conteo ? $conteo['conteoPorMes'] : '' }}</td>
                                <td style="text-align: center;">{{ $conteo ? $conteo['conteoPorAnio'] : '' }}</td>
                                <td style="text-align: center;">{{ $conteo ? $conteo['conteoMesAnterior'] : '' }}</td>
                                <td style="text-align: center;"> {{ $cfdi->flag_sucursales ?? ''}} </td>
                                <td style="text-align: center;">{{ $cfdi->SUSCRIPCION ?? '' }} </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

    <script>
                        var groupColumn0 = [0];
                    var table = $('#laboratorios').DataTable({
                    columnDefs: [{ visible: false, targets: groupColumn0 }],
                    order: [[groupColumn0, 'asc']],
                    displayLength: 25,
                    drawCallback: function (settings) {
                        var api = this.api();
                        var rows = api.rows({ page: 'current' }).nodes();
                        var last = null;

                        api.column(groupColumn0, { page: 'current' })
                            .data()
                            .each(function (group, i) {
                                if (last !== group) {
                                    $(rows)
                                        .eq(i)
                                        .before(
                                            '<tr class="group" > <td colspan="10" style="height: 1px !important; " class="table-secondary ">' +
                                                group + '<tr></tr>'


                                        );

                                    last = group;
                                }
                            });

                    }
                    });

                    // Order by the grouping
                    $('#laboratorio tbody').on('click', 'tr.group', function () {
                    var currentOrder = table.order()[0];
                    if (currentOrder[0] === groupColumn0 && currentOrder[1] === 'asc') {
                        table.order([groupColumn0, 'desc']).draw();
                    }
                    else {
                        table.order([groupColumn0, 'asc']).draw();
                    }
                    });


    </script>




@endsection
