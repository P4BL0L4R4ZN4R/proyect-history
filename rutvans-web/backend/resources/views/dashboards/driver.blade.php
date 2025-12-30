@extends('adminlte::page')

@section('title', 'Dashboard Conductor')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Dashboard - Conductor</h1>
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
                        <i class="fas fa-steering-wheel"></i>
                        Bienvenido, Conductor {{ auth()->user()->name }}
                    </h3>
                </div>
                <div class="card-body">
                    <p class="lead">Panel de control para conductores de RutVans</p>
                    <p>Desde aquí puedes ver tu información personal y gestionar tu perfil.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del conductor -->
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
                                    <i class="fas fa-id-card"></i>
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
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Nota!</h5>
                        Para más funciones, contacta con tu coordinador.
                    </div>
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
                        Estado del Sistema
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <h5 class="description-header text-success">ACTIVO</h5>
                                <span class="description-text">ESTADO</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <h5 class="description-header text-info">CONDUCTOR</h5>
                                <span class="description-text">ROL</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <h5 class="description-header text-warning">{{ now()->format('H:i') }}</h5>
                                <span class="description-text">HORA ACTUAL</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block">
                                <h5 class="description-header text-primary">{{ now()->format('d/m/Y') }}</h5>
                                <span class="description-text">FECHA</span>
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
    console.log('Dashboard de Conductor cargado correctamente');
    
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
