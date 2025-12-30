@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-4" style="background: linear-gradient(135deg, #ff6600 0%, #ff8533 100%); padding: 1.5rem; border-radius: 15px; box-shadow: 0 8px 25px rgba(255, 102, 0, 0.15); animation: fadeInDown 0.6s ease-out;">
        <div>
            <h1 class="text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 2rem;">
                <i class="fas fa-clock me-3"></i> Gestión de Horarios
            </h1>
            <p class="text-white mb-0" style="font-family: 'Poppins', sans-serif; opacity: 0.9; font-size: 1.1rem;">
                Administra los horarios del sistema de transporte
            </p>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 15px 15px 0 0; padding: 1.5rem;">
            <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <i class="fas fa-business-time me-2"></i> Horarios del Sistema
            </h3>
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createHorarioModal" style="font-weight: 600; border-radius: 8px; padding: 0.5rem 1.5rem;">
                <i class="fas fa-plus text-primary"></i> Crear Horario
            </button>
        </div>

        <div class="card-body" style="padding: 2rem;">
            <div class="table-responsive">
                <table id="horariosTable" class="table table-hover table-striped align-middle" style="border-radius: 8px; overflow: hidden;">
                    <thead style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                        <tr>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-hashtag me-1"></i> ID</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-calendar-day me-1"></i> Día</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-clock me-1"></i> Hora Llegada</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-clock me-1"></i> Hora Salida</th>
                        <th class="fw-bold text-center" style="color: #495057; font-family: 'Poppins', sans-serif;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($horarios as $horario)
                        <tr style="transition: all 0.3s ease;">
                            <td><span class="badge bg-primary" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">{{ $horario->id }}</span></td>
                            <td style="font-weight: 500;">{{ e($horario->dia) }}</td>
                            <td style="font-weight: 500;">{{ e($horario->horaLlegada) }}</td>
                            <td style="font-weight: 500;">{{ e($horario->horaSalida) }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-info rounded-circle" data-bs-toggle="modal"
                                        data-bs-target="#editHorarioModal"
                                        data-id="{{ $horario->id }}"
                                        data-dia="{{ $horario->dia }}"
                                        data-llegada="{{ $horario->horaLlegada }}"
                                        data-salida="{{ $horario->horaSalida }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-danger rounded-circle"
                                        onclick="confirmDeleteHorario({{ $horario->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('horarios.create')
    @include('horarios.edit')
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        .transition {
            transition: all 0.3s ease-in-out;
        }
        .btn-outline-light:hover {
            background-color: #ffffff !important;
            color: #4da3ff !important;
            box-shadow: 0 0 0 2px #4da3ff50;
        }
        .btn-outline-info:hover {
            background-color: #4da3ff !important;
            color: #fff !important;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545 !important;
            color: #fff !important;
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
        $(document).ready(function () {
            $('#horariosTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });

            $('#editHorarioModal').on('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const modal = $(this);
                modal.find('#editHorarioId').val(button.getAttribute('data-id'));
                modal.find('#editHorarioDia').val(button.getAttribute('data-dia'));
                modal.find('#editHorarioLlegada').val(button.getAttribute('data-llegada'));
                modal.find('#editHorarioSalida').val(button.getAttribute('data-salida'));
            });
        });

        function confirmDeleteHorario(id) {
            Swal.fire({
                title: '¿Eliminar este horario?',
                text: "No podrás revertir esta acción.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#4da3ff',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/horarios/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    }).catch(() => {
                        Swal.fire('Error', 'No se pudo eliminar el horario.', 'error');
                    });
                }
            });
        }
    </script>

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ session('success') }}',
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif
@endsection
