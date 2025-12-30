@extends('adminlte::page')

@section('title', 'Dashboard Coordinador')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Panel de control para coordinadores de RutVans</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie"></i>
                            Bienvenido, Coordinador {{ auth()->user()->name }}
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="col-12">
                            <div class="card shadow border-0">
                                <div class="card-body">
                                    <div
                                        class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center mb-3 mb-md-0">
                                            {{-- Foto del usuario --}}
                                            @php
                                                $profilePhoto = null;
                                                if ($coordinate && $coordinate->profile_photo_path && \App\Helpers\StorageHelper::fileExists($coordinate->profile_photo_path)) {
                                                    $profilePhoto = \App\Helpers\StorageHelper::getPublicUrl($coordinate->profile_photo_path);
                                                } elseif (auth()->user()->profile_photo_path && \App\Helpers\StorageHelper::fileExists(auth()->user()->profile_photo_path)) {
                                                    $profilePhoto = \App\Helpers\StorageHelper::getPublicUrl(auth()->user()->profile_photo_path);
                                                } else {
                                                    $profilePhoto = 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name);
                                                }
                                            @endphp
                                            <img src="{{ $profilePhoto }}"
                                                alt="Foto de {{ auth()->user()->name }}"
                                                class="rounded-circle shadow-sm me-3"
                                                style="width: 150px; height: 150px; object-fit: cover;">
                                            <div>
                                                <h4 class="mb-0">{{ auth()->user()->name }}</h4>
                                                <small class="text-muted">{{ auth()->user()->email }}</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-primary fs-6 px-4 py-2 rounded-pill">
                                            Coordinador
                                        </span>
                                    </div>

                                    <div class="row text-center">
                                        <div class="col-md-6 mb-4">
                                            <strong>Dirección</strong>
                                            <div class="text-muted">{{ auth()->user()->address ?? 'No registrado' }}</div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <strong>Código Empleado</strong>
                                            <div class="text-muted">{{ $coordinate->employee_code ?? 'N/A' }}</div>
                                        </div>
                                    </div>

                                    @if ($coordinate)
                                        <hr>
                                        <div class="row text-center">
                                            <div class="col-md-6 mb-4">
                                                <strong>Teléfono</strong>
                                                <div class="text-muted">
                                                    {{ auth()->user()->phone_number ?? 'No registrado' }}
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <strong>Último acceso</strong>
                                                <div class="text-muted">{{ now()->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Accesos rápidos -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-tasks"></i>
                                            Acciones Rápidas
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <a href="{{ route('drivers.index') }}" class="btn btn-info btn-block">
                                                    <i class="fas fa-user-plus"></i> Conductores
                                                </a>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <a href="{{ route('cashiers.index') }}" class="btn btn-info btn-block">
                                                    <i class="fas fa-user-plus"></i> Cajeros
                                                </a>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <a href="{{ route('units.index') }}" class="btn btn-warning btn-block">
                                                    <i class="fas fa-bus"></i> Unidades
                                                </a>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <a href="{{ route('route_unit_schedule.index') }}"
                                                    class="btn btn-primary btn-block">
                                                    <i class="fas fa-calendar-alt"></i> Calendario
                                                </a>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <a href="{{ route('sales.history') }}" class="btn btn-success btn-block">
                                                    <i class="fas fa-chart-line"></i> Ventas
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Tarjetas de estadísticas -->
                        <div class="row">
                            <div class="col-lg-4 col-12">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        @php
                                            try {
                                                $conductoresCount = \App\Models\User::role('driver')->count();
                                            } catch (\Exception $e) {
                                                $conductoresCount = 0;
                                            }
                                        @endphp
                                        <h3>{{ $conductoresCount }}</h3>
                                        <p>Conductores</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-12">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        @php
                                            try {
                                                $cajerosCount = \App\Models\User::role('cashier')->count();
                                            } catch (\Exception $e) {
                                                $cajerosCount = 0;
                                            }
                                        @endphp
                                        <h3>{{ $cajerosCount }}</h3>
                                        <p>Cajeros</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-cash-register"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-12">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $totalUsers }}</h3>
                                        <p>Total Usuarios</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users-cog"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box {
            border-radius: 10px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        }

        .btn-block {
            margin-bottom: 10px;
        }

        .list-group-item {
            border: none;
            padding: 10px 0;
        }
    </style>
@stop

@section('js')
    <script>
        console.log('Dashboard de Coordinador cargado correctamente');

        // Mensaje de bienvenida
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Bienvenido!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
@stop
