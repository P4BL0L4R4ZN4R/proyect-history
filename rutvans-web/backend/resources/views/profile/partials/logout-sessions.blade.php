<!-- Formulario para cerrar otras sesiones -->
<form id="logout-other-sessions-form" method="POST" action="{{ route('logout.other.sessions') }}">
    @csrf

    <div class="mb-3">
        <h4 class="text-danger fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill fs-4"></i> 
            Cerrar sesiones activas
        </h4>
        <p class="text-muted">
            Por seguridad, ingresa tu contraseña para cerrar todas las sesiones activas en otros dispositivos o navegadores.
        </p>

        <div class="input-group">
            <input
                type="password"
                class="form-control"
                id="logout_password"
                name="password"
                required
                placeholder="Ingresa tu contraseña"
            >
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye-slash" id="eyeIcon"></i>
            </button>
        </div>
        <div id="passwordError" class="text-danger small mt-1" style="display:none;"></div>
    </div>

    <button type="submit" class="btn btn-danger">
        <i class="bi bi-box-arrow-right me-1"></i>
        Cerrar otras sesiones
    </button>
</form>

<!-- Lista de sesiones -->
<div id="sessionsList" class="mt-5"></div>

<!-- Estilos -->
<style>
.session-card {
    background: #fff;
    border: 1px solid #e3e6f0;
    transition: box-shadow 0.3s ease;
}
.session-card:hover {
    box-shadow: 0 4px 20px rgb(0 0 0 / 0.1);
}
.icon-wrapper {
    width: 60px;
    height: 60px;
    flex-shrink: 0;
}
.session-details h5 {
    max-width: 250px;
}
.action-area button {
    min-width: 80px;
    font-weight: 600;
}
.session-actual {
    border: 2px solid #28a745 !important;
    background-color: #e6f4ea !important;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.3) !important;
}
.session-actual .icon-wrapper {
    background-color: #28a745 !important;
}
.session-actual h5 {
    color: #155724 !important;
}
</style>

<!-- Script para cargar sesiones dinámicamente -->
<script>
function cargarSesiones() {
    fetch('{{ route('sessions.index') }}')
        .then(response => response.json())
        .then(sessions => {
            const container = document.getElementById('sessionsList');
            container.innerHTML = sessions.map(session => {
                const deviceIcon = session.device === 'mobile' ? 'bi-phone-fill' : 'bi-laptop-fill';
                const current = session.is_current_device;

                return `
                    <div class="session-card mb-4 p-3 shadow rounded d-flex align-items-center gap-4 ${current ? 'session-actual' : ''}">
                        <div class="icon-wrapper text-white ${session.device === 'mobile' ? 'bg-info' : 'bg-primary'} rounded-circle d-flex justify-content-center align-items-center fs-2">
                            <i class="bi ${deviceIcon} fs-2"></i>
                        </div>
                        <div class="session-details flex-grow-1">
                            <h5 class="mb-1 fw-bold text-truncate" title="${session.agent}">${session.agent}</h5>
                            <div class="text-secondary small d-flex flex-wrap gap-3">
                                <div><i class="bi bi-globe2 me-1"></i> <span title="IP">${session.ip_address}</span></div>
                                <div><i class="bi bi-clock-history me-1"></i> <span title="Última actividad">${session.last_activity}</span></div>
                            </div>
                        </div>
                        <div class="action-area">
                            ${current
                                ? `<span class="badge bg-success d-flex align-items-center gap-2 fw-semibold rounded-pill shadow-sm px-3 py-2" style="font-size: 0.9rem;">
                                        <i class="bi bi-check-circle-fill fs-5"></i>
                                        <span>Sesión Actual</span>
                                   </span>`
                                : `<button class="btn btn-danger btn-sm d-flex align-items-center gap-2 shadow-sm" onclick="cerrarSesion('${session.id}')">
                                        <i class="bi bi-box-arrow-right fs-5"></i>
                                        <span>Cerrar Sesión</span>
                                   </button>`
                            }
                        </div>
                    </div>
                `;
            }).join('');
        })
        .catch(error => {
            console.error("Error cargando sesiones:", error);
        });
}

// Cargar sesiones al iniciar
document.addEventListener('DOMContentLoaded', cargarSesiones);

// Mostrar/ocultar contraseña
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('logout_password');
    const eyeIcon = document.getElementById('eyeIcon');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    eyeIcon.classList.toggle('bi-eye');
    eyeIcon.classList.toggle('bi-eye-slash');
});

// Manejar el cierre de todas las sesiones
document.getElementById('logout-other-sessions-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const passwordInput = document.getElementById('logout_password');
    const passwordErrorDiv = document.getElementById('passwordError');
    const eyeIcon = document.getElementById('eyeIcon');

    passwordErrorDiv.style.display = 'none';
    passwordErrorDiv.textContent = '';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async response => {
        const contentType = response.headers.get("content-type");
        const isJson = contentType && contentType.includes("application/json");

        if (!response.ok) {
            const errorData = isJson ? await response.json() : { message: 'Error desconocido' };
            throw errorData;
        }

        return isJson ? response.json() : { message: 'Sesiones cerradas correctamente' };
    })
    .then(data => {
        Swal.fire({
            type: 'success',
            title: '¡Éxito!',
            text: data.message || 'Se cerraron correctamente otras sesiones.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            form.reset();

            if (passwordInput.getAttribute('type') === 'text') {
                passwordInput.setAttribute('type', 'password');
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            }

            passwordErrorDiv.style.display = 'none';

            // 👉 Recargar sesiones sin recargar la página
            cargarSesiones();
        });
    })
    .catch(error => {
        let errorMsg = 'Error al cerrar sesiones.';
        if (error.errors) {
            errorMsg = Object.values(error.errors).flat().join('\n');
            if (error.errors.password) {
                passwordErrorDiv.style.display = 'block';
                passwordErrorDiv.textContent = error.errors.password[0];
            }
        } else if (error.message) {
            errorMsg = error.message;
        }

        Swal.fire({
            type: 'error',
            title: 'Error',
            text: errorMsg,
        });
    });
});

// Cerrar sesión individual
function cerrarSesion(sessionId) {
    Swal.fire({
        type: 'warning',
        title: '¿Estás seguro?',
        text: 'Esta acción cerrará la sesión en otro dispositivo.',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/profile/sessions/${sessionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                if (!res.ok) {
                    const errorText = await res.text();
                    throw new Error(errorText || 'No se pudo cerrar la sesión');
                }

                return res.json();
            })
            .then(data => {
                Swal.fire({
                    type: 'success',
                    title: 'Sesión cerrada',
                    text: data.message || 'La sesión fue cerrada correctamente',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    cargarSesiones(); // 🔄 Recargar sesiones sin refrescar la página
                });
            })
            .catch(error => {
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: error.message || 'No se pudo cerrar la sesión.',
                });
            });
        }
    });
}
</script>
