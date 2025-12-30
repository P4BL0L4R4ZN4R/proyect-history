<!-- Modal para Editar Usuario -->
<div class="modal fade" id="editUsuarioModal" tabindex="-1" aria-labelledby="editUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="needs-validation" novalidate autocomplete="off" id="editUsuarioForm">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow">
                <!-- Encabezado -->
                <div class="modal-header text-white" style="background-color: #FF6700;">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-user-edit me-2"></i>Editar Usuario
                    </h5>
                    <button type="button" class="ms-auto text-white fs-2 border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Cerrar">
                        &times;
                    </button>
                </div>

                <!-- Cuerpo -->
                <div class="modal-body p-4">
                    <input type="hidden" id="editUsuarioId" name="usuario_id">

                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-secondary"></i></span>
                                <input type="text" class="form-control border-start-0" id="editUsuarioNombre" name="name" required>
                            </div>
                            <div class="invalid-feedback small">Por favor ingresa un nombre válido</div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-secondary"></i></span>
                                <input type="email" class="form-control border-start-0" id="editUsuarioEmail" name="email" required>
                            </div>
                            <div class="invalid-feedback small">Ingresa un correo válido</div>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone text-secondary"></i></span>
                                <input type="text" class="form-control border-start-0" id="editUsuarioTelefono" name="phone_number">
                            </div>
                            <div class="invalid-feedback small">Ingresa un número válido</div>
                        </div>

                        <!-- Contraseña (opcional) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nueva Contraseña <small class="text-muted">(Opcional)</small></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-secondary"></i></span>
                                <input type="password" class="form-control border-start-0" name="password" autocomplete="new-password">
                            </div>
                            <div class="form-text">Dejar en blanco si no deseas cambiarla</div>
                        </div>

                        <!-- Confirmar contraseña (opcional) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmar Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-secondary"></i></span>
                                <input type="password" class="form-control border-start-0" name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 align-items-start"><i class="fas fa-map-marker-alt text-secondary mt-1"></i></span>
                                <textarea class="form-control border-start-0" id="editUsuarioDireccion" name="address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-dark fw-semibold shadow-sm rounded-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn text-white shadow-sm fw-semibold rounded-3" style="background-color: #FF6700;">
                        Actualizar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
