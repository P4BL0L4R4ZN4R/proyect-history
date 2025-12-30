@include('layouts.plantilla')

@extends('adminlte::page')

@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)

@section('title', 'PanelLab | Administrador de laboratorios')

@section('content_header')
@endsection

@section('content')

<input type="hidden" name="usuario" value="{{ Auth::user()->name }}">

{{-- <h1>{{ Auth::user()->name }}</h1> --}}

{{-- resources\views\layouts\img\inadware-de-mexico.jpeg --}}

  

<div class="d-flex justify-content-end align-items-center gap-3" style="margin-right: 10px;">

    <a type="button" href="{{ route('laboratorio.Cfdiveterinaria') }}" class="btn btn-warning" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
        <i class="fas fa-paw" style="color: white;"></i> 
    </a>
    
    <a type="button" href="{{ route('laboratorio.Cfdisuscripcion') }}" class="btn btn-success" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
        <i class="fas fa-power-off" style="color: white;"></i> 
    </a>

    <a type="button" href="{{ route('laboratorio.Cfdiversion') }}" class="btn btn-primary" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
        <i class="fas fa-laptop"></i> 
    </a>

    <a type="button" href="{{ route('laboratorio.Labsinuso') }}" class="btn btn-danger" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
        <i class="fas fa-virus-slash" style="color: white;"></i> 
    </a>

    <a type="button" href="{{ route('laboratorio.filterCFDI') }}" class="btn btn-secondary" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
        <i class="fas fa-file-alt" style="color: white;"></i> 
    </a>

    @include('laboratorios.modal.crear')
    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#crearModal" style="font-family: Arial, Helvetica, sans-serif;">
        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
        <i class="fas fa-plus"></i> Agregar nuevo Laboratorio
    </button>

</div>


{{-- <form action="{{ route('laboratorio.index') }}" method="GET">
    <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Buscar...">
    <button type="submit">Buscar</button>
</form> --}}



