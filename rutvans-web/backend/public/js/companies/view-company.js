// view-company.js

export function initViewCompany() {
    window.viewCompany = function (companyId) {
        // Mostrar loading en el modal
        const modalBody = document.querySelector('#viewCompanyModal .modal-body');
        modalBody.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Cargando información...</p>
            </div>
        `;
        
        // Abrir el modal
        const modal = new bootstrap.Modal(document.getElementById('viewCompanyModal'));
        modal.show();
        
        // Cargar datos
        $.get(`/companies/${companyId}`, function (data) {
            // Validar que los datos existen
            if (!data.company) {
                modalBody.innerHTML = `
                    <div class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h5>Datos no encontrados</h5>
                        <p>No se pudieron cargar los datos de la empresa/sindicato.</p>
                    </div>
                `;
                return;
            }

            // Asegurar que stats existe con valores por defecto
            const stats = data.stats || {
                sites: 0,
                active_sites: 0,
                total_users: 0
            };

            modalBody.innerHTML = `
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Nombre</label>
                                        <p class="mb-1 fw-bold">${escapeHtml(data.company.name)}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Razón Social</label>
                                        <p class="mb-1">${escapeHtml(data.company.business_name || 'N/A')}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">RFC</label>
                                        <p class="mb-1">${escapeHtml(data.company.rfc || 'N/A')}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Localidad</label>
                                        <p class="mb-1">${escapeHtml(data.company.locality ? data.company.locality.locality : 'N/A')}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Dirección</label>
                                        <p class="mb-1">${escapeHtml(data.company.address)}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Teléfono</label>
                                        <p class="mb-1">${escapeHtml(data.company.phone)}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Email</label>
                                        <p class="mb-1">${escapeHtml(data.company.email || 'N/A')}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Estado</label>
                                        <p class="mb-1">
                                            <span class="badge ${data.company.status === 'active' ? 'bg-success' : 'bg-danger'}">
                                                ${data.company.status === 'active' ? 'Activo' : 'Inactivo'}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Total de Sitios/Rutas</label>
                                        <p class="mb-1 fw-bold text-primary fs-4">${stats.sites}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Sitios Activos</label>
                                        <p class="mb-1 fw-bold text-success fs-4">${stats.active_sites}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Total de Usuarios</label>
                                        <p class="mb-1 fw-bold text-info fs-4">${stats.total_users}</p>
                                    </div>
                                    ${data.company.notes ? `
                                        <div class="mb-3">
                                            <label class="form-label text-muted small">Notas</label>
                                            <p class="mb-1">${escapeHtml(data.company.notes)}</p>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).fail(function () {
            modalBody.innerHTML = `
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <h5>Error al cargar</h5>
                    <p>No se pudo cargar la información de la empresa/sindicato. Por favor, intenta nuevamente.</p>
                </div>
            `;
        });
    }
}

function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
