// public/js/alerts.js
export function showSuccessAlert(message) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: message,
        timer: 2500,
        showConfirmButton: false,
        confirmButtonColor: '#28a745',
    });
}

export function showErrorAlert(message) {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: message,
        confirmButtonColor: '#dc3545',
    });
}

// Esta función es para mostrar alertas que vienen en window.alerts
export function initAlertsFromWindow() {
    if (!window.alerts) return;

    const { success, errors } = window.alerts;

    if (success) {
        showSuccessAlert(success);
    }

    if (errors && errors.length > 0) {
        showErrorAlert(errors.map(e => `<p>${e}</p>`).join(''));
    }
}