<table id="laboratorios" class="table-bordered" >
    <thead>
        <tr>
            <th rowspan="2" style="width: 1%;  height: auto; text-align: center;" data-orderable="false">Nombre</th>
            <th rowspan="2" style="width: 20%; height: auto;  text-align: center;" >Laboratorio</th>
            <th rowspan="2" style="width: 10%; height: auto;  text-align: center;" >Version</th>
            <th rowspan="2" style="width: 10%; height: auto;  text-align: center;" >Veterinaria</th>
            <th rowspan="2" style="width: 12%; height: auto;  text-align: center;" >Ultima actualizacion</th>
            <th colspan="5" style="width: 27%; height: auto;  text-align: center;" data-orderable="false" data-searchable="false" >Ordenes</th>
            <th rowspan="2" style="width: 10%; height: auto; text-align: center;" data-orderable="false" data-searchable="false" >Acciones</th>
            <th rowspan="2" style="width: 10%; height: auto;  text-align: center;" >Suscripcion</th>
        </tr>
        <tr>
            <th  title="ordenes por dia">Dia 
                <i class="fas fa-file-medical-alt" style="color:  #007BFF;  height: auto;" title="ordenes por dia">
                    </i> 
                </th>
            <th  title="ordenes por mes">Mes <i class="fas fa-clipboard-check" style="color: #28A745;  height: auto;"  title="ordenes por mes">
                </i>
            </th>
            <th  title="ordenes por año">Año <i class="fas fa-clipboard-list"  style="color: #DC3545;  height: auto;"   title="ordenes por año">
                </i>
            </th>
            <th style="text-align: center;" title="mes anterior">Mes antes  
                <br>
                <i class="fas fa-calendar-day" style="color:  #343A40  height: auto; text-align:center" title="ordenes del mes anterior">
                </i>
                </th>
            <th style="text-align: center;" title="mes max">mes max 
                <br>
                <i class="fas fa-exclamation-circle"  style="color: #17A2B8;  height: auto; text-align:center"   title="mes max">
                    </i>
                </th> 
        </tr>  
    </thead>
    

    <tbody id="laboratorio-tbody">
        @foreach ($laboratorios as $laboratorio)
            @php
                $cfdis_laboratorio = $cfdis[$laboratorio->id] ?? [];
                $conectado_bd = $laboratorio->base_de_datos_id ? true : false;
            @endphp

            @foreach ($cfdis_laboratorio as $cfdi)
                @php
                    $conteo = $conteosPorCFDi[$laboratorio->id][$cfdi->id] ?? [];
                @endphp
                <tr data-laboratorio-id="{{ $laboratorio->id }}" data-usuario="{{ Auth::user()->name }}" data-conectado-bd="{{ $conectado_bd }}">
                    <td style="height: 1px !important;">
                        {{ $laboratorio->nombre }} 
                    </td>
                    <td style="text-indent: 2em; text-align: center;">{{ $cfdi->descripcion_sucursal ?? '' }}</td>
                    <td style="text-align: center;">{{ $cfdi->VERSION ?? '' }}</td>
                    <td>
                        <input type="hidden" value="{{ $cfdi->id ?? '' }}" name='cfdi_id'>
                        <label class="switch-VETERINARIA" id="switchLabel-{{ $laboratorio->id ?? '' }}-{{ $cfdi->id ?? '' }}">
                            <input type="checkbox" class="form-check-input switch-veterinaria" name="VETERINARIA"
                                id="switch-{{ $laboratorio->id ?? '' }}-{{ $cfdi->id ?? '' }}"
                                {{ ($cfdi->VETERINARIA ?? '') == '1' ? 'checked' : '' }}
                                data-laboratorio-id="{{ $laboratorio->id ?? '' }}"
                                data-cfdi-id="{{ $cfdi->id ?? '' }}"
                                data-sucursal="{{ $cfdi->descripcion_sucursal ?? '' }}"
                                data-usuario="{{ Auth::user()->name }}"
                                value="{{ $cfdi->VETERINARIA ?? '' }}">
                            <span class="slider-VETERINARIA" title="Cambiar entre veterinaria y laboratorio"></span>
                            <label class="form-check-label" for="switch-{{ $laboratorio->id ?? '' }}-{{ $cfdi->id ?? '' }}"></label>
                        </label>
                    </td>
                    <td>{{ $conteo ? $conteo['lastUpdate'] : '' }}</td>
                    <td>{{ $conteo ? $conteo['conteoPorDia'] : '' }}</td>
                    <td>{{ $conteo ? $conteo['conteoPorMes'] : '' }}</td>
                    <td>{{ $conteo ? $conteo['conteoPorAnio'] : '' }}</td>
                    <td>{{ $conteo ? $conteo['conteoMesAnterior'] : '' }}</td>
                    <td> {{ $cfdi->flag_sucursales }}</td>
                    <td>
                        <div class="btn-group-cfdi" role="group" style=" border-radius: 0%; display: inline-flex; text-align:center">
                            @include('laboratorios.modal.editarCFDI')
                            <a class="btn btn-warning btn-sm" href="#" style="font-size: 12px; border-radius: 0%;" data-toggle="modal" title="Editar" data-target="#editarModalCFDI{{ $cfdi->id }}-{{ $laboratorio->id }}">
                                
                                <i class="fas fa-edit" style="color: white; font-size: 14px;"></i>
                            </a>
                            @include('laboratorios.modal.mostrarCFDI')
                            <a class="btn btn-primary btn-sm" href="#" data-toggle="modal" title="Ver" data-target="#mostrarModalCFDI{{ $cfdi->id }}-{{ $laboratorio->id }}" style=" border-radius: 0%;">
                                <i class="fas fa-eye" style="color: white; font-size: 13px;"></i>
                                
                            </a>
                            <form id="botonborrarCFDI{{ $cfdi->id }}" action="{{ route('laboratorio.borrarCFDI', $cfdi->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                                <input type="hidden" name="sucursal" value="{{ $cfdi->descripcion_sucursal ?? '' }}">
                                <input type="hidden" value="{{ $laboratorio->id }}" name='laboratorio_id'>
                                
                                <a class="btn btn-danger btn-sm" onclick="confirmarEliminacion({{ $cfdi->id }})" title="Eliminar" type="button" style=" border-radius: 0%;">
                                    <i class="fas fa-trash" style="color: white;"></i>
                                </a>
                            </form>
                        </div>
                    </td>
                    <td>
                        <input type="hidden" value="{{ $cfdi->id }}" name='cfdi_id'>
                        <label class="switch-SUSCRIPCION" id="switchLabel-{{ $laboratorio->id }}-{{ $cfdi->id }}">
                            <input type="checkbox" class="form-check-input switch-suscripcion" name="SUSCRIPCION"
                                id="switch-{{ $laboratorio->id }}-{{ $cfdi->id }}"
                                {{ $cfdi->SUSCRIPCION == 'ACTIVADO' ? 'checked' : '' }}
                                data-laboratorio-id="{{ $laboratorio->id }}"
                                data-cfdi-id="{{ $cfdi->id }}"
                                data-sucursal="{{ $cfdi->descripcion_sucursal ?? '' }}"
                                data-usuario="{{ Auth::user()->name }}"
                                data-usuario="{{ Auth::user()->name }}"
                                value="{{ $cfdi->SUSCRIPCION ?? '' }}">
                            <span class="slider-SUSCRIPCION" title="Cambiar entre activado y desactivado"></span>
                            <label class="form-check-label" for="switch-{{ $laboratorio->id }}-{{ $cfdi->id }}"></label>
                        </label>
                    </td>
                </tr>
            @endforeach

            @if ($conectado_bd && empty($cfdis_laboratorio))
            <tr data-laboratorio-id="{{ $laboratorio->id }}" data-usuario="{{ Auth::user()->name }}" data-conectado-bd="{{ $conectado_bd }}">
                    @if (isset($errores) && count($errores) > 0)
                        @foreach ($errores as $error)
                            <td>
                                {{ $laboratorio->nombre }}
                                <br>
                                <span style="color:red;">{{ $error }}</span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endforeach
                    @else
                        <td style="height: 1px !important;">
                            {{ $laboratorio->nombre }}
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @elseif ($conectado_bd == false)
            <input type="hidden"  data-conectado-bd="{{ $conectado_bd }}">
            <tr data-laboratorio-id="{{ $laboratorio->id }}" data-usuario="{{ Auth::user()->name }}" data-conectado-bd="{{ $conectado_bd }}">
                    <td style="height: 1px !important;">
                        {{ $laboratorio->nombre }}
                    </td>
                    <td style="color:red;"><strong>Conexión no disponible</strong></td>
                    <td></td>
                    <td></td>
                    <td style="color:red;"><strong>Agrega una base de datos</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>

