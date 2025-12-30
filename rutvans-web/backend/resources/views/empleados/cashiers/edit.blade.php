<div class="modal fade" id="modalEditCashier" tabindex="-1" aria-labelledby="modalEditCashierLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="formEditCashier" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="cashier_id" id="edit_cashier_id" value="">

            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalEditCashierLabel">
                        <i class="fas fa-user-edit me-2"></i>Editar Cajero
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label fw-bold">Nombre</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_email" class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_photo_file" class="form-label fw-bold">Nueva foto (opcional)</label>
                            <input type="file" class="form-control" id="edit_photo_file" name="photo_file" accept="image/*">
                            <small class="form-text text-muted">Si no seleccionas una imagen, se mantendrá la
                                actual.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_password" class="form-label fw-bold">Contraseña (opcional)</label>
                            <input type="password" class="form-control" id="edit_password" name="password"
                                autocomplete="new-password">
                            <small class="form-text text-muted">Si no deseas cambiar la contraseña, déjalo en
                                blanco.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_password_confirmation" class="form-label fw-bold">Confirmar
                                Contraseña</label>
                            <input type="password" class="form-control" id="edit_password_confirmation"
                                name="password_confirmation" autocomplete="new-password">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Foto actual</label><br>
                            <img id="current_photo_preview" src="" alt="Foto actual"
                                style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid #ff6600; display: none;">
                            <p id="no_photo_message" class="text-muted small fst-italic" style="display: none;">
                                Este cajero no tiene una foto asignada.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="fas fa-save me-2"></i>Actualizar Cajero
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
