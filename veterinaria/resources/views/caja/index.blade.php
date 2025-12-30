@extends('adminlte::page')

@section('title', 'Registro de cortes')

@section('content_header')

@stop

@section('content')




    <table  id="cortesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Saldo Inicial</th>
                <th>Saldo Final</th>
                <th>Estado</th>
                <th>Fecha de apertura</th>
                <th>Fecha de cierre</th>
                {{-- <th>Acciones</th> --}}
            </tr>
        </thead>
        <tbody>
            <!-- Los datos se cargarán aquí mediante DataTables -->
        </tbody>
    </table>


    <script>

        window.onload = function() {
            // Configuración de DataTable
            $('#cortesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('caja.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'user_name', name: 'user_name' },
                    { data: 'saldo_inicio', name: 'saldo_inicio' },
                    { data: 'saldo_fin', name: 'saldo_fin' },
                    { data: 'estado', name: 'estado' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    // { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
            });
        };

    </script>

@stop





