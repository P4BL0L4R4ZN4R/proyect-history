// edit-company.js
import { isEmailValid, isPhoneValid, isRequired } from './validation.js';

// Función para enviar el formulario
function submitForm(form) {
    const formData = new FormData(form[0]);
    formData.append('_method', 'PUT');

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function() {
            $('#editCompanyModal').modal('hide');
            Swal.fire({
                title: '¡Éxito!',
                text: 'La empresa se ha actualizado correctamente',
                icon: 'success',
                confirmButtonColor: '#28a745'
            }).then(() => {
                window.location.reload();
            });
        },
        error: function() {
            Swal.fire({
                title: 'Error',
                text: 'Hubo un error al actualizar la empresa',
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
        }
    });
}

export function initEditCompany() {
    // Configurar CSRF token para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Manejar el click en el botón de editar
    $(document).on('click', '.btn-edit-company', function() {
        const button = $(this);
        const companyId = button.data('id');
        const form = $('#editCompanyForm');

        // Llenar el formulario con los datos del botón
        form.find('input[name="name"]').val(button.data('name'));
        form.find('input[name="business_name"]').val(button.data('business_name'));
        form.find('input[name="rfc"]').val(button.data('rfc'));
        form.find('select[name="locality_id"]').val(button.data('locality_id'));
        form.find('input[name="address"]').val(button.data('address'));
        form.find('input[name="phone"]').val(button.data('phone'));
        form.find('input[name="email"]').val(button.data('email'));
        form.find('select[name="status"]').val(button.data('status'));
        form.find('textarea[name="notes"]').val(button.data('notes'));

        form.attr('action', `/companies/${companyId}`);

        // Mostrar el modal
        $('#editCompanyModal').modal('show');

        // Agregar validación al enviar el formulario
        form.off('submit').on('submit', function(e) {
            e.preventDefault();
            
            const errors = [];
            const name = form.find('input[name="name"]').val();
            const phone = form.find('input[name="phone"]').val();
            const email = form.find('input[name="email"]').val();
            const address = form.find('input[name="address"]').val();
            const rfc = form.find('input[name="rfc"]').val();

            // Validaciones
            if (!isRequired(name)) errors.push('El nombre de la empresa es obligatorio');
            if (!isRequired(address)) errors.push('La dirección es obligatoria');
            if (!isRequired(phone)) errors.push('El teléfono es obligatorio');
            if (!isPhoneValid(phone)) errors.push('El formato del teléfono no es válido');
            if (email && !isEmailValid(email)) errors.push('El formato del email no es válido');
            if (rfc && rfc.length > 0 && rfc.length !== 13) errors.push('El RFC debe tener 13 caracteres');

            if (errors.length > 0) {
                Swal.fire({
                    title: 'Error de Validación',
                    html: errors.map(error => `• ${error}`).join('<br>'),
                    icon: 'error',
                    confirmButtonColor: '#28a745'
                });
                return false;
            }

            // Si no hay errores, enviamos el formulario
            submitForm(form);
        });
    });
}
