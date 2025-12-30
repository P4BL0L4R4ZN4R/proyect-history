@extends('adminlte::page')

@section('title', 'Sin Autorización')

@section('content_header')
    <h1>Sin Autorización</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt"></i>
                        Acceso Restringido
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <i class="fas fa-lock fa-5x text-warning"></i>
                        </div>
                        <div class="col-md-9">
                            <h4 class="text-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No tienes permisos para acceder a esta funcionalidad
                            </h4>
                            <p class="lead">
                                {{ $message ?? 'Tu rol actual no tiene los permisos necesarios para realizar esta acción.' }}
                            </p>
                            
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info-circle"></i> Información de tu cuenta:</h5>
                                <p><strong>Usuario:</strong> {{ auth()->user()->name ?? 'No autenticado' }}</p>
                                <p><strong>Email:</strong> {{ auth()->user()->email ?? 'No disponible' }}</p>
                                @if(auth()->user() && auth()->user()->roles->count() > 0)
                                    <p><strong>Roles asignados:</strong>
                                        @foreach(auth()->user()->roles as $role)
                                            <span class="badge badge-primary">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </p>
                                @else
                                    <p><strong>Estado:</strong> <span class="badge badge-warning">Sin roles asignados</span></p>
                                @endif
                            </div>

                            <div class="alert alert-warning">
                                <h5><i class="icon fas fa-lightbulb"></i> ¿Qué puedes hacer?</h5>
                                <ul class="mb-0">
                                    <li>Contacta con tu administrador del sistema</li>
                                    <li>Verifica que tu cuenta tenga los permisos correctos</li>
                                    <li>Regresa a las secciones que sí puedes usar</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a la página anterior
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            @if(auth()->user())
                                @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('dashboard.role') }}" class="btn btn-primary">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="btn btn-info">
                                    <i class="fas fa-user-cog"></i> Mi Perfil
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                </a>
                            @endif
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
.card {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.badge {
    margin-right: 5px;
    margin-bottom: 2px;
}

.alert ul {
    padding-left: 20px;
}

.fa-5x {
    opacity: 0.7;
}
</style>
@stop
@stop
