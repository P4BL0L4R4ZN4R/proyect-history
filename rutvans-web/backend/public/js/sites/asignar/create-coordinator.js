import { validateForm } from './validation.js';

export function initCreateCoordinator() {
    const form = document.getElementById('createCoordinatorForm');
    if (!form) return;

    const modalElement = document.getElementById('createCoordinatorModal');
    const bsModal = bootstrap.Modal.getOrCreateInstance(modalElement);

    form.addEventListener('submit', function handleSubmit(e) {
        e.preventDefault();

        // Obtener valores del formulario
        const fields = {
            name: form.querySelector('[name="name"]'),
            email: form.querySelector('[name="email"]'),
            password: form.querySelector('[name="password"]'),
            address: form.querySelector('[name="address"]'),
            phone_number: form.querySelector('[name="phone_number"]'),
            site_id: form.querySelector('[name="site_id"]')
        };

        // Validaciones
        const errors = validateForm(fields);

        // Limpiar errores previos
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        if (Object.keys(errors).length > 0) {
            // Mostrar errores con SweetAlert2
            Swal.fire({
                title: 'Error de Validación',
                html: Object.values(errors).map(error => `• ${error}`).join('<br>'),
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

        // Crear el coordinador
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
                text: data.message || 'El coordinador se ha creado correctamente',
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
                text: error.message || 'Hubo un error al crear el coordinador',
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
        });
    });

    const photoInput = form.querySelector('#photo_path');
    const photoPreview = form.querySelector('#photoPreview');

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    photoPreview.src = e.target.result;
                    photoPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                photoPreview.style.display = 'none';
                photoPreview.src = '#';
            }
        });
    }

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