{{ $laboratorios->links() }}


<input type="hidden" name="usuario" value="{{ Auth::user()->name }}">


    {{-- <script>
        var groupColumn = 0;

        var table = $('#laboratorios').DataTable({
            columnDefs: [{ visible: false, targets: groupColumn }],
            order: [[groupColumn, 'asc']],
            paging: false,
            info: false,
            displayLength: 25,
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function (group, i) {
                        if (last !== group) {
                            var laboratorioId = $(rows).eq(i).data('laboratorio-id');
                            var usuario = $(rows).eq(i).data('usuario');
                            
                            var buttons = `
                                <div class="btn-group" role="group">
                                    @include('laboratorios.modal.editar')
                                    <a class="btn btn-warning btn-sm" href="#" data-toggle="modal" title="Editar" data-target="#editarModal${laboratorioId}">
                                        <i class="fas fa-edit" style="color: white;font-size: 16px; border-radius:0%"></i>
                                    </a>
                                    @include('laboratorios.modal.config')
                                    <a class="btn btn-success btn-sm" href="#" data-toggle="modal" title="Configurar la conexión" data-target="#configurarModal${laboratorioId}" style="border-radius:0%">
                                        <i class="fas fa-cog" style="font-size: 15px"></i>
                                    </a>
                                    <form id="deleteFormLaboratorio${laboratorioId}" action="{{ route('laboratorio.destroy', '') }}/${laboratorioId}" method="post">
                                        @csrf
                                        @method('DELETE')
                                    <input type="hidden" name="usuario" value="${usuario}">  
                                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminar('deleteFormLaboratorio${laboratorioId}')" title="Eliminar" type="button" style="border-radius:0%">
                                            <i class="fas fa-trash" style="color: white"></i>
                                        </button>
                                    </form>
                                </div>
                            `;
                            $(rows).eq(i).before(
                                `<tr class="group">
                                    <td colspan="9" style="height: 1px !important;" class="table-secondary">
                                        ${group}
                                    </td>
                                    <td colspan="3" style="height: 1px !important;" class="table-secondary">
                                        ${buttons}
                                    </td>
                                </tr>`
                            );

                            last = group;
                        }
                    });
            }
        });
    </script> --}}

    {{-- <script>
        var groupColumn = 0;
    
        var table = $('#laboratorios').DataTable({
            columnDefs: [{ visible: false, targets: groupColumn }],
            order: [[groupColumn, 'asc']],
            paging: false,
            info: false,
            displayLength: 25,
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;
    
                api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function (group, i) {
                        if (last !== group) {
                            var laboratorioId = $(rows).eq(i).data('laboratorio-id');
                            var usuario = $(rows).eq(i).data('usuario');
                            
                            var buttons = `
                                <div class="btn-group" role="group">
                                    @include('laboratorios.modal.editar')
                                    <a class="btn btn-warning btn-sm" href="#" data-toggle="modal" title="Editar" data-target="#editarModal${laboratorioId}">
                                        <i class="fas fa-edit" style="color: white;font-size: 16px; border-radius:0%"></i>
                                    </a>
                                    @include('laboratorios.modal.config')
                                    <a class="btn btn-success btn-sm" href="#" data-toggle="modal" title="Configurar la conexión" data-target="#configurarModal${laboratorioId}" style="border-radius:0%">
                                        <i class="fas fa-cog" style="font-size: 15px"></i>
                                    </a>
                                    <form id="deleteFormLaboratorio${laboratorioId}" action="{{ route('laboratorio.destroy', '') }}/${laboratorioId}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="usuario" value="${usuario}">  
                                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminar('deleteFormLaboratorio${laboratorioId}')" title="Eliminar" type="button" style="border-radius:0%">
                                            <i class="fas fa-trash" style="color: white"></i>
                                        </button>
                                    </form>
                                </div>
                            `;
                            $(rows).eq(i).before(
                                `<tr class="group">
                                    <td colspan="9" style="height: 1px !important;" class="table-secondary">
                                        ${group}
                                    </td>
                                    <td colspan="3" style="height: 1px !important;" class="table-secondary">
                                        ${buttons}
                                    </td>
                                </tr>`
                            );
    
                            last = group;
                        }
                    });
            }
        });
    
      
    </script> --}}
    
    {{-- <script>
        var groupColumn = 0;
            
        var table = $('#laboratorios').DataTable({
            columnDefs: [{ visible: false, targets: groupColumn }],
            order: [[groupColumn, 'asc']],
            paging: false,
            info: false,
            displayLength: 25,
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;
    
                api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function (group, i) {
                        if (last !== group) {
                            var laboratorioId = $(rows).eq(i).data('laboratorio-id');
                            var usuario = $(rows).eq(i).data('usuario');
                            var conectado_bd = $(rows).eq(i).data('conectado-bd');
                            var buttons = '';
    
                            // Verificar si hay conexión de base de datos
                            if (!conectado_bd) {
                                // No hay conexión a base de datos
                                buttons = `
                                    <div class="btn-group" role="group">
                                        @include('laboratorios.modal.editar')
                                        <a class="btn btn-warning btn-sm" href="#" data-toggle="modal" title="Editar" data-target="#editarModal${laboratorioId}">
                                            <i class="fas fa-edit" style="color: white;font-size: 16px; border-radius:0%"></i>
                                        </a>
                                        @include('laboratorios.modal.conexion')
                                        <a class="btn btn-success btn-sm" href="#" data-toggle="modal" title="Configurar la conexión" data-target="#crearConexionModal${laboratorioId}" style="border-radius:0%">
                                            <i class="fas fa-cog" style="font-size: 15px"></i>
                                        </a>
                                        <form id="deleteFormLaboratorio${laboratorioId}" action="{{ route('laboratorio.destroy', '') }}/${laboratorioId}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="usuario" value="${usuario}">  
                                            <button class="btn btn-danger btn-sm" onclick="confirmarEliminar('deleteFormLaboratorio${laboratorioId}')" title="Eliminar" type="button" style="border-radius:0%">
                                                <i class="fas fa-trash" style="color: white"></i>
                                            </button>
                                        </form>
                                    </div>
                                `;
                            } else {
                                // Hay conexión a base de datos
                                buttons = `
                                    <div class="btn-group" role="group">
                                        @include('laboratorios.modal.editar')
                                        <a class="btn btn-warning btn-sm" href="#" data-toggle="modal" title="Editar" data-target="#editarModal${laboratorioId}">
                                            <i class="fas fa-edit" style="color: white;font-size: 16px; border-radius:0%"></i>
                                        </a>
                                        @include('laboratorios.modal.config')
                                        <a class="btn btn-success btn-sm" href="#" data-toggle="modal" title="Configurar la conexión" data-target="#configurarModal${laboratorioId}" style="border-radius:0%">
                                            <i class="fas fa-cog" style="font-size: 15px"></i>
                                        </a>
                                        <form id="deleteFormLaboratorio${laboratorioId}" action="{{ route('laboratorio.destroy', '') }}/${laboratorioId}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="usuario" value="${usuario}">  
                                            <button class="btn btn-danger btn-sm" onclick="confirmarEliminar('deleteFormLaboratorio${laboratorioId}')" title="Eliminar" type="button" style="border-radius:0%">
                                                <i class="fas fa-trash" style="color: white"></i>
                                            </button>
                                        </form>
                                    </div>
                                `;
                            }
    
                            $(rows).eq(i).before(
                                `<tr class="group">
                                    <td colspan="9" style="height: 1px !important;" class="table-secondary">
                                        ${group}
                                    </td>
                                    <td colspan="3" style="height: 1px !important;" class="table-secondary">
                                        ${buttons}
                                    </td>
                                </tr>`
                            );
    
                            last = group;
                        }
                    });
            }
        });
    </script>
     --}}
    
     <script>
        var groupColumn = 0;
            
        var table = $('#laboratorios').DataTable({
            columnDefs: [{ visible: false, targets: groupColumn }],
            order: [[groupColumn, 'asc']],
            paging: false,
            info: false,
            displayLength: 25,
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;
    
                api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function (group, i) {
                        if (last !== group) {
                            var laboratorioId = $(rows).eq(i).data('laboratorio-id');
                            var usuario = $(rows).eq(i).data('usuario');
                            var conectado_bd = $(rows).eq(i).data('conectado-bd');
                            console.log(conectado_bd);
                            var buttons = '';
                            // Verificar si hay conexión de base de datos
                            if (conectado_bd == false || conectado_bd != '1') {
                                // No hay conexión a base de datos o no es '1'
                                buttons = `
                                    <div class="btn-group" role="group">
                                        @include('laboratorios.modal.editar')
                                        <a class="btn btn-warning btn-sm" href="#" data-toggle="modal" title="Editar" data-target="#editarModal${laboratorioId}">
                                            <i class="fas fa-edit" style="color: white;font-size: 16px; border-radius:0%"></i>
                                        </a>
                                        @include('laboratorios.modal.conexion')
                                        <a class="btn btn-success btn-sm" href="#" data-toggle="modal" title="Configurar la conexión" data-target="#crearConexionModal${laboratorioId}" style="border-radius:0%">
                                            <i class="fas fa-cog" style="font-size: 15px"></i>
                                        </a>
                                        <form id="deleteFormLaboratorio${laboratorioId}" action="{{ route('laboratorio.destroy', '') }}/${laboratorioId}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="usuario" value="${usuario}">  
                                            <button class="btn btn-danger btn-sm" onclick="confirmarEliminar('deleteFormLaboratorio${laboratorioId}')" title="Eliminar" type="button" style="border-radius:0%">
                                                <i class="fas fa-trash" style="color: white"></i>
                                            </button>
                                        </form>
                                    </div>
                                `;
                            } else {
                                // Hay conexión a base de datos ('1')
                                buttons = `
                                    <div class="btn-group" role="group">
                                        @include('laboratorios.modal.editar')
                                        <a class="btn btn-warning btn-sm" href="#" data-toggle="modal" title="Editar" data-target="#editarModal${laboratorioId}">
                                            <i class="fas fa-edit" style="color: white;font-size: 16px; border-radius:0%"></i>
                                        </a>
                                        @include('laboratorios.modal.config')
                                        <a class="btn btn-success btn-sm" href="#" data-toggle="modal" title="Configurar la conexión" data-target="#configurarModal${laboratorioId}" style="border-radius:0%">
                                            <i class="fas fa-cog" style="font-size: 15px"></i>
                                        </a>
                                        <form id="deleteFormLaboratorio${laboratorioId}" action="{{ route('laboratorio.destroy', '') }}/${laboratorioId}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="usuario" value="${usuario}">  
                                            <button class="btn btn-danger btn-sm" onclick="confirmarEliminar('deleteFormLaboratorio${laboratorioId}')" title="Eliminar" type="button" style="border-radius:0%">
                                                <i class="fas fa-trash" style="color: white"></i>
                                            </button>
                                        </form>
                                    </div>
                                `;
                            }
    
                            $(rows).eq(i).before(
                                `<tr class="group">
                                    <td colspan="9" style="height: 1px !important;" class="table-secondary">
                                        ${group}
                                    </td>
                                    <td colspan="3" style="height: 1px !important;" class="table-secondary">
                                        ${buttons}
                                    </td>
                                </tr>`
                            );
    
                            last = group;
                        }
                    });
            }
        });
    </script>
    

@endsection



