@extends('adminlte::page')

@section('title', 'Conductores')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3"
        style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-building me-2"></i> Gestión de Conductores
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">Administra los conductores del sistema de transporte
            </p>
        </div>

        <a href="{{ route('coordinator.dashboard') }}" class="btn btn-light btn-sm" style="color: #e55a00; font-weight: 500;">
            <i class="fas fa-arrow-left me-1"></i> Volver al Panel
        </a>
    </div>

@endsection

@section('content')
    <div class="card shadow-sm"
        style="border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
        <div class="card-header d-flex justify-content-between align-items-center"
            style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
            <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <i class="fas fa-steering-wheel me-2"></i> Conductores Registrados
            </h3>
            <div class="ms-auto">
                <button type="button" class="btn btn-light btn-sm" style="color: #e55a00; font-weight: 500;"
                    data-bs-toggle="modal" data-bs-target="#modalCreateDriver">
                    <i class="fas fa-plus me-1"></i> Nuevo Conductor
                </button>
            </div>
        </div>

        <div class="card-body" style="padding: 2rem;">
            <div class="table-responsive">
                <table id="driversTable" class="table table-hover table-striped"
                    style="border-radius: 8px; overflow: hidden;">
                    <thead style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                        <tr>
                            <th><i class="fas fa-hashtag me-1"></i> ID</th>
                            <th><i class="fas fa-user me-1"></i> Usuario</th>
                            <th><i class="fas fa-envelope me-1"></i> Correo</th>
                            <th><i class="fas fa-cogs me-1"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drivers as $driver)
                            <tr>
                                <td>
                                    <span class="badge bg-primary"
                                        style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">{{ $driver->id }}</span>
                                </td>
                                <td>
                                    <i class="fas fa-user-circle text-muted me-2"></i>
                                    {{ optional($driver->user)->name ?? 'Sin usuario' }}
                                </td>
                                <td>
                                    <i class="fas fa-at text-muted me-2"></i>
                                    {{ optional($driver->user)->email ?? 'Sin correo' }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-warning btn-sm btn-edit-driver"
                                            data-id="{{ $driver->id }}" data-name="{{ optional($driver->user)->name ?? '' }}"
                                            data-email="{{ optional($driver->user)->email ?? '' }}"
                                            data-license="{{ $driver->license ?? '' }}"
                                            data-photo-path="{{ optional($driver->documents()->where('type', 'foto')->first())->photo_path ?? '' }}"
                                            style="border-radius: 6px; font-weight: 500;">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        <form action="{{ route('drivers.destroy', $driver) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Seguro que quieres eliminar este conductor?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit"
                                                style="border-radius: 6px; font-weight: 500;">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="alert alert-info text-center py-5"
                                        style="border: 2px dashed #17a2b8; background-color: rgba(23, 162, 184, 0.1);">
                                        <i class="fas fa-car-side mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
                                        <h4>No hay conductores registrados</h4>
                                        <p class="mb-0">Agrega el primer conductor para comenzar a gestionar el transporte
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('empleados.drivers.create')
    @include('empleados.drivers.edit')
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .content-wrapper {
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('js')

    {{-- Script Sweet Alert --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script type="module" src="{{ asset('js/employees/drivers/main.js') }}"></script>
@endsection