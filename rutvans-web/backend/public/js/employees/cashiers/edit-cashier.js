import { showErrorAlert, showSuccessAlert } from '../../alerts.js';

export function setupEditCashierModal() {
    const modalEditCashier = new bootstrap.Modal(document.getElementById('modalEditCashier'));
    const formEditCashier = document.getElementById('formEditCashier');
    const photoInput = document.getElementById('edit_photo_file');
    const currentPhotoPreview = document.getElementById('current_photo_preview');
    const noPhotoMessage = document.getElementById('no_photo_message');

    if (!formEditCashier) return;

    // Abrir modal y llenar formulario con datos del botón
    document.querySelectorAll('.btn-edit-cashier').forEach(button => {
        button.addEventListener('click', function () {
            const cashier = this.dataset;

            document.getElementById('edit_cashier_id').value = cashier.id;
            document.getElementById('edit_name').value = cashier.name;
            document.getElementById('edit_email').value = cashier.email;

            // Mostrar foto actual o mensaje si no hay
            if (cashier.photo) {
                currentPhotoPreview.src = cashier.photo;
                currentPhotoPreview.style.display = 'block';
                noPhotoMessage.style.display = 'none';
            } else {
                currentPhotoPreview.src = '#';
                currentPhotoPreview.style.display = 'none';
                noPhotoMessage.style.display = 'block';
            }

            formEditCashier.action = `/cashiers/${cashier.id}`;

            // Limpiar input file
            if (photoInput) {
                photoInput.value = '';
            }

            modalEditCashier.show();
        });
    });

    if (photoInput && currentPhotoPreview) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                if (file.size > 2 * 1024 * 1024) {
                    showErrorAlert('La imagen debe ser menor a 2 MB.');
                    this.value = '';
                    currentPhotoPreview.style.display = 'none';
                    currentPhotoPreview.src = '#';
                    noPhotoMessage.style.display = 'block';
                    return;
                }
                const reader = new FileReader();
                reader.onload = e => {
                    currentPhotoPreview.src = e.target.result;
                    currentPhotoPreview.style.display = 'block';
                    noPhotoMessage.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                currentPhotoPreview.style.display = 'none';
                currentPhotoPreview.src = '#';
                noPhotoMessage.style.display = 'block';
            }
        });
    }

    formEditCashier.addEventListener('submit', function (e) {
        e.preventDefault();

        const name = formEditCashier.name.value.trim();
        const email = formEditCashier.email.value.trim();
        const password = formEditCashier.password.value;
        const passwordConfirmation = formEditCashier.password_confirmation.value;
        const photoFile = photoInput.files[0];

        const errors = [];

        const passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=<>?{}[\]~]).{8,}$/;

        if (password) {
            if (!passwordRegex.test(password)) errors.push('La contraseña debe tener mínimo 8 caracteres, incluir letras, números y símbolos.');
            if (password !== passwordConfirmation) errors.push('Las contraseñas no coinciden.');
        }

        if (photoFile) {
            if (!photoFile.type.startsWith('image/')) errors.push('El archivo de foto debe ser una imagen válida.');
            if (photoFile.size > 2 * 1024 * 1024) errors.push('La imagen debe ser menor a 2 MB.');
        }

        if (errors.length) {
            showErrorAlert(errors.join('<br>'));
            return;
        }

        const validateImageDimensions = file => new Promise((resolve, reject) => {
            if (!file) return resolve(true);
            const img = new Image();
            img.onload = () => (img.width < 100 || img.height < 100)
                ? reject('La imagen debe tener un tamaño mínimo de 100x100 píxeles.')
                : resolve(true);
            img.onerror = () => reject('No se pudo cargar la imagen para validar dimensiones.');
            img.src = URL.createObjectURL(file);
        });

        validateImageDimensions(photoFile)
            .then(() => {
                const formData = new FormData(formEditCashier);

                fetch(formEditCashier.action, {
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
                            showErrorAlert('Error desconocido al actualizar el cajero.');
                        }
                        throw new Error('Error al guardar');
                    }
                    return response.json();
                })
                .then(data => {
                    showSuccessAlert('Cajero actualizado correctamente.');

                    modalEditCashier.hide();
                    formEditCashier.reset();
                    currentPhotoPreview.style.display = 'none';
                    currentPhotoPreview.src = '#';
                    noPhotoMessage.style.display = 'block';

                    setTimeout(() => {
                        location.reload();
                    }, 300);
                })
                .catch(err => console.error(err));
            })
            .catch(errMsg => {
                showErrorAlert(errMsg);
            });
    });
}
