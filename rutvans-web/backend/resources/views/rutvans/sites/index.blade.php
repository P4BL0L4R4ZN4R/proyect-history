@extends('adminlte::page')

@section('title', 'Gestión de Sitios/Terminales Rutvans')

@section('adminlte_css_pre')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3"
        style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-building me-2"></i> Gestión de Sitios/Terminales
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">Administra los sitios/terminales de las
                empresas/sindicatos</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm" style="border: none; border-radius: 12px;">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
                        <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            <i class="fas fa-building me-2"></i> Sitios/Terminales Registrados
                        </h3>
                        <div class="ms-auto">
                            <button type="button" class="btn" style="background-color: #ff6600; color: white;"
                                data-bs-toggle="modal" data-bs-target="#createSiteModal" style="font-weight: 600;">
                                <i class="fas fa-plus me-1"></i> Nuevo Sitio/Terminal
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert"
                                style="border-radius: 8px;">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Cerrar"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert"
                                style="border-radius: 8px;">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Cerrar"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="sitesTable"
                                style="border-radius: 8px; overflow: hidden;">
                                <thead style="background: linear-gradient(135deg, #6c757d, #495057); color: white;">
                                    <tr>
                                        <th>#</th>
                                        <th>Empresa/Sindicato</th>
                                        <th>Nombre del Sitio</th>
                                        <th>Ruta</th>
                                        <th>Localidad</th>
                                        <th>Teléfono</th>
                                        <th>Estado</th>
                                        <th>Usuarios</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sites as $site)
                                        <tr>
                                            <td>{{ $site->id }}</td>
                                            <td>
                                                <strong>{{ $site->company->name ?? 'N/A' }}</strong>
                                                @if ($site->company)
                                                    <br><small
                                                        class="text-muted">{{ $site->company->business_name }}</small>
                                                @endif
                                            </td>
                                            <td><strong>{{ $site->name }}</strong></td>
                                            <td>
                                                @if ($site->route_name)
                                                    <strong>{{ $site->route_name }}</strong><br>
                                                    <small class="text-muted">Ruta principal del sitio</small>
                                                @else
                                                    <span class="text-muted">Sin ruta principal</span><br>
                                                    <small class="text-muted">Se crearán rutas específicas después</small>
                                                @endif
                                            </td>
                                            <td>{{ $site->locality->locality ?? 'N/A' }}</td>
                                            <td>{{ $site->phone }}</td>
                                            <td>
                                                @if ($site->status === 'active')
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-info">{{ $site->users->count() }}</span></td>
                                            <td>
                                                <div class="btn-group" role="group"
                                                    aria-label="Acciones sitio {{ $site->name }}">
                                                    <a href="{{ route('clients.show', $site->id) }}"
                                                        class="btn btn-info btn-sm" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit-site"
                                                        data-id="{{ $site->id }}"
                                                        data-company_id="{{ $site->company_id }}"
                                                        data-name="{{ $site->name }}"
                                                        data-route_name="{{ $site->route_name }}"
                                                        data-locality_id="{{ $site->locality_id }}"
                                                        data-address="{{ $site->address }}"
                                                        data-phone="{{ $site->phone }}" data-status="{{ $site->status }}"
                                                        title="Editar" aria-label="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="deleteSite({{ $site->id }}, '{{ addslashes($site->name) }}')"
                                                        title="Eliminar" aria-label="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">
                                                <div class="py-4">
                                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No hay sitios/terminales registrados</h5>
                                                    <p class="text-muted">Comienza agregando tu primer sitio/terminal.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($sites->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $sites->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('rutvans.sites.create')
    @include('rutvans.sites.edit')

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
    <script type="module" src="{{ asset('js/sites/main.js') }}"></script>
    {{-- Script de los alerts --}}
    <script>
        window.alerts = {
            success: @json(session('success') ?? null),
            errors: @json($errors->any() ? $errors->all() : []),
        };
    </script>
@stop
