<!-- Modal para Editar/Agregar Administrador -->
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editAdminForm" method="POST">
                @csrf
                <input type="hidden" id="admin_form_method" name="_method" value="PUT">

                <div class="modal-header" id="adminModalHeader">
                    <h5 class="modal-title" id="editAdminModalLabel">
                        <i class="fas fa-user-edit me-2" id="adminModalIcon"></i> 
                        <span id="adminModalTitle">Editar Administrador</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <!-- Información de la empresa -->
                    <div class="alert alert-info mb-3" id="companyInfoAlert">
                        <i class="fas fa-building me-2"></i>
                        <strong>Empresa: </strong><span id="modalCompanyName"></span>
                    </div>
                    
                    <!-- Mensaje cuando no hay admin -->
                    <div class="alert alert-warning mb-3" id="noAdminAlert" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atención:</strong> Esta empresa no tiene un administrador asignado. Use este formulario para agregar uno.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" id="edit_admin_id" name="admin_id">
                            <div class="form-group mb-3">
                                <label for="edit_admin_name" class="required">Nombre Completo</label>
                                <input type="text" class="form-control" id="edit_admin_name" name="admin_name" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_admin_email" class="required">Correo Electrónico</label>
                                <input type="email" class="form-control" id="edit_admin_email" name="admin_email" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_admin_password" id="passwordLabel">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="edit_admin_password" name="admin_password">
                                <small class="form-text text-muted" id="passwordHint">Dejar en blanco para mantener la contraseña actual</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_admin_password_confirmation" id="passwordConfirmLabel">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="edit_admin_password_confirmation" name="admin_password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn" id="submitButton">
                        <i class="fas fa-save me-1" id="submitIcon"></i> 
                        <span id="submitText">Guardar Cambios</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
