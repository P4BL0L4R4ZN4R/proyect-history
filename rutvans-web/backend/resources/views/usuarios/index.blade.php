@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-4" style="background: linear-gradient(135deg, #ff6600 0%, #ff8533 100%); padding: 1.5rem; border-radius: 15px; box-shadow: 0 8px 25px rgba(255, 102, 0, 0.15); animation: fadeInDown 0.6s ease-out;">
        <div>
            <h1 class="text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 2rem;">
                <i class="fas fa-users me-3"></i> Gestión de Usuarios
            </h1>
            <p class="text-white mb-0" style="font-family: 'Poppins', sans-serif; opacity: 0.9; font-size: 1.1rem;">
                Administra todos los usuarios del sistema
            </p>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 15px 15px 0 0; padding: 1.5rem;">
            <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <i class="fas fa-user-cog me-2"></i> Administración de Usuarios
            </h3>
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createUsuarioModal" style="font-weight: 600; border-radius: 8px; padding: 0.5rem 1.5rem;">
                <i class="fas fa-user-plus text-primary"></i> Crear Usuario
            </button>
        </div>
        
        <div class="card-body" style="padding: 2rem;">
            <div class="table-responsive">
                <table id="usuariosTable" class="table table-hover table-striped align-middle" style="border-radius: 8px; overflow: hidden;">
                    <thead style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                        <tr>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-hashtag me-1"></i> ID</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-user me-1"></i> Nombre</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-envelope me-1"></i> Email</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-phone me-1"></i> Teléfono</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-map-marker-alt me-1"></i> Dirección</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-calendar-plus me-1"></i> Creado</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-calendar-check me-1"></i> Actualizado</th>
                            <th class="fw-bold text-center" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-cogs me-1"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $usuario)
                            <tr style="transition: all 0.3s ease;">
                                <td>
                                    <span class="badge bg-primary" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">{{ $usuario->id }}</span>
                                </td>
                                <td style="font-weight: 500;">
                                    <i class="fas fa-user-circle text-muted me-2"></i>
                                    {{ e($usuario->name) }}
                                </td>
                                <td>
                                    <i class="fas fa-at text-muted me-2"></i>
                                    {{ e($usuario->email) }}
                                </td>
                                <td>
                                    <i class="fas fa-mobile-alt text-muted me-2"></i>
                                    {{ e($usuario->phone_number) }}
                                </td>
                                <td>
                                    <i class="fas fa-home text-muted me-2"></i>
                                    {{ e($usuario->address) }}
                                </td>
                                <td>
                                    <small class="text-muted">{{ $usuario->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $usuario->updated_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUsuarioModal"
                                            data-id="{{ $usuario->id }}"
                                            data-name="{{ e($usuario->name) }}"
                                            data-email="{{ e($usuario->email) }}"
                                            data-phone="{{ e($usuario->phone_number) }}"
                                            data-address="{{ e($usuario->address) }}"
                                            style="border-radius: 6px; font-weight: 500;">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        
                                        <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $usuario->id }})" style="border-radius: 6px; font-weight: 500;">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('usuarios.create')
    @include('usuarios.edit')
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            background-color: #f8f9fa;
        }
        
        .content-wrapper {
            background-color: #f8f9fa;
        }
    </style>

        /* SweetAlert2 custom button styles */
        .swal2-confirm-red {
            background-color: #d33 !important; /* Red */
            color: white !important;
            border: none !important;
            padding: 10px 20px !important; /* Increase padding for size */
            font-size: 1em !important; /* Adjust font size if needed */
            outline: none !important; /* Remove focus outline */
            box-shadow: none !important; /* Ensure no residual shadow */
        }

        .swal2-cancel-black {
            background-color: #000000 !important; /* Black */
            color: white !important;
            border: none !important;
            padding: 10px 20px !important; /* Increase padding for size */
            font-size: 1em !important; /* Adjust font size if needed */
            outline: none !important; /* Remove focus outline */
            box-shadow: none !important; /* Ensure no residual shadow */
        }

        /* Espacio entre botones de SweetAlert2 */
        .swal2-actions {
            gap: 15px; /* Adjust the desired space between buttons, increased slightly */
            margin-top: 20px; /* Add some space above the buttons if they are too close to the text */
        }

        /* Also target the buttons directly to catch any specific pseudo-classes */
        .swal2-confirm-red:focus,
        .swal2-confirm-red:active,
        .swal2-cancel-black:focus,
        .swal2-cancel-black:active {
            outline: none !important;
            box-shadow: none !important;
            border: none !important; /* Redundant but good for safety */
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#usuariosTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                responsive: true,
                autoWidth: false
            });

            document.querySelectorAll('[data-bs-target="#editUsuarioModal"]').forEach(button => {
                button.addEventListener('click', (event) => {
                    const target = event.currentTarget;
                    const form = document.getElementById('editUsuarioForm');

                    form.action = `/usuarios/${target.getAttribute('data-id')}`;
                    form.querySelector('#editUsuarioId').value = target.getAttribute('data-id');
                    form.querySelector('#editUsuarioNombre').value = target.getAttribute('data-name');
                    form.querySelector('#editUsuarioEmail').value = target.getAttribute('data-email');
                    form.querySelector('#editUsuarioTelefono').value = target.getAttribute('data-phone');
                    form.querySelector('#editUsuarioDireccion').value = target.getAttribute('data-address');

                    form.classList.remove('was-validated');
                });
            });

            document.getElementById('createUsuarioModal').addEventListener('hidden.bs.modal', () => {
                const form = document.getElementById('createUsuarioForm');
                form.reset();
                form.classList.remove('was-validated');
            });

            document.getElementById('editUsuarioModal').addEventListener('hidden.bs.modal', () => {
                const form = document.getElementById('editUsuarioForm');
                form.reset();
                form.classList.remove('was-validated');
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: '¿Eliminar este usuario?',
                text: "Esta acción no se puede deshacer.",
                icon: "error",
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                buttonsStyling: false, // Disable SweetAlert2's default button styling
                customClass: {
                    confirmButton: 'swal2-confirm-red', // Custom class for the confirm button
                    cancelButton: 'swal2-cancel-black' // Custom class for the cancel button
                },
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/usuarios/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    }).then(response => response.json())
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
