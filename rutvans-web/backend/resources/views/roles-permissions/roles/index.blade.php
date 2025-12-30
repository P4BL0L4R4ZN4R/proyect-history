@extends('adminlte::page')

@section('title', 'Gestión de Roles Rutvans')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-user-shield me-2"></i> Gestión de Roles
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">Administra los roles del sistema Rutvans</p>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm" style="border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
                    <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <i class="fas fa-user-shield me-2"></i> Roles del Sistema
                    </h3>
                    <div class="ml-auto">
                        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#createRoleModal" style="font-weight: 600;">
                            <i class="fas fa-plus mr-1"></i>
                            Crear Rol
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="rolesTable" style="border-radius: 8px; overflow: hidden;">
                            <thead style="background: linear-gradient(135deg, #6c757d, #495057); color: white;">
                                <tr>
                                    <th style="width: 10%">#</th>
                                    <th>Nombre del Rol</th>
                                    <th style="width: 15%" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    <tr>
                                        <td class="text-center">{{ $role->id }}</td>
                                        <td>
                                            <strong>{{ $role->name }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-warning btn-sm" 
                                                        data-toggle="modal" data-target="#editRoleModal"
                                                        data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-role-id="{{ $role->id }}" onclick="confirmDelete(this)"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No hay roles registrados</h5>
                                                <p class="text-muted">Comienza creando tu primer rol del sistema.</p>
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

@include('roles-permissions.roles.create')
@include('roles-permissions.roles.edit')

@endsection

@section('css')
    {{-- Aquí puedes agregar hojas de estilo específicas --}}
@endsection

@section('js')
<script>
        $(document).ready(function() {
            let rolesDataTable = null;
    
            function initializeDataTable() {
                const table = $('#rolesTable');
                if (table.length === 0) return;
                
                const tbody = table.find('tbody');
                const rows = tbody.find('tr');
                const hasData = rows.length > 0 && !tbody.find('tr td[colspan]').length;
                
                if (!hasData) return;
                
                try {
                    if (rolesDataTable) {
                        rolesDataTable.destroy();
                        rolesDataTable = null;
                    }
                    
                    rolesDataTable = table.DataTable({
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
                        "order": [[ 0, "desc" ]],
                        "pageLength": 10,
                        "responsive": true,
                        "autoWidth": false,
                        "columnDefs": [
                            {
                                "targets": [0],
                                "width": "10%",
                                "className": "text-center"
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

            // Manejar edición de roles
            $(document).on('click', '[data-toggle="modal"][data-target="#editRoleModal"]', function() {
                const id = $(this).data('role-id');
                const name = $(this).data('role-name');
                
                $('#editRoleForm').attr('action', `/roles/${id}`);
                $('#editRoleId').val(id);
                $('#editRoleName').val(name);
            });

            // Limpiar formulario Crear al cerrar modal
            $('#createRoleModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('form').removeClass('was-validated');
            });

            // Limpiar formulario Editar al cerrar modal
            $('#editRoleModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('form').removeClass('was-validated');
            });
        });

        // Exponer la función confirmDelete al ámbito global
        window.confirmDelete = function(button) {
            const id = button.getAttribute('data-role-id');

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Deseas eliminar este rol? Esta acción no se puede deshacer.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Crear formulario tradicional para DELETE
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/roles/${id}`;

                    // Token CSRF
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Método DELETE
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);

                    // Agregar al DOM
                    document.body.appendChild(form);

                    // Enviar formulario
                    form.submit();
                }
            });
        }
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
