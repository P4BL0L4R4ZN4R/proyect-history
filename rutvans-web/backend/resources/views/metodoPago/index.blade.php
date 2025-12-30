@extends('adminlte::page')

@section('title', 'Métodos de Pago')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-money-check-alt me-2"></i> Métodos de Pago
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">Administra los métodos de pago disponibles en el sistema</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm" style="border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
            <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <i class="fas fa-credit-card me-2"></i> Gestión de Métodos de Pago
            </h3>
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#crearMetodoModal" style="font-weight: 600; border-radius: 8px; padding: 0.5rem 1.5rem;">
                <i class="fas fa-plus-circle text-primary"></i> Nuevo Método
            </button>
        </div>

        <div class="card-body" style="padding: 2rem;">
            <div class="table-responsive">
                <table id="tablaMetodos" class="table table-hover table-striped" style="border-radius: 8px; overflow: hidden;">
                    <thead style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                        <tr>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-hashtag me-1"></i> ID</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-tag me-1"></i> Nombre</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-cogs me-1"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($metodos as $metodo)
                            <tr style="transition: all 0.3s ease;">
                                <td>
                                    <span class="badge bg-primary" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">{{ $metodo->id }}</span>
                                </td>
                                <td style="font-weight: 500;">
                                    <i class="fas fa-money-check-alt text-muted me-2"></i>
                                    {{ $metodo->name }}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-info btn-sm" 
                                            data-bs-toggle="modal"
                                            data-bs-target="#editarMetodoModal"
                                            data-id="{{ $metodo->id }}"
                                            data-nombre="{{ $metodo->name }}"
                                            style="border-radius: 6px;">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        <form method="POST" action="{{ route('metodoPago.destroy', $metodo->id) }}"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este método de pago?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit" style="border-radius: 6px;">
                                                <i class="fas fa-trash-alt"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Crear -->
    <div class="modal fade" id="crearMetodoModal" tabindex="-1" aria-labelledby="crearMetodoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('metodoPago.store') }}" class="needs-validation" novalidate id="formCrearMetodo">
                    @csrf
                    <div class="modal-header" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 0;">
                        <h5 class="modal-title" id="crearMetodoModalLabel" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            <i class="fas fa-plus-circle me-2"></i> Agregar Método de Pago
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar" style="opacity: 1;"></button>
                    </div>
                    <div class="modal-body" style="padding: 2rem;">
                        <div class="form-group mb-3">
                            <label for="nombreMetodo" class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">
                                <i class="fas fa-tag me-1"></i> Nombre del método
                            </label>
                            <input type="text" class="form-control" id="nombreMetodo" name="name" required
                                placeholder="Ej: Efectivo, Tarjeta de Crédito, Transferencia"
                                style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                            <div class="invalid-feedback">Este campo es obligatorio.</div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding: 1.5rem;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #ff6600, #e55a00); border-color: #ff6600; color: white; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 600; font-family: 'Poppins', sans-serif;">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="editarMetodoModal" tabindex="-1" aria-labelledby="editarMetodoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="" class="needs-validation" novalidate id="formEditarMetodo">
                    @csrf
                    @method('PUT')
                    <div class="modal-header" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 0;">
                        <h5 class="modal-title" id="editarMetodoModalLabel" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            <i class="fas fa-edit me-2"></i> Editar Método de Pago
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar" style="opacity: 1;"></button>
                    </div>
                    <div class="modal-body" style="padding: 2rem;">
                        <input type="hidden" name="id" id="editarMetodoId">
                        <div class="form-group mb-3">
                            <label for="editarNombreMetodo" class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">
                                <i class="fas fa-tag me-1"></i> Nombre del método
                            </label>
                            <input type="text" class="form-control" id="editarNombreMetodo" name="name" required
                                placeholder="Ej: Efectivo, Tarjeta de Crédito, Transferencia"
                                style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                            <div class="invalid-feedback">Este campo es obligatorio.</div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding: 1.5rem;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #ff6600, #e55a00); border-color: #ff6600; color: white; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 600; font-family: 'Poppins', sans-serif;">
                            <i class="fas fa-save"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
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
@endsection

@section('js')
    <!-- Scripts base -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#tablaMetodos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                responsive: true,
                autoWidth: false
            });

            // Editar: llenar formulario
            const editarModal = document.getElementById('editarMetodoModal');
            editarModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');

                const form = document.getElementById('formEditarMetodo');
                form.action = `/metodoPago/${id}`;
                document.getElementById('editarNombreMetodo').value = nombre;
            });

            // Validación formularios
            ['formCrearMetodo', 'formEditarMetodo'].forEach(function (formId) {
                const form = document.getElementById(formId);
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        });
    </script>

    {{-- Mensajes con SweetAlert2 --}}
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Entendido'
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Excelente!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        </script>
    @endif

    @if (session('updated'))
        <script>
            Swal.fire({
                icon: 'info',
                title: '¡Actualizado!',
                text: '{{ session('updated') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        </script>
    @endif

    @if (session('deleted'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: '¡Eliminado!',
                text: '{{ session('deleted') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        </script>
    @endif
@endsection
