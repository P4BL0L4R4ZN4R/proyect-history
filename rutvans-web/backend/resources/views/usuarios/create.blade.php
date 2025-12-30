<div class="modal fade" id="createUsuarioModal" tabindex="-1" aria-labelledby="createUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('usuarios.store') }}" method="POST" class="needs-validation" novalidate id="createUsuarioForm">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <!-- Encabezado -->
                <div class="modal-header text-white" style="background-color: #FF6700;">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-user-plus me-2"></i>Crear Usuario
                    </h5>
                    <button type="button" class="ms-auto text-white fs-2 border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Cerrar">
                        &times;
                    </button>
                </div>

                <!-- Cuerpo del modal -->
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-user" style="color: #FF6700;"></i></span>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="invalid-feedback">Por favor ingresa un nombre válido</div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-envelope" style="color: #FF6700;"></i></span>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="invalid-feedback">Por favor ingresa un correo válido</div>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-phone" style="color: #FF6700;"></i></span>
                                <input type="tel" class="form-control" name="phone_number">
                            </div>
                            <div class="invalid-feedback">Ingresa un número válido</div>
                        </div>

                        <!-- Contraseña -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-lock" style="color: #FF6700;"></i></span>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="invalid-feedback">Ingresa una contraseña válida (mínimo 8 caracteres)</div>
                        </div>

                        <!-- Confirmar contraseña -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmar Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-lock" style="color: #FF6700;"></i></span>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                            <div class="invalid-feedback">Confirma correctamente la contraseña</div>
                        </div>

                        <!-- Dirección -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white align-items-start"><i class="fas fa-map-marker-alt mt-1" style="color: #FF6700;"></i></span>
                                <textarea class="form-control" name="address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer del modal -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-dark fw-semibold shadow-sm rounded-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn fw-semibold shadow-sm rounded-3 text-white" style="background-color: #FF6700;">
                        Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
