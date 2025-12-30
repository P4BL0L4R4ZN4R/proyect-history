@extends('adminlte::page')

@section('title', 'Gestión de Empresas/Sindicatos')

@section('adminlte_css_pre')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3"
        style="background: linear-gradient(135deg,#208e3a, #28a745); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-industry me-2"></i> Gestión de Empresas/Sindicatos
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">Administra las empresas y sindicatos que contratan
                Rutvans</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm" style="border: none; border-radius: 12px;">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(135deg, #208e3a, #28a745); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
                        <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            <i class="fas fa-building me-2"></i> Empresas/Sindicatos Registrados
                        </h3>
                        <div class="ml-auto">
                            <button type="button" class="btn" style="background-color: #208e3a; color: white;"
                                data-bs-toggle="modal" data-bs-target="#createCompanyModal" style="font-weight: 600;"
                                aria-label="Nueva Empresa/Sindicato">
                                <i class="fas fa-plus mr-1"></i>
                                Nueva Empresa/Sindicato
                            </button>
                        </div>
                    </div>

                    <div class="card-body">

                        {{-- Mensajes de éxito y error --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert"
                                style="border-radius: 8px;">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert"
                                style="border-radius: 8px;">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ session('error') }}
                                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- Tabla sin DataTables para evitar conflicto con paginación Laravel --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover nowrap"
                                style="border-radius: 8px; overflow: hidden; width: 100%;" id="companiesTable">
                                <thead style="background: linear-gradient(135deg, #6c757d, #495057); color: white;">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Razón Social</th>
                                        <th>RFC</th>
                                        <th>Localidad</th>
                                        <th>Admin</th>
                                        <th>Teléfono</th>
                                        <th>Estado</th>
                                        <th>Sitios/Rutas</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($companies as $company)
                                        <tr>
                                            <td>{{ $company->id }}</td>
                                            <td><strong>{{ $company->name }}</strong></td>
                                            <td>{{ $company->business_name ?: 'N/A' }}</td>
                                            <td>{{ $company->rfc ?: 'N/A' }}</td>
                                            <td>{{ $company->locality->locality ?? 'N/A' }}</td>
                                            <td>{{ $company->admin_name }}</td>
                                            <td>{{ $company->phone }}</td>
                                            <td>
                                                @if ($company->status === 'active')
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-info">{{ $company->sites->count() }}</span></td>
                                            <td>
                                                <div class="btn-group" role="group"
                                                    aria-label="Acciones Empresa {{ $company->name }}">
                                                    <button type="button" class="btn btn-info btn-sm"
                                                        onclick="viewCompany({{ $company->id }})" title="Ver detalles"
                                                        aria-label="Ver detalles de {{ $company->name }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit-company"
                                                        data-id="{{ $company->id }}"
                                                        data-name="{{ $company->name }}"
                                                        data-business_name="{{ $company->business_name }}"
                                                        data-rfc="{{ $company->rfc }}"
                                                        data-locality_id="{{ $company->locality_id }}"
                                                        data-address="{{ $company->address }}"
                                                        data-phone="{{ $company->phone }}"
                                                        data-email="{{ $company->email }}"
                                                        data-status="{{ $company->status }}"
                                                        data-notes="{{ $company->notes }}"
                                                        title="Editar"
                                                        aria-label="Editar {{ $company->name }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="{{ route('clients.index', ['company' => $company->id]) }}"
                                                        class="btn btn-primary btn-sm" title="Gestionar sitios/rutas"
                                                        aria-label="Gestionar sitios y rutas de {{ $company->name }}">
                                                        <i class="fas fa-route"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-edit-admin {{ $company->admin_id ? 'btn-secondary' : 'btn-success' }}"
                                                        data-company-id="{{ $company->id }}"
                                                        data-admin-id="{{ $company->admin_id }}"
                                                        data-admin-name="{{ $company->admin_name }}"
                                                        data-admin-email="{{ $company->admin_email }}"
                                                        title="{{ $company->admin_id ? 'Editar Admin' : 'Agregar Admin' }}"
                                                        aria-label="{{ $company->admin_id ? 'Editar Admin' : 'Agregar Admin' }} de {{ $company->name }}">
                                                        <i class="fas {{ $company->admin_id ? 'fa-user-edit' : 'fa-user-plus' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="deleteCompany({{ $company->id }}, '{{ addslashes($company->name) }}')"
                                                        title="Eliminar" aria-label="Eliminar {{ $company->name }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div class="py-4">
                                                    <i class="fas fa-industry fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No hay empresas/sindicatos registrados</h5>
                                                    <p class="text-muted">Comienza agregando tu primera empresa/sindicato
                                                        cliente.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación Laravel --}}
                        @if ($companies->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $companies->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modales --}}
    @include('rutvans.companies.create')
    @include('rutvans.companies.edit')
    @include('rutvans.companies.show')
    @include('rutvans.companies.edit-admin.edit-admin')

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
    <script type="module" src="{{ asset('js/companies/main.js') }}"></script>
    {{-- Script de los alerts --}}
    <script>
        window.alerts = {
            success: @json(session('success') ?? null),
            errors: @json($errors->any() ? $errors->all() : []),
        };
    </script>
@stop
