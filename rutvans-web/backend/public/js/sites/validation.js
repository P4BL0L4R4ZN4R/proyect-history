// public/js/sites/validation.js

export function isRequired(value) {
    return value !== undefined && value !== null && value.toString().trim() !== '';
}

export function isEmail(value) {
    if (!isRequired(value)) return false;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(value);
}

export function isPhone(value) {
    if (!isRequired(value)) return false;
    // Validación simple para teléfonos mexicanos: 10 dígitos numéricos
    const phoneRegex = /^\d{10}$/;
    return phoneRegex.test(value);
}

export function validateForm(fields) {
    const errors = {};

    for (const [field, rules] of Object.entries(fields)) {
        const value = rules.value;
        for (const rule of rules.rules) {
            switch (rule) {
                case 'required':
                    if (!isRequired(value)) errors[field] = 'Este campo es obligatorio.';
                    break;
                case 'email':
                    if (!isEmail(value)) errors[field] = 'Email no válido.';
                    break;
                case 'phone':
                    if (!isPhone(value)) errors[field] = 'Teléfono no válido. Debe ser 10 dígitos.';
                    break;
                // Puedes agregar más reglas si necesitas
            }
            if (errors[field]) break; // Si ya hay error, no evalúo más reglas para ese campo
        }
    }

    return errors;
}
