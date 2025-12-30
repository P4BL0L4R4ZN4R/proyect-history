@include('layouts.plantilla')

@extends('adminlte::page')

@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)

@section('title', 'PanelLab | Administrador de laboratorios')

@section('content_header')
@endsection

@section('content')



<h2>Version de los Laboratorios</h2>

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
        <a type="button" href="{{ route('laboratorio.Labsinuso') }}" class="comic-button-primary-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
            <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                <i class="far fa-frown" ></i>
        
            </a>    
            <span class="tooltip-md bg-primary">Laboratorios sin uso actualmente</span>
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



<table id="laboratorios" class="table-bordered">
    <thead>
        <tr>
            <th style="width: 1%;  height: auto; text-align: center;" data-orderable="false">Nombre</th>
            <th style="width: 20%; height: auto;  text-align: center;" >Laboratorio</th>
            <th style="width: 10%; height: auto;  text-align: center;" >Version</th>
        </tr>
        
    </thead>
    

    <tbody id="laboratorio-tbody">
        @foreach ($laboratorios as $laboratorio)
            @php
                $cfdis_laboratorio = $cfdis[$laboratorio->id] ?? [];
                $conectado_bd = $laboratorio->base_de_datos_id ? true : false;
            @endphp

            @foreach ($cfdis_laboratorio as $cfdi)
                <tr>
                    <td style="height: 1px !important; text-align: center;">
                        <div style="font-weight: bold; display: inline-block; width:60%;">{{ $laboratorio->nombre }}</div>
                    </td>
                    <td style="text-indent: 2em; text-align: center;">{{ $cfdi->descripcion_sucursal ?? '' }}</td>
                    <td style="text-align: center;">{{ $cfdi->VERSION ?? '' }}</td>

                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
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
                                                '<tr class="group" > <td colspan="3" style="height: 1px !important; " class="table-secondary ">' +
                                                    group + '<tr></tr>'


                                            );

                                        last = group;
                                    }
                                });

                        }
                        });

                        // Order by the grouping
                        $('#labo ').on('click', 'tr.group', function () {
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