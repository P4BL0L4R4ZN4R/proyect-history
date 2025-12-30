@extends('adminlte::page')

@section('title', 'Detalle del Sitio - ' . $site->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3"
        style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-eye me-2"></i> Detalle del Sitio
            </h1>
            <p class="mb-0" style="opacity: 0.9;">Información detallada y asignación de coordinador</p>
        </div>
        <div class="mb-3">
            <a href="{{ route('clients.index') }}" class="btn" style="background-color: #963c00; color: white;">
                <i class="fas fa-arrow-left me-1"></i> Regresar a Sitios
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Datos del sitio --}}
        <div class="card shadow-sm rounded mb-4">
            <div class="card-body">
                <h4><i class="fas fa-building me-2"></i>{{ $site->name }}</h4>
                <hr>
                <dl class="row">
                    <dt class="col-sm-3">Empresa</dt>
                    <dd class="col-sm-9">{{ $site->company->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Localidad</dt>
                    <dd class="col-sm-9">{{ $site->locality->locality ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Teléfono</dt>
                    <dd class="col-sm-9">{{ $site->phone ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Ruta Principal</dt>
                    <dd class="col-sm-9">{{ $site->route_name ?? 'Sin ruta principal' }}</dd>

                    <dt class="col-sm-3">Estado</dt>
                    <dd class="col-sm-9">
                        @if ($site->status === 'active')
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        {{-- Datos del coordinador asignado --}}
        <div class="card shadow-sm rounded">
            <div class="card-header d-flex justify-content-between align-items-center"
                style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px 8px 0 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h5 class="mb-0" style="flex-grow: 1;">
                    <i class="fas fa-user-tie me-2"></i> Coordinador Asignado
                </h5>

                @unless ($assignedCoordinator)
                    <button id="btnCrearCoordinador" class="btn btn-sm" data-bs-toggle="modal"
                        data-bs-target="#createCoordinatorModal"
                        style="background-color: #963c00; color: white; border: none; font-weight: 600;">
                        <i class="fas fa-plus me-1"></i> Crear Coordinador
                    </button>
                @else
                    @php
                        $coordinatorPhoto = $assignedCoordinator
                            ? $assignedCoordinator->photo ?? ($assignedCoordinator->profile_photo_path ?? null)
                            : null;
                        $photoExists =
                            !empty($coordinatorPhoto) && \App\Helpers\StorageHelper::fileExists($coordinatorPhoto);
                        $assignedCoordinatorData = $assignedCoordinator
                            ? array_merge($assignedCoordinator->toArray(), [
                                'site_id' => $site->id,
                                'photo_url' => $photoExists
                                    ? \App\Helpers\StorageHelper::getPublicUrl($coordinatorPhoto)
                                    : null,
                            ])
                            : null;
                    @endphp

                    <button class="btn btn-sm btnEditarCoordinador"
                        style="background-color: #963c00; color: white; border: none; font-weight: 600;"
                        data-user='@json($assignedCoordinatorData)'>
                        <i class="fas fa-edit me-1"></i> Editar Coordinador
                    </button>
                @endunless

            </div>
            <div class="card-body">
                @if ($assignedCoordinator)
                    <div class="row">
                        <div class="col-md-3">
                            @php
                                $coordinatorPhoto =
                                    $assignedCoordinator->photo_path ?? ($assignedCoordinator->photo_path ?? null);
                                $photoExists =
                                    !empty($coordinatorPhoto) &&
                                    \App\Helpers\StorageHelper::fileExists($coordinatorPhoto);
                            @endphp

                            @if ($photoExists)
                                <img src="{{ \App\Helpers\StorageHelper::getPublicUrl($coordinatorPhoto) }}"
                                    alt="Foto del coordinador" class="img-thumbnail"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center text-white"
                                    style="width: 100px; height: 100px; border-radius: 4px;">
                                    <i class="fas fa-user fa-2x"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <dl class="row">
                                <dt class="col-sm-4">Nombre Completo</dt>
                                <dd class="col-sm-8">{{ $assignedCoordinator->name }}</dd>

                                <dt class="col-sm-4">Correo Electrónico</dt>
                                <dd class="col-sm-8">{{ $assignedCoordinator->email }}</dd>

                                <dt class="col-sm-4">Dirección</dt>
                                <dd class="col-sm-8">{{ $assignedCoordinator->address }}</dd>

                                <dt class="col-sm-4">Teléfono</dt>
                                <dd class="col-sm-8">{{ $assignedCoordinator->phone_number }}</dd>

                                <dt class="col-sm-4">Código Empleado</dt>
                                <dd class="col-sm-8">{{ $assignedCoordinator->employee_code ?? 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>
                @else
                    <p class="text-muted">No hay coordinador asignado a este sitio.</p>
                @endif
            </div>
        </div>

        @include('rutvans.sites.asignar.create')
        @include('rutvans.sites.asignar.edit')
    </div>
@endsection

@section('css')
    {{-- Aquí puedes agregar hojas de estilo específicas --}}
@stop

@section('js')

    {{-- Script Sweet Alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 JS Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Script principal de la vista --}}
    <script type="module" src="{{ asset('js/sites/asignar/main.js') }}"></script>
    {{-- Script de los alerts --}}
    <script>
        window.alerts = {
            success: @json(session('success') ?? null),
            errors: @json($errors->any() ? $errors->all() : []),
        };
    </script>

@stop
