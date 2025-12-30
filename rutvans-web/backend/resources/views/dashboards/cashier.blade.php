@extends('adminlte::page')

@section('title', 'Dashboard Cajero')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Dashboard - Cajero</h1>
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
                        <i class="fas fa-cash-register"></i>
                        Bienvenido, Cajero {{ auth()->user()->name }}
                    </h3>
                </div>
                <div class="card-body">
                    <p class="lead">Panel de control para cajeros de RutVans</p>
                    <p>Desde aquí puedes gestionar tu información personal y ver tu perfil.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del cajero -->
    <div class="row">
        <div class="col-md-8">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i>
                        Mi Información
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-user"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nombre Completo</span>
                                    <span class="info-box-number">{{ auth()->user()->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Email</span>
                                    <span class="info-box-number">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        // Obtener información adicional del usuario si existe
                        $phoneNumber = auth()->user()->phone_number ?? 'No registrado';
                        $address = auth()->user()->address ?? 'No registrada';
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Teléfono</span>
                                    <span class="info-box-number">{{ $phoneNumber }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Dirección</span>
                                    <span class="info-box-number">{{ $address }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i>
                        Acciones
                    </h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-user-edit"></i> Editar Perfil
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-key"></i> Cambiar Contraseña
                    </a>
                    <hr>
                    <div class="alert alert-success">
                        <h5><i class="icon fas fa-info"></i> Información!</h5>
                        Como cajero, puedes gestionar tu perfil personal.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ now()->format('H:i') }}</h3>
                    <p>Hora Actual</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ now()->format('d') }}</h3>
                    <p>Día del Mes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>ACTIVO</h3>
                    <p>Estado</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>CAJERO</h3>
                    <p>Rol</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tag"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del rol -->
    <div class="row">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Información del Sistema
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="description-block border-right">
                                <h5 class="description-header text-success">
                                    <i class="fas fa-check-circle"></i> CONECTADO
                                </h5>
                                <span class="description-text">ESTADO DEL SISTEMA</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="description-block border-right">
                                <h5 class="description-header text-info">
                                    <i class="fas fa-cash-register"></i> CAJERO
                                </h5>
                                <span class="description-text">TU ROL ACTUAL</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="description-block">
                                <h5 class="description-header text-primary">
                                    <i class="fas fa-calendar"></i> {{ now()->format('d/m/Y') }}
                                </h5>
                                <span class="description-text">FECHA ACTUAL</span>
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
.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 10px;
    margin-bottom: 1rem;
}

.card {
    border-radius: 10px;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.small-box {
    border-radius: 10px;
}

.description-block {
    text-align: center;
    padding: 0 20px;
}

.btn-block {
    margin-bottom: 10px;
}
</style>
@stop

@section('js')
<script>
    console.log('Dashboard de Cajero cargado correctamente');
    
    // Mensaje de bienvenida
    @if(session('success'))
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
