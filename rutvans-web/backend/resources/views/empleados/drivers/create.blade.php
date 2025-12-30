<div class="modal fade" id="modalCreateDriver" tabindex="-1" aria-labelledby="modalCreateDriverLabel" aria-hidden="true"
    role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('drivers.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content rounded-3 shadow-lg">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #ff6600, #e55a00); padding: 1rem;">
                    <h5 class="modal-title" id="modalCreateDriverLabel">
                        <i class="fas fa-user-plus me-2"></i>Nuevo Conductor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
    <script>
        window.currentUserRole = @json(auth()->user()->hasRole('admin') ? 'admin' : 'coordinator');
    </script>
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-id-badge me-2"></i>
                                Información del Conductor
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
    @php $isAdmin = auth()->user()->hasRole('admin'); @endphp
    @if($isAdmin)
                                <div class="col-md-12">
                                    <label for="site_id" class="form-label fw-bold">
                                        <i class="fas fa-map-marker-alt me-1"></i>Selecciona el sitio
                                    </label>
                                    <select class="form-select" id="site_id" name="site_id" required>
                                        <option value="">-- Selecciona un sitio --</option>
                                        @foreach($sites as $site)
                                            <option value="{{ $site->id }}">{{ $site->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
    @endif
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-bold">
                                        <i class="fas fa-user me-1"></i>Nombre
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-1"></i>Correo Electrónico
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-bold">
                                        <i class="fas fa-lock me-1"></i>Contraseña
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-bold">
                                        <i class="fas fa-lock me-1"></i>Confirmar Contraseña
                                    </label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="photo_file" class="form-label fw-bold">
                                        <i class="fas fa-camera me-1"></i>Foto del Conductor
                                    </label>
                                    <input type="file" class="form-control" id="photo_file" name="photo_file" accept="image/*">
                                    <small class="form-text text-muted">Opcional. Formatos: JPG, PNG. Máximo 2MB.</small>
                                </div>

                            </div>
                        </div>
                    </div>

                    @if(!$isAdmin)
                    <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        El conductor será asignado automáticamente al mismo sitio que tú.
                    </div>
                    @endif
                </div>

                <div class="modal-footer bg-light rounded-bottom">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn text-white"
                        style="background: linear-gradient(135deg, #ff6600, #e55a00); font-weight: 600;">
                        <i class="fas fa-save me-2"></i>Crear Conductor
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
