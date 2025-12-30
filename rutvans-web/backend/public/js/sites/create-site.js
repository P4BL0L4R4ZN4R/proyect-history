import { isRequired, isPhone } from './validation.js';

export function initCreateSite() {
    const form = document.getElementById('createSiteForm');
    if (!form) return;

    const modalElement = document.getElementById('createSiteModal');
    const bsModal = bootstrap.Modal.getOrCreateInstance(modalElement);

    form.addEventListener('submit', function handleSubmit(e) {
        e.preventDefault();

        // Obtener valores del formulario
        const companyId = form.querySelector('[name="company_id"]').value.trim();
        const name = form.querySelector('[name="name"]').value.trim();
        const routeName = form.querySelector('[name="route_name"]').value.trim();
        const localityId = form.querySelector('[name="locality_id"]').value.trim();
        const address = form.querySelector('[name="address"]').value.trim();
        const phone = form.querySelector('[name="phone"]').value.trim();
        const status = form.querySelector('[name="status"]').value.trim();

        // Validaciones
        const errors = [];

        // Validaciones del sitio
        if (!isRequired(companyId)) errors.push('La compañía es obligatoria');
        if (!isRequired(name)) errors.push('El nombre del sitio es obligatorio');
        if (!isRequired(localityId)) errors.push('La localidad es obligatoria');
        if (!isRequired(address)) errors.push('La dirección es obligatoria');
        if (!isRequired(phone)) {
            errors.push('El teléfono es obligatorio');
        } else if (!/^\d{10}$/.test(phone)) {
            errors.push('El teléfono debe tener exactamente 10 dígitos.');
        }
        if (!isRequired(status)) errors.push('El estado es obligatorio');

        // Limpiar errores previos
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        if (errors.length > 0) {
            // Mostrar errores con SweetAlert2
            Swal.fire({
                title: 'Error de Validación',
                html: errors.map(error => `• ${error}`).join('<br>'),
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
            return;
        }

        // Mostrar loading
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Crear el sitio
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!response.ok) {
                let errorText = `Error: ${response.statusText} (${response.status})`;
                if (contentType && contentType.includes('application/json')) {
                    const errorData = await response.json();
                    errorText = errorData.message || JSON.stringify(errorData.errors);
                } else {
                    console.error("Respuesta de error no JSON del servidor:", await response.text().catch(() => ''));
                }
                throw new Error(errorText);
            }

            if (!contentType || !contentType.includes('application/json')) {
                console.error("Respuesta inesperada del servidor (no es JSON):", await response.text().catch(() => ''));
                throw new TypeError("Respuesta inesperada del servidor. Se esperaba JSON.");
            }

            return response.json();
        })
        .then(data => {
            Swal.fire({
                title: '¡Éxito!',
                text: data.message || 'El sitio se ha creado correctamente',
                icon: 'success',
                confirmButtonColor: '#28a745'
            }).then(() => {
                bsModal.hide();
                window.location.reload();
            });
        })
        .catch(error => {
            Swal.close(); // Cierra el Swal de "cargando"
            Swal.fire({
                title: 'Error',
                text: error.message || 'Hubo un error al crear el sitio',
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
        });
    });

    // Reset del formulario al abrir el modal
    modalElement.addEventListener('show.bs.modal', () => {
        form.reset();
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    });

    // Reset del formulario al cerrar con botones
    modalElement.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', () => {
            form.reset();
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        });
    });
}
