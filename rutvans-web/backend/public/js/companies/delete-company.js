// delete-company.js

export function initDeleteCompany() {
    window.deleteCompany = function (companyId, companyName) {
        Swal.fire({
            title: '¿Estás seguro?',
            html: `¿Deseas eliminar la empresa/sindicato <strong>${escapeHtml(companyName)}</strong>?<br><br>
                   <div class="alert alert-warning">
                       <i class="fas fa-exclamation-triangle"></i>
                       <strong>Advertencia:</strong> Esta acción también eliminará todos los sitios/rutas 
                       y usuarios asociados a esta empresa y es irreversible.
                   </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    method: 'POST',
                    action: `/companies/${companyId}`
                });

                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    type: 'hidden',
                    name: '_method',
                    value: 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
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
