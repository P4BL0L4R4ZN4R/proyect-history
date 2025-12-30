@extends('adminlte::page')

@section('title', 'Acceso Denegado')

@section('content_header')
    <h1>Acceso Denegado</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="error-page">
                <h2 class="headline text-warning"> 403</h2>

                <div class="error-content">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> ¡Oops! No tienes permisos.</h3>

                    <p>
                        Lo sentimos, no tienes los permisos necesarios para acceder a esta sección.
                        <br>
                        Si crees que esto es un error, contacta con tu administrador.
                    </p>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-info-circle"></i>
                                        Información
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Usuario:</strong> {{ auth()->user()->name ?? 'No autenticado' }}</p>
                                    <p><strong>Email:</strong> {{ auth()->user()->email ?? 'No disponible' }}</p>
                                    @if(auth()->user() && auth()->user()->roles->count() > 0)
                                        <p><strong>Rol:</strong> 
                                            @foreach(auth()->user()->roles as $role)
                                                <span class="badge badge-info">{{ $role->name }}</span>
                                            @endforeach
                                        </p>
                                    @else
                                        <p><strong>Rol:</strong> <span class="badge badge-secondary">Sin rol asignado</span></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-question-circle"></i>
                                        ¿Qué puedo hacer?
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success"></i> Verifica que tengas el rol correcto</li>
                                        <li><i class="fas fa-check text-success"></i> Contacta con tu administrador</li>
                                        <li><i class="fas fa-check text-success"></i> Regresa a la página principal</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            @if(auth()->user())
                                @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary mr-2">
                                        <i class="fas fa-tachometer-alt"></i> Ir al Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('dashboard.role') }}" class="btn btn-primary mr-2">
                                        <i class="fas fa-tachometer-alt"></i> Ir al Dashboard
                                    </a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="btn btn-info">
                                    <i class="fas fa-user"></i> Mi Perfil
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
.error-page {
    margin: 20px auto 0 auto;
    text-align: center;
}

.error-page > .headline {
    float: left;
    font-size: 100px;
    font-weight: 300;
    margin-right: 20px;
}

.error-page > .error-content {
    display: block;
    margin-left: 190px;
}

.error-page > .error-content > h3 {
    font-weight: 300;
    font-size: 25px;
}

@media (max-width: 991px) {
    .error-page > .headline {
        float: none;
        text-align: center;
        margin-bottom: 10px;
    }
    
    .error-page > .error-content {
        margin-left: 0;
    }
}

.card {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    margin-bottom: 1rem;
}

.badge {
    margin-right: 5px;
}
</style>
@stop

@section('js')
<script>
    console.log('Vista de error 403 cargada correctamente');
    
    // Auto-redirección después de 30 segundos (opcional)
    // setTimeout(function() {
    //     if (confirm('¿Deseas ser redirigido a la página principal?')) {
    //         window.location.href = '{{ route('login') }}';
    //     }
    // }, 30000);
</script>
@stop
