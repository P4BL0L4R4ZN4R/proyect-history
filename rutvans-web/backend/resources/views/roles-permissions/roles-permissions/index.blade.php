@extends('adminlte::page')

@section('title', 'Gestión de Roles y Permisos Rutvans')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3"
        style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1"
                style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-tasks me-2"></i> Asignar Permisos a Roles
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">Administra los permisos asignados a los roles del
                sistema Rutvans</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm"
                    style="border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
                        <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            <i class="fas fa-tasks me-2"></i> Permisos por Rol
                        </h3>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert"
                                style="border-radius: 8px;">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert"
                                style="border-radius: 8px;">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="rolesPermissionsTable"
                                style="border-radius: 8px; overflow: hidden;">
                                <thead
                                    style="background: linear-gradient(135deg, #6c757d, #495057); color: white;">
                                    <tr>
                                        <th style="width: 25%">Rol</th>
                                        <th>Permisos Asignados</th>
                                        <th style="width: 15%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roles as $role)
                                        <tr>
                                            <td>
                                                <strong>{{ $role->name }}</strong>
                                            </td>
                                            <td>
                                                @forelse($role->permissions as $permission)
                                                    <span class="badge badge-success mb-1">{{ $permission->name }}</span>
                                                @empty
                                                    <span class="text-muted">Sin permisos asignados</span>
                                                @endforelse
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        data-toggle="modal" data-target="#editPermissionModal"
                                                        data-role-id="{{ $role->id }}"
                                                        title="Asignar Permisos">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <div class="py-4">
                                                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No hay roles registrados</h5>
                                                    <p class="text-muted">No hay roles disponibles para asignar
                                                        permisos.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('roles-permissions.roles-permissions.edit')
@endsection

@section('css')
    {{-- Aquí puedes agregar hojas de estilo específicas --}}
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let rolesPermissionsDataTable = null;

            function initializeDataTable() {
                const table = $('#rolesPermissionsTable');
                if (table.length === 0) return;

                const tbody = table.find('tbody');
                const rows = tbody.find('tr');
                const hasData = rows.length > 0 && !tbody.find('tr td[colspan]').length;

                if (!hasData) return;

                try {
                    if (rolesPermissionsDataTable) {
                        rolesPermissionsDataTable.destroy();
                        rolesPermissionsDataTable = null;
                    }

                    rolesPermissionsDataTable = table.DataTable({
                        "language": {
                            "decimal": "",
                            "emptyTable": "No hay roles registrados",
                            "info": "Mostrando _START_ a _END_ de _TOTAL_ roles",
                            "infoEmpty": "Mostrando 0 a 0 de 0 roles",
                            "infoFiltered": "(filtrado de _MAX_ roles totales)",
                            "lengthMenu": "Mostrar _MENU_ roles por página",
                            "loadingRecords": "Cargando...",
                            "processing": "Procesando...",
                            "search": "Buscar:",
                            "zeroRecords": "No se encontraron roles que coincidan",
                            "paginate": {
                                "first": "Primero",
                                "last": "Último",
                                "next": "Siguiente",
                                "previous": "Anterior"
                            }
                        },
                        "order": [[ 0, "asc" ]],
                        "pageLength": 10,
                        "responsive": true,
                        "autoWidth": false,
                        "columnDefs": [
                            {
                                "targets": [0],
                                "width": "25%"
                            },
                            {
                                "targets": [2],
                                "width": "15%",
                                "orderable": false,
                                "searchable": false,
                                "className": "text-center"
                            }
                        ]
                    });
                } catch (error) {
                    // Removed console.error for cleaner code
                }
            }

            setTimeout(initializeDataTable, 250);

            // Manejar edición de permisos
            $(document).on('click', '[data-toggle="modal"][data-target="#editPermissionModal"]', function() {
                const roleId = $(this).data('role-id');
                const form = $('#editPermissionForm');
                const permissionsContainer = $('#permissionsContainer');

                // Configurar la acción del formulario
                form.attr('action', `/roles-permissions/${roleId}`);
                $('#editRoleId').val(roleId);

                // Mostrar mensaje de carga
                permissionsContainer.html('<div class="text-muted">Cargando permisos...</div>');

                // Realizar la solicitud para obtener los permisos
                $.ajax({
                    url: `/roles-permissions/${roleId}/edit`,
                    method: 'GET',
                    success: function(data) {
                        permissionsContainer.html('');

                        data.permissions.forEach(function(permission) {
                            const isChecked = data.assigned_permissions.includes(permission.id);
                            const checkbox = `
                                <div class="form-check mb-2">
                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="${permission.id}" ${isChecked ? 'checked' : ''} id="permission_${permission.id}">
                                    <label class="form-check-label" for="permission_${permission.id}">
                                        ${permission.name}
                                    </label>
                                </div>
                            `;
                            permissionsContainer.append(checkbox);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar permisos:', error);
                        permissionsContainer.html('<div class="text-danger">Error al cargar permisos.</div>');
                    }
                });
            });

            // Limpiar formulario al cerrar modal
            $('#editPermissionModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('#permissionsContainer').html('');
            });
        });
    </script>

    {{-- Script para SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ session('success') }}',
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 2000,
                width: '300px'
            });
        </script>
    @endif
@endsection
