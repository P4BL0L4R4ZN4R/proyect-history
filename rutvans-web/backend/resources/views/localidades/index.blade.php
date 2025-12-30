@extends('adminlte::page')

@section('title', 'Localidades')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-map-marker-alt me-2"></i> Gestión de Localidades
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">
                Administra las ubicaciones geográficas del sistema Rutvans
            </p>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm" style="border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
            <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <i class="fas fa-globe-americas me-2"></i> Gestión de Localidades
            </h3>
        </div>

        <div class="card-body" style="padding: 2rem;">
            <div class="row">
                <!-- Contenedor del mapa -->
                <div class="col-md-7">
                    {{-- Incluye mapa, inputs y JS --}}
                    @include('localidades.mapa')
                </div>

                <!-- Formulario de localidades -->
                <div class="col-md-5">
                    <h4 class="text-center mb-3" style="color: #ff6600; font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Formulario de Localidad
                    </h4>
                    <div class="card" style="border: 2px solid #ff6600; border-radius: 12px;">
                        <div class="card-body" style="padding: 1.5rem;">
                            {{-- Formulario de creación --}}
                            @include('localidades.create')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Localidades Registradas -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm" style="border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
                        <div class="card-header" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
                            <h5 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                <i class="fas fa-list-ul me-2"></i> Localidades Registradas ({{ $localidadesCount }})
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 2rem;">
                            {{-- Tabla de localidades --}}
                            @include('localidades.tabla_localidades')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ asset('js/localidades/search.js') }}"></script>
    <script src="{{ asset('js/localidades/controls.js') }}"></script>
@endsection
