@extends('adminlte::page')

@section('title', 'Dashboard Cliente')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Dashboard - Cliente</h1>
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
                        <i class="fas fa-user-circle"></i>
                        Bienvenido, {{ auth()->user()->name }}
                    </h3>
                </div>
                <div class="card-body">
                    <p class="lead">Portal del cliente RutVans</p>
                    <p>Desde aquí puedes gestionar tu información personal y ver tu perfil.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del cliente -->
    <div class="row">
        <div class="col-md-8">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i>
                        Mi Información Personal
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Teléfono</span>
                                    <span class="info-box-number">{{ auth()->user()->phone_number ?? 'No registrado' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Miembro desde</span>
                                    <span class="info-box-number">{{ auth()->user()->created_at->format('M Y') }}</span>
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
                        Mi Cuenta
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
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Información!</h5>
                        Como cliente, puedes gestionar tu información personal.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios disponibles -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><i class="fas fa-bus"></i></h3>
                    <p>Transporte</p>
                </div>
                <div class="icon">
                    <i class="fas fa-route"></i>
                </div>
                <div class="small-box-footer">
                    Servicios de transporte
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><i class="fas fa-clock"></i></h3>
                    <p>Horarios</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="small-box-footer">
                    Consulta horarios
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><i class="fas fa-map"></i></h3>
                    <p>Rutas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <div class="small-box-footer">
                    Ver rutas disponibles
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><i class="fas fa-phone"></i></h3>
                    <p>Soporte</p>
                </div>
                <div class="icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="small-box-footer">
                    Contactar soporte
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Información del Servicio
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <h5 class="description-header text-success">
                                    <i class="fas fa-check-circle"></i> ACTIVO
                                </h5>
                                <span class="description-text">ESTADO DE CUENTA</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <h5 class="description-header text-info">
                                    <i class="fas fa-user"></i> CLIENTE
                                </h5>
                                <span class="description-text">TIPO DE CUENTA</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <h5 class="description-header text-warning">
                                    <i class="fas fa-star"></i> BÁSICO
                                </h5>
                                <span class="description-text">PLAN ACTUAL</span>
                            </div>
                        </div>
                        <div class="col-md-3">
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

.small-box-footer {
    text-align: center;
    padding: 10px 0;
    color: rgba(255,255,255,.8);
    font-size: 13px;
    background: rgba(0,0,0,.1);
}
</style>
@stop

@section('js')
<script>
    console.log('Dashboard de Cliente cargado correctamente');
    
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
