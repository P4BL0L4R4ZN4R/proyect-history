@extends('adminlte::page')

@section('title', 'Asignación de Rutas a Unidades')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-route me-2"></i> Asignación de Rutas a Unidades
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">Gestiona las asignaciones de rutas y unidades de transporte</p>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">
@endsection

@section('content')
<div class="card shadow-sm" style="border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
        <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            <i class="fas fa-map-marked-alt me-2"></i> Asignaciones Registradas
        </h3>
        <button class="btn btn-light" data-toggle="modal" data-target="#createRutaUnidadModal" style="font-weight: 600; border-radius: 8px; padding: 0.5rem 1.5rem;">
            <i class="fas fa-plus text-primary"></i> Nueva Asignación
        </button>
    </div>
    
    <div class="card-body" style="padding: 2rem;">
        <div class="table-responsive">
            <table id="rutaUnidadTable" class="table table-hover table-striped" style="border-radius: 8px; overflow: hidden;">
                <thead style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                    <tr>
                        <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Ruta</th>
                        <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Unidad - Chofer</th>
                        <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Localidad Intermedia</th>
                        <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Precio</th>
                        <th class="text-center fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rutaUnidades as $item)
                        <tr style="transition: all 0.3s ease;">
                            <td style="font-weight: 500;">{{ optional($item->ruta->source)->locality }} → {{ optional($item->ruta->destination)->locality }}</td>
                            <td>{{ optional($item->driverUnit->unit)->plate }} - {{ optional($item->driverUnit->driver->user)->name }}</td>
                            <td>{{ optional($item->intermediateLocation)->locality ?? 'N/A' }}</td>
                            <td class="fw-bold" style="color: #28a745; font-size: 1.1rem;">${{ number_format($item->price, 2) }}</td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm me-1" data-toggle="modal" data-target="#editModal{{ $item->id }}" style="border-radius: 6px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('rutas-unidades.update', $item->id) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta asignación?')" style="border-radius: 6px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2" style="opacity: 0.5;"></i>
                                <br>No hay asignaciones registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modales de Edición -->
@foreach($rutaUnidades as $item)
<div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('rutas-unidades.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 0;">
                    <h5 class="modal-title" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <i class="fas fa-edit me-2"></i> Editar Asignación
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Ruta</label>
                        <select name="id_route" class="form-control" style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                            @foreach($rutas as $ruta)
                                <option value="{{ $ruta->id }}" {{ $item->id_route == $ruta->id ? 'selected' : '' }}>
                                    {{ optional($ruta->source)->locality }} → {{ optional($ruta->destination)->locality }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Unidad y Chofer</label>
                        <select name="id_driver_unit" class="form-control" style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                            @foreach($driverUnits as $du)
                                <option value="{{ $du->id }}" {{ $item->id_driver_unit == $du->id ? 'selected' : '' }}>
                                    {{ optional($du->unit)->plate }} - {{ optional($du->driver->user)->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Localidad Intermedia</label>
                        <select name="intermediante_location_id" class="form-control" style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                            <option value="">Sin localidad</option>
                            @foreach($localidades as $loc)
                                <option value="{{ $loc->id }}" {{ $item->intermediante_location_id == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->locality }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Precio</label>
                        <input type="number" step="0.01" class="form-control" name="price" value="{{ $item->price }}" required style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                    </div>
                </div>
                <div class="modal-footer" style="padding: 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500;">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #ff6600, #e55a00); border-color: #ff6600; color: white; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 600; font-family: 'Poppins', sans-serif;">
                        <i class="fas fa-save me-1"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal crear -->
<div class="modal fade" id="createRutaUnidadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('rutaunidad.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 0;">
                    <h5 class="modal-title" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <i class="fas fa-plus me-2"></i> Nueva Asignación
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <!-- Ruta -->
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Ruta</label>
                        <select name="id_route" class="form-control" required style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                            <option value="">Seleccione una ruta</option>
                            @foreach($rutas as $ruta)
                                <option value="{{ $ruta->id }}" {{ old('id_route') == $ruta->id ? 'selected' : '' }}>
                                    {{ optional($ruta->source)->locality }} → {{ optional($ruta->destination)->locality }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Unidad y Chofer -->
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Unidad y Chofer</label>
                        <select name="id_driver_unit" class="form-control" required style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                            <option value="">Seleccione unidad y chofer</option>
                            @foreach($driverUnits as $du)
                                <option value="{{ $du->id }}" {{ old('id_driver_unit') == $du->id ? 'selected' : '' }}>
                                    {{ optional($du->unit)->plate }} - {{ optional($du->driver->user)->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Localidad Intermedia -->
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Localidad Intermedia (opcional)</label>
                        <select name="intermediante_location_id" class="form-control" style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                            <option value="">Sin localidad</option>
                            @foreach($localidades as $loc)
                                <option value="{{ $loc->id }}" {{ old('intermediante_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->locality }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Precio -->
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Precio</label>
                        <input type="number" step="0.01" class="form-control" name="price" required value="{{ old('price') }}" style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                    </div>
                </div>

                <div class="modal-footer" style="padding: 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500;">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #ff6600, #e55a00); border-color: #ff6600; color: white; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 600; font-family: 'Poppins', sans-serif;">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#rutaUnidadTable').DataTable({
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
});
</script>
@endpush

@endsection
