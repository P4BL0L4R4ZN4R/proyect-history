@extends('adminlte::page')

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Bootstrap 5 JS (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('title', 'Perfil')

@section('content')
<div class="container py-3">
    <div class="card shadow-lg rounded-4">
       <div class="card-header" style="background-color: #ff6f00; color: white;">
    <h2 class="mb-0"><i class="bi bi-person-circle me-2"></i>Perfil</h2>
</div>

        <div class="card-body p-4">

            <ul class="nav nav-tabs nav-tabs-bordered mb-4" role="tablist" id="profileTab">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active d-flex align-items-center" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                        <i class="bi bi-person-fill me-1"></i> Datos personales
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                        <i class="bi bi-key-fill me-1"></i> Contraseña
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions" type="button" role="tab" aria-controls="sessions" aria-selected="false">
                        <i class="bi bi-laptop-fill me-1"></i> Sesiones
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center" id="2fa-tab" data-bs-toggle="tab" data-bs-target="#2fa" type="button" role="tab" aria-controls="2fa" aria-selected="false">
                        <i class="bi bi-shield-lock-fill me-1"></i> 2FA
                    </button>
                </li>
                <li class="nav-item ms-auto" role="presentation">
                    <button class="nav-link text-danger d-flex align-items-center" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete" type="button" role="tab" aria-controls="delete" aria-selected="false">
                        <i class="bi bi-trash-fill me-1"></i> Eliminar cuenta
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="profileTabContent">
                <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                    @include('profile.partials.edit-profile')
                </div>
                <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                    @include('profile.partials.update-password')
                </div>
                <div class="tab-pane fade" id="sessions" role="tabpanel" aria-labelledby="sessions-tab">
                    @include('profile.partials.logout-sessions')
                </div>
                <div class="tab-pane fade" id="2fa" role="tabpanel" aria-labelledby="2fa-tab">
                    @include('profile.partials.two-fa')
                </div>
                <div class="tab-pane fade" id="delete" role="tabpanel" aria-labelledby="delete-tab">
                    @include('profile.partials.delete-account')
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* General */
    .nav-tabs-bordered .nav-link {
        border: 1px solid transparent;
        border-bottom-color: transparent;
        border-radius: 0.375rem 0.375rem 0 0;
        transition: all 0.3s ease;
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        color: #495057;
        background-color: #e9ecef;
    }

    .nav-tabs-bordered .nav-link:hover {
        background-color: #dee2e6;
        color: #000;
    }

    .nav-tabs-bordered .nav-link.active {
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        font-weight: bold;
    }

    /* Íconos */
    .nav-link i {
        font-size: 1.1rem;
    }

    /* Colores por tab */
    #info-tab {
        color: #0d6efd;
        background-color: #e7f1ff;
    }

    #info-tab.active {
        color: #0d6efd;
        background-color: #fff;
    }

    #password-tab {
        color: #ffc107;
        background-color: #fff8e1;
    }

    #password-tab.active {
        color: #ffc107;
        background-color: #fff;
    }

    #sessions-tab {
        color: #28a745;
        background-color: #e6f4ea;
    }

    #sessions-tab.active {
        color: #28a745;
        background-color: #fff;
    }

    #2fa-tab {
        color: #17a2b8;
        background-color: #e0f7fa;
    }

    #2fa-tab.active {
        color: #17a2b8;
        background-color: #fff;
    }

    #delete-tab {
        color: #dc3545;
        background-color: #fdecea;
    }

    #delete-tab.active {
        color: #dc3545;
        background-color: #fff;
    }
</style>

@endsection
