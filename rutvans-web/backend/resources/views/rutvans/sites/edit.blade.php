<!-- Modal para Editar Sitio/Ruta -->
<div class="modal fade" id="editSiteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="" method="POST" id="editSiteForm">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Sitio/Ruta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_company_id" class="required">Empresa/Sindicato</label>
                                {{-- Siempre mostrar la empresa del admin como readonly --}}
                                <input type="text" class="form-control" 
                                       value="{{ $companies->first()->name ?? 'No hay empresa asignada' }}" readonly>
                                <input type="hidden" id="edit_company_id" name="company_id" value="{{ $companies->first()->id ?? '' }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_name" class="required">Nombre del Sitio/Terminal</label>
                                <input type="text" class="form-control" id="edit_name" name="name"
                                    required placeholder="Ej: Terminal Central">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_route_name">Ruta Principal (opcional)</label>
                                <input type="text" class="form-control" id="edit_route_name"
                                    name="route_name" placeholder="Ej: Maxcanú-Mérida">
                                <small class="form-text text-muted">Las rutas específicas se
                                    configurarán posteriormente por el admin del sitio</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_locality_id" class="required">Localidad donde está ubicado</label>
                                <select class="form-control" id="edit_locality_id" name="locality_id" required>
                                    <option value="">Seleccionar localidad...</option>
                                    @foreach ($localities as $locality)
                                        <option value="{{ $locality->id }}">
                                            {{ $locality->locality }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="edit_address" class="required">Dirección</label>
                                <input type="text" class="form-control" id="edit_address"
                                    name="address" required placeholder="Dirección completa del sitio">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_phone" class="required">Teléfono</label>
                                <input type="text" class="form-control" id="edit_phone"
                                    name="phone" required placeholder="Ej: +1234567890">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status" class="required">Estado</label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <option value="active">Activo</option>
                                    <option value="inactive">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cancelar">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i>
                        Actualizar Sitio/Terminal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
