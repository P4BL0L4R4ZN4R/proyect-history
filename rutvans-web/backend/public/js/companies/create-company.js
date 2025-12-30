// create-company.js
import { isEmailValid, isPhoneValid, isRequired, passwordsMatch, isMinLength } from './validation.js';

export function initCreateCompany() {
    const form = document.getElementById('createCompanyForm');

    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Campos de la empresa
        const name = form.name.value;
        const businessName = form.business_name.value;
        const rfc = form.rfc.value;
        const phone = form.phone.value;
        const email = form.email.value;
        const address = form.address.value;
        const status = form.status.value;

        // Campos del administrador
        const adminName = form.admin_name.value;
        const adminEmail = form.admin_email.value;
        const adminPassword = form.admin_password.value;
        const adminPasswordConfirm = form.admin_password_confirmation.value;

        // Validaciones
        const errors = [];

        // Validaciones de la empresa
        if (!isRequired(name)) errors.push('El nombre de la empresa es obligatorio');
        if (!isRequired(address)) errors.push('La dirección es obligatoria');
        if (!isRequired(phone)) errors.push('El teléfono es obligatorio');
        if (!isPhoneValid(phone)) errors.push('El formato del teléfono no es válido');
        if (email && !isEmailValid(email)) errors.push('El formato del email no es válido');
        if (rfc && rfc.length > 0 && rfc.length !== 13) errors.push('El RFC debe tener 13 caracteres');
        
        // Validaciones del administrador
        if (!isRequired(adminName)) errors.push('El nombre del administrador es obligatorio');
        if (!isRequired(adminEmail)) errors.push('El email del administrador es obligatorio');
        if (!isEmailValid(adminEmail)) errors.push('El formato del email del administrador no es válido');
        if (!isRequired(adminPassword)) errors.push('La contraseña del administrador es obligatoria');
        if (!isMinLength(adminPassword, 6)) errors.push('La contraseña debe tener al menos 6 caracteres');
        if (!passwordsMatch(adminPassword, adminPasswordConfirm)) errors.push('Las contraseñas no coinciden');

        if (errors.length > 0) {
            Swal.fire({
                title: 'Error de Validación',
                html: errors.map(error => `• ${error}`).join('<br>'),
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
            return false;
        }

        try {
            form.submit();
            
            Swal.fire({
                title: '¡Éxito!',
                text: 'La empresa y el administrador se han creado correctamente',
                icon: 'success',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        } catch (error) {
            Swal.fire({
                title: 'Error',
                text: 'Hubo un error al crear la empresa y el administrador',
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
        }
    });
}
