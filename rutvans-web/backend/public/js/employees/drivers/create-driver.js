import { showErrorAlert, showSuccessAlert } from '../../alerts.js';

export function setupCreateDriverModal() {
    const form = document.querySelector('#modalCreateDriver form');
    if (!form) return;

    // Corregir: Definir correctamente photoInput y preview si existen en el modal
    const photoInput = form.querySelector('input[type="file"][name="photo_file"]');
    const preview = document.getElementById('photoPreview');

    if (photoInput && preview) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                if (file.size > 2 * 1024 * 1024) {
                    showErrorAlert('La imagen debe ser menor a 2 MB.');
                    this.value = '';
                    preview.style.display = 'none';
                    preview.src = '#';
                    return;
                }
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                preview.src = '#';
            }
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const password = form.password.value;
        const passwordConfirmation = form.password_confirmation.value;
        const siteSelect = document.getElementById('site_id');

        const errors = [];
        const passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=<>?{}[\]~]).{8,}$/;

        if (!passwordRegex.test(password)) errors.push('La contraseña debe tener mínimo 8 caracteres, incluir letras, números y símbolos.');
        if (password !== passwordConfirmation) errors.push('Las contraseñas no coinciden.');

        // Solo admins deben validar el select de sitio
        if (window.currentUserRole === 'admin') {
            if (!siteSelect || !siteSelect.value) errors.push('Debes seleccionar un sitio.');
        }

        if (errors.length) {
            showErrorAlert(errors.join('<br>'));
            return;
        }

        const formData = new FormData(form);
        // Solo admins deben enviar el site_id
        if (window.currentUserRole === 'admin' && siteSelect) {
            formData.set('site_id', siteSelect.value);
        }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                const data = await response.json().catch(() => null);
                if (data && data.errors) {
                    const serverErrors = Object.values(data.errors).flat();
                    showErrorAlert(serverErrors.join('<br>'));
                } else {
                    showErrorAlert('Error desconocido al guardar el conductor.');
                }
                throw new Error('Error al guardar');
            }
            return response.json();
        })
        .then(data => {
            showSuccessAlert('Conductor creado correctamente.');

            // Cerrar modal y limpiar foco para evitar warnings aria-hidden
            const modalElement = document.getElementById('modalCreateDriver');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) modalInstance.hide();

            // Limpiar formulario
            form.reset();

            setTimeout(() => {
                location.reload();  // recarga para ver cambios reflejados
            }, 300);
        })
        .catch(err => {
            console.error(err);
        });
    });
}
