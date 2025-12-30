<!-- Modal para Editar Coordinador -->
<div class="modal fade" id="editCoordinatorModal" tabindex="-1" aria-labelledby="editCoordinatorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editCoordinatorForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editCoordinatorModalLabel">
                        <i class="fas fa-user-edit me-2"></i> Editar Coordinador
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Izquierda -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_name" class="required">Nombre Completo</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_email" class="required">Correo Electrónico</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_address" class="required">Dirección</label>
                                <input type="text" class="form-control" id="edit_address" name="address" required>
                            </div>
                        </div>

                        <!-- Derecha -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_phone_number" class="required">Teléfono</label>
                                <input type="text" class="form-control" id="edit_phone_number" name="phone_number"
                                    required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_photo_path">Foto</label>
                                <input type="file" class="form-control" id="edit_photo_path" name="photo_path"
                                    accept="image/*">
                                <img id="edit_photoPreview" src="#" alt="Vista previa" class="img-fluid mt-2"
                                    style="max-height: 150px; display:none; border-radius: 6px;">
                            </div>

                            {{-- Hidden para el site_id si es necesario --}}
                            <input type="hidden" name="site_id" id="edit_site_id" value="{{ $site->id }}">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="fas fa-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
