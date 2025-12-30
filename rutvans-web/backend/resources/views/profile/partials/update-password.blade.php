<form id="updatePasswordForm" class="p-4 bg-white rounded-4 shadow-sm border">
    @csrf
    @method('PUT')

    <h5 class="mb-3 text-primary fw-bold"><i class="bi bi-shield-lock-fill me-2"></i> Actualizar Contraseña</h5>
    <p class="text-muted mb-4">
        Por seguridad, ingresa tu contraseña actual antes de establecer una nueva.
    </p>

    <!-- Contraseña actual -->
    <div class="mb-3">
        <label for="current_password" class="form-label">Contraseña actual</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
            <input name="current_password" id="current_password" type="password"
                class="form-control"
                placeholder="Ingresa tu contraseña actual" required>
            <span class="input-group-text bg-light toggle-password" data-target="current_password" style="cursor:pointer;">
                <i class="bi bi-eye-fill"></i>
            </span>
        </div>
    </div>

    <!-- Nueva contraseña -->
    <div class="mb-3">
        <label for="new_password" class="form-label">Nueva contraseña</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-shield-lock"></i></span>
            <input name="password" id="new_password" type="password"
                class="form-control"
                placeholder="Mínimo 8 caracteres" required>
            <span class="input-group-text bg-light toggle-password" data-target="new_password" style="cursor:pointer;">
                <i class="bi bi-eye-fill"></i>
            </span>
        </div>
    </div>

    <!-- Confirmar nueva contraseña -->
    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-shield-check"></i></span>
            <input name="password_confirmation" id="password_confirmation" type="password"
                class="form-control" placeholder="Repite la nueva contraseña" required>
            <span class="input-group-text bg-light toggle-password" data-target="password_confirmation" style="cursor:pointer;">
                <i class="bi bi-eye-fill"></i>
            </span>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" id="submitPassword" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-arrow-repeat"></i> Actualizar contraseña
        </button>
    </div>
</form>

 <style>.text-naranja {
    color: #ff6f00 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Mostrar/Ocultar contraseña
    document.querySelectorAll('.toggle-password').forEach(el => {
        el.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-fill');
                icon.classList.add('bi-eye-slash-fill');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash-fill');
                icon.classList.add('bi-eye-fill');
            }
        });
    });

    // Envío del formulario con verificación previa de la contraseña actual
    const form = document.getElementById('updatePasswordForm');
    const submitBtn = document.getElementById('submitPassword');

    submitBtn.addEventListener('click', function () {
        const currentPassword = document.getElementById('current_password').value.trim();

        if (!currentPassword) {
            Swal.fire({
                type: 'warning',
                title: 'Campo requerido',
                text: 'Ingresa tu contraseña actual para continuar.',
                confirmButtonColor: '#f8bb86'
            });
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Validando...';

        // Primero validamos la contraseña actual
        fetch("{{ route('profile.verify-password') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ password: currentPassword })
        })
        .then(async res => {
            if (!res.ok) {
                const errorData = await res.json();
                throw errorData;
            }
            return res.json();
        })
        .then(() => {
            // Si la contraseña es correcta, enviamos la actualización
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Actualizando...';

            const formData = new FormData(form);
            // Enviar método PUT para que Laravel lo entienda
            formData.append('_method', 'PUT');

            return fetch("{{ route('profile.updatePassword') }}", {
                method: 'POST',  // POST + _method=PUT
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });
        })
        .then(async response => {
            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    type: 'success',
                    title: '¡Éxito!',
                    text: data.message || 'Contraseña actualizada correctamente',
                    confirmButtonColor: '#3085d6'
                });
                form.reset();
            } else {
                const errors = data.errors || {};
                const firstError = Object.values(errors)[0][0] || data.message || 'Error al actualizar la contraseña';

                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: firstError,
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: error.message || 'No se pudo actualizar la contraseña',
                confirmButtonColor: '#d33'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Actualizar contraseña';
        });
    });
});

</script>
