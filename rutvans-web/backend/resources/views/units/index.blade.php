<!-- resources/views/units/index.blade.php -->
@extends('adminlte::page')

@section('title', 'Unidades')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />
@endsection
@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-shuttle-van me-2"></i> Gestión de Unidades
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">Administra las unidades de transporte del sistema</p>
        </div>
    </div>
@endsection

@section('content')
<div class="card shadow-sm" style="border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
        <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            <i class="fas fa-bus me-2"></i> Unidades Registradas
        </h3>
        <button class="btn btn-light" data-toggle="modal" data-target="#createUnitModal" style="font-weight: 600; border-radius: 8px; padding: 0.5rem 1.5rem;">
            <i class="fas fa-plus text-primary"></i> Nueva Unidad
        </button>
    </div>
    
    <div class="card-body" style="padding: 2rem;">
        <!-- Filtros y búsqueda -->
        <form method="GET" action="{{ route('units.index') }}" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">
                            <i class="fas fa-search me-1"></i> Buscar unidad
                        </label>
                        <input type="text" name="search" class="form-control" 
                            placeholder="Buscar por nombre o placa" value="{{ request('search') }}"
                            style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-info" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500;">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('units.index') }}" class="btn btn-secondary" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500;">
                            <i class="fas fa-eraser"></i> Limpiar
                        </a>
                        <a href="{{ route('exports.unitsexportexcel', ['search' => request('search')]) }}" class="btn btn-success" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500;">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        <a href="{{ route('exports.unitsexportpdf', ['search' => request('search')]) }}" class="btn btn-danger" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500;">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
        </form>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="border-radius: 8px; border-left: 4px solid #28a745;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 8px; border-left: 4px solid #dc3545;">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card mt-3" style="border: none; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div class="card-body" style="padding: 2rem;">
            <div class="table-responsive">
                <table id="unitsTable" class="table table-hover table-striped" style="border-radius: 8px; overflow: hidden;">
                    <thead style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                    <tr>
                        <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Placa</th>
                        <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Capacidad</th>
                        <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Foto</th>
                        <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Choferes Asignados</th>
                        <th class="text-center fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                    <tr style="transition: all 0.3s ease;">
                        <td style="font-weight: 600; color: #495057;">{{ $unit->plate }}</td>
                        <td><span class="badge bg-info">{{ $unit->capacity }} personas</span></td>
                        <td>
                            @if($unit->photo && \App\Helpers\StorageHelper::fileExists($unit->photo))
                                <img src="{{ \App\Helpers\StorageHelper::getPublicUrl($unit->photo) }}"
                                    alt="Foto unidad"
                                    class="img-thumbnail"
                                    width="80" style="border-radius: 8px;">
                            @else
                                <span class="text-muted">Sin imagen</span>
                            @endif
                        </td>
                        <td>
                            @if($unit->drivers->count() > 0)
                                @foreach($unit->drivers as $driver)
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded" style="background-color: #f8f9fa; border-left: 3px solid #ff6600;">
                                        <div>
                                            <strong>{{ $driver->user->name }}</strong>
                                            <span class="badge bg-info ms-2">{{ $driver->pivot->status }}</span>
                                        </div>
                                        <form action="{{ route('units.removeDriver', [$unit->id, $driver->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('¿Quitar conductor?')"
                                                    style="border-radius: 6px; font-weight: 500;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-muted">Sin conductores asignados</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-1"
                                    data-toggle="modal"
                                    data-target="#editUnitModal{{ $unit->id }}"
                                    style="border-radius: 6px; font-weight: 500;">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button class="btn btn-success btn-sm me-1"
                                    data-toggle="modal"
                                    data-target="#assignDriverModal{{ $unit->id }}"
                                    style="border-radius: 6px; font-weight: 500;">
                                <i class="fas fa-user-plus"></i>
                            </button>

                            <form action="{{ route('units.destroy', $unit->id) }}"
                                method="POST"
                                style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Eliminar unidad?')"
                                        style="border-radius: 6px; font-weight: 500;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay unidades registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para crear unidad -->
