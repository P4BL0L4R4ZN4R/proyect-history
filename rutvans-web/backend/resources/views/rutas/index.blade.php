@extends('adminlte::page')

@section('title', 'Rutas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-route me-2"></i> Gestión de Rutas
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">Administra las rutas de transporte del sistema</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm" style="border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
            <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <i class="fas fa-map-marked-alt me-2"></i> Rutas de Transporte
            </h3>
            <button class="btn btn-light" data-toggle="modal" data-target="#createModal" style="font-weight: 600; border-radius: 8px; padding: 0.5rem 1.5rem;">
                <i class="fas fa-plus text-primary"></i> Nueva Ruta
            </button>
        </div>
        
        <div class="card-body" style="padding: 2rem;">
            <div class="table-responsive">
                <table class="table table-hover table-striped" style="border-radius: 8px; overflow: hidden;">
                    <thead style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                        <tr>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-hashtag me-1"></i> ID</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-map-marker-alt me-1"></i> Ubicación Inicio</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-flag-checkered me-1"></i> Ubicación Final</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i class="fas fa-cogs me-1"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rutas as $ruta)
                            <tr style="transition: all 0.3s ease;">
                                <td>
                                    <span class="badge bg-primary" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">{{ $ruta->id }}</span>
                                </td>
                                <td style="font-weight: 500;">
                                    <i class="fas fa-map-pin text-success me-2"></i>
                                    {{ $ruta->ubicacionInicio->locality ?? 'N/A' }}
                                </td>
                                <td style="font-weight: 500;">
                                    <i class="fas fa-flag text-danger me-2"></i>
                                    {{ $ruta->ubicacionFinal->locality ?? 'N/A' }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal{{ $ruta->id }}" style="border-radius: 6px;">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        
                                        <form action="{{ route('rutas.destroy', $ruta->id) }}" method="POST" class="d-inline" 
                                            onsubmit="return confirm('¿Eliminar esta ruta?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit" style="border-radius: 6px;">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                    <!-- Modal Editar -->
                    <div class="modal fade" id="editModal{{ $ruta->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('rutas.update', $ruta->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 0;">
                                        <h5 class="modal-title" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                            <i class="fas fa-pen me-2"></i>Editar Ruta
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 1;">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" style="padding: 2rem;">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Ubicación Inicio</label>
                                            <select name="id_location_s" class="form-control" required style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                                                @foreach($localities as $locality)
                                                    <option value="{{ $locality->id }}" {{ $locality->id == $ruta->ubicacionInicio->id ? 'selected' : '' }}>
                                                        {{ $locality->locality }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Ubicación Final</label>
                                            <select name="id_location_f" class="form-control" required style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                                                @foreach($localities as $locality)
                                                    <option value="{{ $locality->id }}" {{ $locality->id == $ruta->ubicacionFinal->id ? 'selected' : '' }}>
                                                        {{ $locality->locality }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="padding: 1.5rem;">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500;">Cancelar</button>
                                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #ff6600, #e55a00); border-color: #ff6600; color: white; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 600; font-family: 'Poppins', sans-serif;">Guardar cambios</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="fas fa-route fa-2x mb-2" style="opacity: 0.5;"></i>
                            <br>No hay rutas registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('rutas.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 0;">
                    <h5 class="modal-title" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <i class="fas fa-plus-circle me-2"></i>Crear Nueva Ruta
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Ubicación Inicio</label>
                        <select name="id_location_s" class="form-control" required style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                            @foreach($localities as $locality)
                                <option value="{{ $locality->id }}">{{ $locality->locality }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;">Ubicación Final</label>
                        <select name="id_location_f" class="form-control" required style="border: 2px solid #ff6600; border-radius: 8px; padding: 0.75rem; font-size: 0.95rem;">
                            @foreach($localities as $locality)
                                <option value="{{ $locality->id }}">{{ $locality->locality }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500;">Cancelar</button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #ff6600, #e55a00); border-color: #ff6600; color: white; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 600; font-family: 'Poppins', sans-serif;">Guardar Ruta</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
