<!-- Modal para Editar Empresa/Sindicato -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="editCompanyForm">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Empresa/Sindicato
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Información de la Empresa/Sindicato -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-industry mr-2"></i>
                                        Información de la Empresa/Sindicato
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="edit_name" class="required">Nombre</label>
                                                <input type="text" class="form-control" id="edit_name" name="name"
                                                    required placeholder="Ej: Sindicato de Maxcanú">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="edit_business_name">Razón Social</label>
                                                <input type="text" class="form-control" id="edit_business_name"
                                                    name="business_name" placeholder="Nombre fiscal de la empresa">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_rfc">RFC</label>
                                                <input type="text" class="form-control" id="edit_rfc" name="rfc"
                                                    placeholder="RFC de la empresa" maxlength="13">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_locality_id">Localidad Principal</label>
                                                <select class="form-control" id="edit_locality_id" name="locality_id">
                                                    <option value="">Seleccionar localidad...</option>
                                                    @foreach ($localities as $locality)
                                                        <option value="{{ $locality->id }}">{{ $locality->locality }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_status" class="required">Estado</label>
                                                <select class="form-control" id="edit_status" name="status" required>
                                                    <option value="active">Activo</option>
                                                    <option value="inactive">Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="edit_address" class="required">Dirección</label>
                                                <input type="text" class="form-control" id="edit_address"
                                                    name="address" required
                                                    placeholder="Dirección completa de la empresa/sindicato">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edit_phone" class="required">Teléfono</label>
                                                <input type="text" class="form-control" id="edit_phone"
                                                    name="phone" required placeholder="Ej: +1234567890">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="edit_email">Email de Contacto</label>
                                                <input type="email" class="form-control" id="edit_email"
                                                    name="email" placeholder="contacto@empresa.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="edit_notes">Notas Adicionales</label>
                                                <textarea class="form-control" id="edit_notes" name="notes" rows="3"
                                                    placeholder="Información adicional sobre la empresa/sindicato"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- card-body -->
                            </div> <!-- card -->
                        </div> <!-- col-12 -->
                    </div> <!-- row -->
                </div> <!-- modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cancelar">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i>
                        Actualizar Empresa/Sindicato
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
