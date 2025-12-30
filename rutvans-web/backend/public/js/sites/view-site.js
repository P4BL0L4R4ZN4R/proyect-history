export function initViewSite() {
    function viewSite(siteId) {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.get(`/clients/${siteId}`, function(data) {
            Swal.fire({
                title: '<i class="fas fa-route"></i> Detalles del Sitio/Ruta',
                html: `
                    <div class="row text-left">
                        <div class="col-md-6">
                            <h6><strong>Información del Sitio</strong></h6>
                            <p><strong>Empresa:</strong> ${data.site.company ? data.site.company.name : 'N/A'}</p>
                            <p><strong>Nombre:</strong> ${data.site.name}</p>
                            <p><strong>Ruta Principal:</strong> ${data.site.route_name || 'No especificada'}</p>
                            <p><strong>Localidad:</strong> ${data.site.locality ? data.site.locality.locality : 'N/A'}</p>
                            <p><strong>Dirección:</strong> ${data.site.address}</p>
                            <p><strong>Teléfono:</strong> ${data.site.phone}</p>
                            <p><strong>Estado:</strong> 
                                <span class="badge badge-${data.site.status === 'active' ? 'success' : 'danger'}">
                                    ${data.site.status === 'active' ? 'Activo' : 'Inactivo'}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Estadísticas</strong></h6>
                            <p><strong>Conductores:</strong> ${data.stats.drivers}</p>
                            <p><strong>Cajeros:</strong> ${data.stats.cashiers}</p>
                            <p><strong>Coordinadores:</strong> ${data.stats.coordinates}</p>
                            <p><strong>Unidades:</strong> ${data.stats.units}</p>
                        </div>
                    </div>
                `,
                width: 600,
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#ff6600'
            });
        }).fail(() => {
            Swal.fire({
                title: 'Error',
                text: 'No se pudo cargar la información del sitio',
                icon: 'error',
                confirmButtonColor: '#ff6600'
            });
        });
    }

    window.viewSite = viewSite;
}
