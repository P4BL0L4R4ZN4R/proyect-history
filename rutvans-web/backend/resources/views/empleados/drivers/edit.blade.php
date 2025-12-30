<div class="modal fade" id="modalEditDriver" tabindex="-1" aria-labelledby="modalEditDriverLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded-3 shadow-lg">
            <form method="POST" id="formEditDriver" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="driver_id" id="edit_driver_id" value="">

                <div class="modal-header text-white" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
                    <h5 class="modal-title" id="modalEditDriverLabel">
                        <i class="fas fa-user-edit me-2"></i>Editar Conductor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-id-badge me-2"></i>
                                Información del Conductor
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="edit_name" class="form-label fw-bold">
                                        <i class="fas fa-user me-1"></i>Nombre
                                    </label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_email" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-1"></i>Correo Electrónico
                                    </label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_password" class="form-label fw-bold">
                                        <i class="fas fa-lock me-1"></i>Contraseña (opcional)
                                    </label>
                                    <input type="password" class="form-control" id="edit_password" name="password" autocomplete="new-password">
                                    <small class="form-text text-muted">Dejar vacío para mantener la contraseña actual.</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_password_confirmation" class="form-label fw-bold">
                                        <i class="fas fa-lock me-1"></i>Confirmar Contraseña
                                    </label>
                                    <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation" autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-file-alt me-2"></i>
                                Documentos del Conductor
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="edit_license_file" class="form-label fw-bold">
                                        <i class="fas fa-id-card me-1"></i>Licencia (PDF/JPG/PNG)
                                    </label>
                                    <input type="file" class="form-control" id="edit_license_file" name="license_file" accept="application/pdf,image/*">
                                    <small class="form-text text-muted">Si no seleccionas un archivo, se mantendrá el actual.</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_license_expiration" class="form-label fw-bold">
                                        <i class="fas fa-calendar-alt me-1"></i>Fecha de expiración
                                    </label>
                                    <input type="date" class="form-control" id="edit_license_expiration" name="license_expiration">
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_photo_file" class="form-label fw-bold">
                                        <i class="fas fa-camera me-1"></i>Nueva foto del conductor
                                    </label>
                                    <input type="file" class="form-control" id="edit_photo_file" name="photo_file" accept="image/*">
                                    <small class="form-text text-muted">Si no seleccionas una imagen, se mantendrá la actual.</small>
                                </div>

                                <div class="col-md-6">
                                    <div class="text-center">
                                        <label class="form-label fw-bold">Foto actual</label><br>
                                        <img id="current_photo_preview" src="" alt="Foto actual"
                                            style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #ffc107;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="fas fa-save me-2"></i>Actualizar Conductor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
