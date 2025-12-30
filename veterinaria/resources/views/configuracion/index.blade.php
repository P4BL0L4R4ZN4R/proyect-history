@extends('adminlte::page')

@section('content')
<div class="card shadow rounded" style="max-width: 400px; margin: 3rem auto; background-color: #ffffff;">
    <div class="card-header bg-primary text-white text-center">
        <h3 class="card-title mb-0"><i class="fas fa-cogs me-2"></i>Configuración Actual</h3>
    </div>

    <div class="card-body text-center">
        <div class="image-preview mb-3">
            <img src="{{ asset($currentLogo) }}?v={{ time() }}"
                 class="img-fluid shadow rounded-circle border"
                 style="max-height: 150px; border: 4px solid #e3e3e3; padding: 6px;">
        </div>

        <h5 class="mb-1">{{ $config->empresa ?? 'Nombre Empresa' }}</h5>
        <p class="text-muted mb-3">{{ $config->sucursal ?? 'Sucursal Principal' }}</p>

        <a href="{{ route('configuracion.edit') }}" class="btn btn-outline-warning px-4 py-2">
            <i class="fas fa-edit me-2"></i>Editar Configuración
        </a>
    </div>
</div>
@endsection



@section('css')
<style>
    body {
        background-color: #f4f6f9 !important;
    }

    .card {
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .image-preview:hover img {
        transform: scale(1.03);
        transition: all 0.3s ease;
    }
</style>
@endsection
