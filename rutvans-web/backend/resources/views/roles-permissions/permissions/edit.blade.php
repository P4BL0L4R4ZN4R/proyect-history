<!-- Modal para Editar Permiso -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" role="dialog" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content"
            style="border-radius: 12px; border: none; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
            <div class="modal-header text-white"
                style="background: linear-gradient(135deg, #ffc107, #e0a800); border-radius: 12px 12px 0 0; padding: 1.5rem;">
                <h5 class="modal-title mb-0" id="editPermissionModalLabel"
                    style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    <i class="fas fa-edit mr-2"></i> Editar Permiso
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                    style="opacity: 1;">
                    <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                </button>
            </div>

            <form id="editPermissionForm" method="POST" class="needs-validation" novalidate autocomplete="off">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding: 2rem;">
                    <input type="hidden" id="editPermissionId" name="permission_id">
                    <div class="form-group">
                        <label for="editPermissionName" class="form-label"
                            style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">
                            <i class="fas fa-lock mr-1 text-warning"></i> Nombre del Permiso
                        </label>
                        <input type="text" class="form-control" id="editPermissionName" name="name" required
                            style="border-radius: 8px; border: 1px solid #ddd; padding: 0.75rem; font-size: 0.95rem;">
                        <div class="invalid-feedback">
                            Por favor ingresa un nombre válido para el permiso.
                        </div>
                    </div>
                </div>

                <div class="modal-footer"
                    style="background-color: #f8f9fa; border-radius: 0 0 12px 12px; padding: 1rem 2rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        style="border-radius: 6px; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn text-white"
                        style="background: linear-gradient(135deg, #ffc107, #e0a800); border: none; border-radius: 6px; padding: 0.5rem 1.5rem; font-weight: 600;">
                        <i class="fas fa-save mr-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
