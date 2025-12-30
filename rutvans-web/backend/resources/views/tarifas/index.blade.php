@extends('adminlte::page')

@section('title', 'Tipos de Tarifas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-dollar-sign me-2 text-success"></i> Gestión de Tipos de Tarifas
        </h1>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTipoTarifaModal">
            <i class="fas fa-plus-circle me-1"></i> Crear Tipo de Tarifa
        </button>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table id="tarifasTable" class="table table-hover text-nowrap align-middle">
                <thead class="table-success">
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th>Nombre</th>
                        <th>Porcentaje</th>
                        <th>Creado</th>
                        <th>Actualizado</th>
                        <th class="text-center" style="width: 15%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tarifas as $tipo)
                        <tr>
                            <td>{{ $tipo->id }}</td>
                            <td>{{ e($tipo->name) }}</td>
                            <td>{{ $tipo->percentage }}%</td>
                            <td>{{ $tipo->created_at }}</td>
                            <td>{{ $tipo->updated_at }}</td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-warning btn-edit-tarifa"
                                        data-id="{{ $tipo->id }}"
                                        data-name="{{ e($tipo->name) }}"
                                        data-percentage="{{ $tipo->percentage }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $tipo->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('tarifas.create')
    @include('tarifas.edit')
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar DataTable
            new DataTable('#tarifasTable', {
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                responsive: true,
                autoWidth: false
            });

            // Manejar clic en botón de editar
            const buttons = document.querySelectorAll('.btn-edit-tarifa');
            const form = document.getElementById('editTipoTarifaForm');

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const percentage = button.getAttribute('data-percentage');

                    document.getElementById('editTipoTarifaId').value = id;
                    document.getElementById('editTipoTarifaNombre').value = name;
                    document.getElementById('editTipoTarifaPorcentaje').value = percentage;

                    form.setAttribute('action', `/tarifas/${id}`);

                    // Mostrar el modal manualmente (Bootstrap 5)
                    const modal = new bootstrap.Modal(document.getElementById('editTipoTarifaModal'));
                    modal.show();
                });
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: '¿Eliminar este tipo de tarifa?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ url('tarifas') }}/" + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.success ? 'success' : 'error',
                            title: data.message,
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => location.reload());
                    });
                }
            });
        }
    </script>
@endsection
