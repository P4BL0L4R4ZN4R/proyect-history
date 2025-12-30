@include('layouts.plantilla')

@extends('adminlte::page')

@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)

@section('title', 'PanelLab | Administrador de laboratorios')

@section('content_header')




@endsection
@section('content')


<h2>Reporte de consumos mensuales</h2>

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
    
    
    <div class="tooltip-container">
        <a type="button" href="{{ route('laboratorio.Labsinuso') }}" class="comic-button-primary-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
            <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                <i class="far fa-frown" ></i>
        
            </a>    
            <span class="tooltip-md bg-primary">Laboratorios sin uso actualmente</span>
            <span class="text"></span>
    </div>



{{-- 

    <div class="tooltip-container">
        <!-- Enlace para generar PDF en nueva pestaña -->
            <a href="{{ route('laboratorios.pdf') }}"  target="_blank" class="comic-button-danger-outline-md" style="font-family: Arial, Helvetica, sans-serif">
                <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                <i class="fas fa-solid fa-file-pdf"></i>
                Generar PDF
            </a>
                <span class="tooltip-md bg-danger">Generar PDF</span>
                <span class="text"></span>
    </div>

        <!-- Formulario oculto para enviar el usuario al controlador -->
        <form id="formGenerarPDF" action="{{ route('laboratorios.pdf') }}" method="POST" style="display: none;" target="_blank">
            @csrf
            <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
            <!-- Puedes agregar más campos ocultos según sea necesario -->
            <!-- Aquí podrías incluir más campos según sea necesario -->
            <button type="submit" class="btn btn-danger" style="font-family: Arial, Helvetica, sans-serif;">
                <i class="fas fa-solid fa-file-pdf"></i>
                Generar PDF
            </button>
        </form> --}}


        <div class="tooltip-container">
            <!-- Enlace para generar PDF en nueva pestaña -->
                <a href="{{ route('laboratorios.pdf') }}"  target="_blank" class="comic-button-danger-outline-md PDFEXPORT" style="font-family: Arial, Helvetica, sans-serif">
                    <i class="fas fa-solid fa-file-pdf"></i>
                    Generar PDF
                </a>
                    <span class="tooltipUI bg-danger">Generar PDF</span>
                    <span class="text"></span>
            </div>
            <!-- Formulario oculto para enviar el usuario al controlador -->
            <form id="formGenerarPDF" action="{{ route('laboratorios.pdf') }}" method="POST" style="display: none;" target="_blank">
                @csrf
                <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                <!-- Puedes agregar más campos ocultos según sea necesario -->
                <!-- Aquí podrías incluir más campos según sea necesario -->
                <button type="submit" class="btn btn-danger PDFEXPORT" style="font-family: Arial, Helvetica, sans-serif;">
                    <i class="fas fa-solid fa-file-pdf"></i>
                    Generar PDF
                </button>
            </form>
     
