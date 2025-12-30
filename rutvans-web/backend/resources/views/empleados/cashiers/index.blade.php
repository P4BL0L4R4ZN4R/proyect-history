@extends('adminlte::page')

@section('title', 'Cajeros')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3"
        style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 2rem;">
                <i class="fas fa-cash-register me-3"></i> Gestión de Cajeros
            </h1>
            <p class="text-white mb-0" style="font-family: 'Poppins', sans-serif; opacity: 0.9; font-size: 1.1rem;">
                Administra los cajeros responsables de las ventas y pagos
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
                <i class="fas fa-money-bill-wave me-2"></i> Cajeros del Sistema
            </h3>
            <div class="ms-auto">
                <button type="button" class="btn btn-light btn-sm" style="color: #e55a00; font-weight: 500;"
                    data-bs-toggle="modal" data-bs-target="#modalCreateCashier">
                    <i class="fas fa-plus me-1"></i> Nuevo Cajero
                </button>
            </div>
        </div>

        <div class="card-body" style="padding: 2rem;">
            <div class="table-responsive">
                <table id="cashiersTable" class="table table-hover table-striped"
                    style="border-radius: 8px; overflow: hidden;">
                    <thead style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                        <tr>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i
                                    class="fas fa-hashtag me-1"></i> ID</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i
                                    class="fas fa-user me-1"></i> Nombre</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i
                                    class="fas fa-envelope me-1"></i> Correo</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i
                                    class="fas fa-id-badge me-1"></i> Código</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i
                                    class="fas fa-camera me-1"></i> Foto</th>
                            <th class="fw-bold" style="color: #495057; font-family: 'Poppins', sans-serif;"><i
                                    class="fas fa-cogs me-1"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cashiers as $cashier)
                            <tr style="transition: all 0.3s ease;">
                                <td>
                                    <span class="badge bg-success"
                                        style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">{{ $cashier->id }}</span>
                                </td>
                                <td style="font-weight: 500;">
                                    <i class="fas fa-user-circle text-muted me-2"></i>
                                    {{ $cashier->user->name ?? 'Sin usuario' }}
                                </td>
                                <td style="font-weight: 500;">
                                    <i class="fas fa-at text-muted me-2"></i>
                                    {{ $cashier->user->email ?? 'Sin correo' }}
                                </td>
                                <td>
                                    <span class="badge bg-warning"
                                        style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">{{ $cashier->employee_code }}</span>
                                </td>
                                <td>
                                    @if ($cashier->photo_path && \App\Helpers\StorageHelper::fileExists($cashier->photo_path))
                                        <img src="{{ \App\Helpers\StorageHelper::getPublicUrl($cashier->photo_path) }}"
                                            alt="Foto de {{ $cashier->user->name ?? '' }}" class="rounded-circle shadow-sm"
                                            style="width: 50px; height: 50px; object-fit: cover;"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-light"
                                            style="width: 50px; height: 50px; display: none;">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-light"
                                            style="width: 50px; height: 50px;">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-warning btn-sm btn-edit-cashier"
                                            data-id="{{ $cashier->id }}" data-name="{{ $cashier->user->name ?? '' }}"
                                            data-email="{{ $cashier->user->email ?? '' }}"
                                            data-employee-code="{{ $cashier->employee_code }}"
                                            data-photo="{{ $cashier->photo_path && \App\Helpers\StorageHelper::fileExists($cashier->photo_path) ? \App\Helpers\StorageHelper::getPublicUrl($cashier->photo_path) : '' }}"
                                            style="border-radius: 6px; font-weight: 500;">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        <form action="{{ route('cashiers.destroy', $cashier) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Seguro que quieres eliminar este cajero?')">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('empleados.cashiers.create')
    @include('empleados.cashiers.edit')
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 JS Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script de datatables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script type="module" src="{{ asset('js/employees/cashiers/main.js') }}"></script>
@endsection