<!-- Modal para Crear Empresa/Sindicato -->
<div class="modal fade" id="createCompanyModal" tabindex="-1" aria-labelledby="createCompanyModalLabel" aria-hidden="true"
    role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('companies.store') }}" method="POST" id="createCompanyForm" novalidate>
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="createCompanyModalLabel">
                        <i class="fas fa-plus me-2"></i>
                        Nueva Empresa/Sindicato
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Información de la Empresa/Sindicato -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-industry me-2"></i>
                                        Información de la Empresa/Sindicato
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label required">Nombre</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                required placeholder="Ej: Sindicato de Maxcanú" autocomplete="off">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="business_name" class="form-label">Razón Social</label>
                                            <input type="text" class="form-control" id="business_name"
                                                name="business_name" placeholder="Nombre fiscal de la empresa"
                                                autocomplete="off">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="rfc" class="form-label">RFC</label>
                                            <input type="text" class="form-control" id="rfc" name="rfc"
                                                maxlength="13" placeholder="RFC de la empresa" autocomplete="off">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="locality_id" class="form-label">Localidad Principal</label>
                                            <select class="form-select" id="locality_id" name="locality_id"
                                                aria-label="Seleccionar localidad">
                                                <option value="" selected>Seleccionar localidad...</option>
                                                @foreach ($localities as $locality)
                                                    <option value="{{ $locality->id }}">
                                                        {{ $locality->locality }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="status" class="form-label required">Estado</label>
                                            <select class="form-select" id="status" name="status" required
                                                aria-required="true" aria-label="Seleccionar estado">
                                                <option value="active" selected>Activo</option>
                                                <option value="inactive">Inactivo</option>
                                            </select>
                                        </div>

                                        <div class="col-md-8">
                                            <label for="address" class="form-label required">Dirección</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                required placeholder="Dirección completa de la empresa/sindicato"
                                                autocomplete="off">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="phone" class="form-label required">Teléfono</label>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                required placeholder="Ej: +1234567890" autocomplete="off"
                                                pattern="^\+?\d{7,15}$"
                                                title="Número telefónico válido, ej: +1234567890">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email de Contacto</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="contacto@empresa.com" autocomplete="off">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="notes" class="form-label">Notas Adicionales</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                                placeholder="Información adicional sobre la empresa/sindicato" autocomplete="off"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Usuario Admin Principal -->
                        <div class="col-12 mt-3">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user-shield me-2"></i>
                                        Usuario Administrador Principal
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>
                                            Este usuario será el administrador principal de la empresa/sindicato y podrá
                                            gestionar todos sus sitios/rutas.
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="admin_name" class="form-label required">Nombre del
                                                Admin</label>
                                            <input type="text" class="form-control" id="admin_name"
                                                name="admin_name" required
                                                placeholder="Nombre completo del administrador" autocomplete="off">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="admin_email" class="form-label required">Email del
                                                Admin</label>
                                            <input type="email" class="form-control" id="admin_email"
                                                name="admin_email" required placeholder="admin@empresa.com"
                                                autocomplete="off">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="admin_password" class="form-label required">Contraseña</label>
                                            <input type="password" class="form-control" id="admin_password"
                                                name="admin_password" required minlength="6"
                                                placeholder="Mínimo 6 caracteres" autocomplete="new-password"
                                                aria-describedby="passwordHelp">
                                            <div id="passwordHelp" class="form-text">La contraseña debe tener al menos
                                                6 caracteres.</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="admin_password_confirmation"
                                                class="form-label required">Confirmar Contraseña</label>
                                            <input type="password" class="form-control"
                                                id="admin_password_confirmation" name="admin_password_confirmation"
                                                required minlength="6" placeholder="Repetir contraseña"
                                                autocomplete="new-password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cancelar">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" aria-label="Crear Empresa/Sindicato">
                        <i class="fas fa-save me-1"></i>
                        Crear Empresa/Sindicato
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
