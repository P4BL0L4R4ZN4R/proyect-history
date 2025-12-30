@include('layouts.plantilla')

@extends('adminlte::page')

@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)

@section('title', 'PanelLab | Administrador de laboratorios')

@section('content_header')
@endsection

@section('content')

    <h1>Bitacora</h1>

    <div class="text-right"> <!-- Contenedor con alineación a la derecha -->
    </div>

        <div class="container-fluid" style="  width: 100%; overflow-x: auto; margin: auto; ">

                <table id="bitacora" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Accion realizada</th>
                            <th>Fecha y hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bitacoras as $bitacora)
                        <tr>
                            <td>{{$bitacora->usuario}}</td>
                            <td>{{$bitacora->accion}}</td>
                            <td>{{$bitacora->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>

        <script>
            $(document).ready(function() {
                var groupColumn = 0;
                var table = $('#bitacora').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/2.1.0/i18n/es-MX.json'
                    },
                    order: [[2, 'desc']] 
                });
            });
        </script>




@endsection