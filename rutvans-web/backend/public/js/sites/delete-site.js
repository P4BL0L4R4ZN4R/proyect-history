// Función auxiliar para escapar HTML y prevenir XSS
function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

export function initDeleteSite() {
    async function deleteSite(siteId, siteName) {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            html: `¿Deseas eliminar el sitio/ruta <strong>${escapeHtml(siteName)}</strong>?<br><br>
                   <div class="alert alert-warning">
                       <i class="fas fa-exclamation-triangle"></i>
                       <strong>Advertencia:</strong> Esta acción también eliminará todos los usuarios asociados 
                       exclusivamente a este sitio y es irreversible.
                   </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                // Mostrar loading
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Crear y enviar el formulario
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/clients/${siteId}`;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;

                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);

                await new Promise((resolve) => {
                    form.addEventListener('submit', () => {
                        setTimeout(resolve, 1000); // dar tiempo para que el formulario se envíe
                    });
                    form.submit();
                });

                await Swal.fire({
                    title: '¡Eliminado!',
                    text: 'El sitio ha sido eliminado correctamente',
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                });

                window.location.reload();
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'Hubo un error al eliminar el sitio',
                    icon: 'error',
                    confirmButtonColor: '#28a745'
                });
            }
        }
    }

    // Hacer la función accesible globalmente
    window.deleteSite = deleteSite;
}
