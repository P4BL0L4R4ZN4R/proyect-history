export function validateForm(fields) {
    const errors = {};

    // Validación para nombre
    if (fields.name) {
        if (!fields.name.value) {
            errors.name = 'El nombre es obligatorio.';
        } else if (fields.name.value.length < 3) {
            errors.name = 'El nombre debe tener al menos 3 caracteres.';
        }
    }

    // Validación para email
    if (fields.email) {
        const email = fields.email.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            errors.email = 'El correo electrónico es obligatorio.';
        } else if (!emailRegex.test(email)) {
            errors.email = 'El correo electrónico no es válido.';
        }
    }

    // Validación para contraseña
    if (fields.password) {
        const password = fields.password.value;
        if (!password) {
            errors.password = 'La contraseña es obligatoria.';
        } else if (password.length < 8) {
            errors.password = 'La contraseña debe tener al menos 8 caracteres.';
        }
    }

    // Validación para dirección
    if (fields.address) {
        if (!fields.address.value) {
            errors.address = 'La dirección es obligatoria.';
        } else if (fields.address.value.length < 5) {
            errors.address = 'La dirección debe tener al menos 5 caracteres.';
        }
    }

    // Validación para teléfono
    if (fields.phone_number) {
        const phone = fields.phone_number.value;
        const phoneDigitsOnly = phone.replace(/\D/g, '');
        if (!phone) {
            errors.phone_number = 'El teléfono es obligatorio.';
        } else if (phoneDigitsOnly.length !== 10) {
            errors.phone_number = 'El teléfono debe tener exactamente 10 dígitos.';
        } else if (!/^\+?\d+$/.test(phone)) {
            errors.phone_number = 'El teléfono solo debe contener números y opcionalmente un + al inicio.';
        }
    }

    // Validación para foto (input file)
    if (fields.photo) {
        const fileInput = document.getElementById('photo');
        if (fileInput && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            const maxSize = 2 * 1024 * 1024; // 2 MB en bytes

            if (!allowedTypes.includes(file.type)) {
                errors.photo = 'La foto debe ser un archivo JPG o PNG.';
            } else if (file.size > maxSize) {
                errors.photo = 'La foto no debe pesar más de 2 MB.';
            }
        }
    }

    return errors;
}
