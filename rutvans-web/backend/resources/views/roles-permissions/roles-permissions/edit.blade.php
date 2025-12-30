<div class="modal fade" id="editPermissionModal" tabindex="-1" role="dialog" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #007bff, #0056b3); border-radius: 12px 12px 0 0; padding: 1.5rem;">
                <h5 class="modal-title mb-0" id="editPermissionModalLabel" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    <i class="fas fa-lock me-2"></i> Asignar Permisos al Rol
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1;">
                    <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                </button>
            </div>

            <form id="editPermissionForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editRoleId" name="role_id">
                <div class="modal-body" style="padding: 2rem;">
                    <div class="form-group">
                        <label for="permissionsContainer" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">
                            <i class="fas fa-user-shield mr-1 text-primary"></i> Selecciona los permisos:
                        </label>
                        <div id="permissionsContainer" class="border rounded p-3 bg-light">
                            <div class="text-muted">Cargando permisos...</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="background-color: #f8f9fa; border-radius: 0 0 12px 12px; padding: 1rem 2rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 6px; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn text-white" style="background: linear-gradient(135deg, #007bff, #0056b3); border: none; border-radius: 6px; padding: 0.5rem 1.5rem; font-weight: 600;">
                        <i class="fas fa-save mr-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