</div>



    <div class="container-fluid" style="width: 100%; overflow-x: auto; margin: auto; ">


            <table id="laboratorios" class="table-borderless">
                <thead>
                    <tr>
                        <th rowspan="2" class="no-border-bottom" style="width: 1%; height: 10%; text-align: center;">Nombre</th>
                        <th rowspan="2" class="no-border-bottom" style="width: 20%; height: 10%; text-align: center;">Laboratorio</th>
                        <th rowspan="2" class="no-border-bottom" style="width: 10%; height: 10%; text-align: center;">Versión</th>
                        <th rowspan="2" class="no-border-bottom" style="width: 10%; height: 10%; text-align: center;">Veterinaria</th>
                        <th rowspan="2" class="no-border-bottom" style="width: 12%; height: 10%; text-align: center;">Última Actualización</th>
                        <th style="width: 32%; height: 10%; text-align: center;" colspan="5" data-dt-order="disable" >Órdenes</th>
                        <th rowspan="2" class="no-border-bottom" style="width: auto; height: 10%; text-align: center;"> Acciones </th>
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
                                    <div style="font-weight: bold; display: inline-block; width:60%;">{{ $laboratorio->nombre }} </div>
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
                                <td>
                                    @include('laboratorios.modal.consumoXperiodo')
                        
                                    <div class="tooltip-container">
                                            <button class="comic-button-success-outline  consumosX" data-toggle="modal" 
                                            data-target="#mostrarModalCFDI{{ $cfdi->id }}-{{ $laboratorio->id }}"
                                            data-sucursal-id="{{ $cfdi->CFDISUCURSAL }}"
                                            data-sucursal="{{ $cfdi->descripcion_sucursal }}"
                                            data-nombre-laboratorio="{{ $laboratorio->nombre }}"
                                            data-cfdi-id="{{ $cfdi->id }}" data-laboratorio-id="{{ $laboratorio->id }}" >
                                                <i class="fas fa-calendar"></i>
                                            </button>
                                                <span class="tooltipUI bg-success">Consultar consumos </span>
                                                <span class="text"></span>
                                    </div>


                                </td>
                                <td style="text-align: center;">{{ $cfdi->SUSCRIPCION ?? '' }} </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

    </div>



                    
            <script>


                $(document).on('click', '.consumosX', function() {
                    // Obtén los IDs desde el botón
                    var sucursalId = $(this).data('sucursal-id');
                    var Idlaboratorio = $(this).data('laboratorio-id');
                    var laboratorioNombre = $(this).data('nombre-laboratorio');
                    var sucursalNombre = $(this).data('sucursal');
                    
                    $.ajax({
                            url: 'fecha/',
                            method:'GET',
                            // data: {id: $(this).attr(data-laboratorio-id)}
                            success: function(response) {
                                if(response.success)
                            {
                    
                            $("#LaboratorioNombre").val(laboratorioNombre);
                            $("#SucursalNombre").val(sucursalNombre);
                            
                            $("#idlaboratorio").val(Idlaboratorio);
                            $("#idSucursal").val(sucursalId);
                            

                            $("#fechaInicio").val(response.fechaInicio);
                            $("#fechaFin").val(response.fechaFin);

                            $('#consumos').modal('show');
                            }
                            else
                            {
                                $.notify(response.mensaje,"error");
                            }

                            }
                        })


                    // console.log(sucursalId);
                    // console.log(Idlaboratorio);
                    // Muestra el modal
                  
                });

                $(document).on('click', '.consultarOrdenes',function() {
                    // console.log('Botón consultado');
                    var token = $('meta[name="csrf-token"]').attr('content');

                    var fechaInicio = $('#fechaInicio').val();
                    var fechaFin = $('#fechaFin').val();
                    
                    var sucursalId = $("#idSucursal").val();
                    var Idlaboratorio = $("#idlaboratorio").val();

                    // console.log(sucursalId);
                    // console.log(Idlaboratorio);
                    // console.log(fechaInicio);
                    // console.log(fechaFin);

                    var $button = $(this);
    
                    $button.addClass('btn-disabled'); 


                    // Realiza la solicitud AJAX

                    $("#ConsumosTotales").val('Calculando...');

                    $.ajax({
                        url: 'ConsumosXPeriodo/' + Idlaboratorio, // Asegúrate de que la URL sea correcta
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token // Incluye el token CSRF en los encabezados
                        },
                        data: {
                        
                            idSucursal: sucursalId,
                            fechaInicio: fechaInicio,
                            fechaFin: fechaFin
                        },
                        success: function(response) {
                            if (response.success) {
                                // Actualiza el campo con los datos recibidos
                                $("#ConsumosTotales").val(response.resultados);
                            } else {
                                // Muestra un mensaje de error si la respuesta no es exitosa
                                $("#ConsumosTotales").val('No disponible');
                                console.error(response.mensaje || 'No se pudo obtener la información', "error");
                            }
                            $button.removeClass('btn-disabled');
                        },
                        error: function(xhr, status, error) {
                            $button.removeClass('btn-disabled');
                            // Maneja errores en la solicitud AJAX
                            console.error("Error en la solicitud: " + error, "error");
                        }
                    });
                });
            </script>






<script>
    // Evento clic para el botón de Generar PDF
    $(document).ready(function() {
        $('button.PDFEXPORT').click(function(e) {
            e.preventDefault(); // Prevenir comportamiento por defecto del botón

            // Obtener el formulario y enviarlo
            var form = $('#formGenerarPDF');
            form.submit();
        });
    });
</script>


            <script>
                // Evento clic para el enlace de Generar PDF
                $(document).ready(function() {
                    $('a.PDFEXPORT').click(function(e) {
                        e.preventDefault(); // Prevenir comportamiento por defecto del enlace

                        // Obtener el formulario oculto y enviarlo
                        var form = $('#formGenerarPDF');
                        form.submit();
                    });
                });
            </script>


    <script>
                        var groupColumn0 = [0];
                    var table = $('#laboratorios').DataTable({
                        language: {
                        url: '//cdn.datatables.net/plug-ins/2.1.0/i18n/es-MX.json',

                        },
                    columnDefs: [{ visible: false, targets: groupColumn0 }],
                    order: [[groupColumn0, 'asc']],
                    displayLength: 25,
                    // responsive: true,
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
                                            '<tr class="group" > <td colspan="11" style="height: 1px !important; " class="table-secondary ">' +
                                                group + '<tr></tr>'


                                        );

                                    last = group;
                                }
                            });

                    }
                    });




    </script>
@endsection