<div class="modal fade" id="createUnitModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('units.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i> Registrar Nueva Unidad
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="plate" class="font-weight-bold text-dark">Placa</label>
                        <input type="text" class="form-control" id="plate" name="plate" required style="border-color: #ff6600;">
                    </div>
                    <div class="form-group">
                        <label for="capacity" class="font-weight-bold text-dark">Capacidad (personas)</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" min="1" required style="border-color: #ff6600;">
                    </div>
                    <div class="form-group">
                        <label for="photo" class="font-weight-bold text-dark">Foto de la unidad (opcional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="photo" name="photo">
                            <label class="custom-file-label" for="photo">Seleccionar archivo</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background-color: #ff6600; border-color: #ff6600; color: white;">
                        <i class="fas fa-save me-1"></i> Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales para editar y asignar conductores -->
@foreach($units as $unit)
<!-- Modal Editar Unidad -->
<div class="modal fade" id="editUnitModal{{ $unit->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('units.update', $unit->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header" style="background: linear-gradient(135deg, #ffc107, #e0a800); color: #212529;">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i> Editar Unidad: {{ $unit->plate }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="plate" class="font-weight-bold text-dark">Placa</label>
                        <input type="text" class="form-control" name="plate" value="{{ $unit->plate }}" required style="border-color: #ff6600;">
                    </div>
                    <div class="form-group">
                        <label for="capacity" class="font-weight-bold text-dark">Capacidad (personas)</label>
                        <input type="number" class="form-control" name="capacity" value="{{ $unit->capacity }}" min="1" required style="border-color: #ff6600;">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Foto actual:</label>
                        @if($unit->photo && \App\Helpers\StorageHelper::fileExists($unit->photo))
                            <img src="{{ \App\Helpers\StorageHelper::getPublicUrl($unit->photo) }}"
                                alt="Foto actual"
                                class="img-fluid mb-2 rounded"
                                style="max-height: 150px">
                        @else
                            <p class="text-muted">Sin imagen registrada</p>
                        @endif
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="photo">
                            <label class="custom-file-label">Cambiar foto...</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background-color: #ffc107; border-color: #ffc107; color: #212529;">
                        <i class="fas fa-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Asignar Chofer -->
<div class="modal fade" id="assignDriverModal{{ $unit->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('units.assignDriver', $unit->id) }}" method="POST">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #1e7e34); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i> Asignar Chofer a: {{ $unit->plate }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="driver_id" class="font-weight-bold text-dark">Seleccionar Chofer</label>
                        <select class="form-control select2" id="driver_id" name="driver_id" required style="border-color: #ff6600;">
                            <option value="">-- Seleccione un conductor --</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status" class="font-weight-bold text-dark">Estado de la asignación</label>
                        <select class="form-control" id="status" name="status" required style="border-color: #ff6600;">
                            <option value="Activo">Activo</option>
                            <option value="En mantenimiento">En mantenimiento</option>
                            <option value="Inactivo">Inactivo</option>
                            <option value="Reserva">Reserva</option>
                        </select>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold" style="color: #ff6600;">Choferes Asignados</h6>
                    @if($unit->drivers->count() > 0)
                        @foreach($unit->drivers as $driver)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <div>
                                    <strong>{{ $driver->user->name }}</strong>
                                    <span class="badge badge-info ml-2">{{ $driver->pivot->status }}</span>
                                </div>
                                <form action="{{ route('units.removeDriver', [$unit->id, $driver->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Quitar conductor?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No hay choferes asignados.</p>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background-color: #28a745; border-color: #28a745; color: white;">
                        <i class="fas fa-user-plus me-1"></i> Asignar Chofer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicializar DataTable con configuración simplificada
        $('#unitsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
            },
            "pageLength": 25,
            "order": [[0, 'asc']],
            "columnDefs": [
                { "orderable": false, "targets": [4] }
            ]
        });

        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Seleccione un conductor'
        });

        // Manejo de archivos
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>
@endpush

@endsection
            
