// validation.js
export function isEmailValid(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

export function isPhoneValid(phone) {
    // Simple validación, permite + y números, ajusta según necesites
    const re = /^\+?\d{7,15}$/;
    return re.test(phone);
}

export function isRequired(value) {
    return value && value.trim().length > 0;
}

export function passwordsMatch(pw1, pw2) {
    return pw1 === pw2;
}

export function isMinLength(value, min) {
    return value && value.length >= min;
}
