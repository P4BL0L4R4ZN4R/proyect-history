export function initEditAdmin() {
    const modalElement = document.getElementById('editAdminModal');
    if (!modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    const form = document.getElementById('editAdminForm');
    if (!form) return;

    document.querySelectorAll('.btn-edit-admin').forEach(button => {
        button.addEventListener('click', () => {
            const adminData = button.dataset;
            const companyId = adminData.companyId;
            const companyName = button.closest('tr').querySelector('td:nth-child(2) strong').textContent;
            
            // Determinar si es modo editar o agregar
            const hasAdmin = adminData.adminId && 
                             adminData.adminId !== '' && 
                             adminData.adminId !== 'null' && 
                             adminData.adminId !== 'undefined';
            const isEditMode = hasAdmin;

            // Configurar el modal según el modo
            setupModalForMode(isEditMode, companyName);
            
            // Configurar la acción del formulario
            if (isEditMode) {
                form.action = `/companies/${companyId}/admin`;
                document.getElementById('admin_form_method').value = 'PUT';
            } else {
                form.action = `/companies/${companyId}/admin`;
                document.getElementById('admin_form_method').value = 'POST';
            }

            // Llenar los campos
            form.querySelector('#edit_admin_name').value = adminData.adminName || '';
            form.querySelector('#edit_admin_email').value = adminData.adminEmail || '';
            form.querySelector('#edit_admin_id').value = adminData.adminId || '';

            // Limpiar contraseñas
            form.querySelector('#edit_admin_password').value = '';
            form.querySelector('#edit_admin_password_confirmation').value = '';

            modal.show();
        });
    });

    function setupModalForMode(isEditMode, companyName) {
        const modalHeader = document.getElementById('adminModalHeader');
        const modalTitle = document.getElementById('adminModalTitle');
        const modalIcon = document.getElementById('adminModalIcon');
        const submitButton = document.getElementById('submitButton');
        const submitIcon = document.getElementById('submitIcon');
        const submitText = document.getElementById('submitText');
        const noAdminAlert = document.getElementById('noAdminAlert');
        const companyNameSpan = document.getElementById('modalCompanyName');
        const passwordLabel = document.getElementById('passwordLabel');
        const passwordHint = document.getElementById('passwordHint');
        const passwordConfirmLabel = document.getElementById('passwordConfirmLabel');
        const passwordField = document.getElementById('edit_admin_password');

        // Establecer nombre de la empresa
        companyNameSpan.textContent = companyName;

        if (isEditMode) {
            // Modo Editar
            modalHeader.className = 'modal-header bg-warning text-dark';
            modalTitle.textContent = 'Editar Administrador';
            modalIcon.className = 'fas fa-user-edit me-2';
            submitButton.className = 'btn btn-warning text-white';
            submitIcon.className = 'fas fa-save me-1';
            submitText.textContent = 'Guardar Cambios';
            noAdminAlert.style.display = 'none';
            passwordLabel.textContent = 'Nueva Contraseña';
            passwordHint.style.display = 'block';
            passwordConfirmLabel.textContent = 'Confirmar Nueva Contraseña';
            passwordField.required = false;
        } else {
            // Modo Agregar
            modalHeader.className = 'modal-header bg-success text-white';
            modalTitle.textContent = 'Agregar Administrador';
            modalIcon.className = 'fas fa-user-plus me-2';
            submitButton.className = 'btn btn-success';
            submitIcon.className = 'fas fa-plus me-1';
            submitText.textContent = 'Agregar Administrador';
            noAdminAlert.style.display = 'block';
            passwordLabel.textContent = 'Contraseña';
            passwordHint.style.display = 'none';
            passwordConfirmLabel.textContent = 'Confirmar Contraseña';
            passwordField.required = true;
        }
    }

    form.addEventListener('submit', function handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const isAddMode = document.getElementById('admin_form_method').value === 'POST';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Error al ${isAddMode ? 'agregar' : 'actualizar'} el administrador`);
            }

            return response.json();
        })
        .then(data => {
            Swal.fire({
                title: '¡Éxito!',
                text: `El administrador se ha ${isAddMode ? 'agregado' : 'actualizado'} correctamente`,
                icon: 'success',
                confirmButtonColor: '#28a745'
            }).then(() => {
                modal.hide();
                window.location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: error.message,
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
        });
    });
}
