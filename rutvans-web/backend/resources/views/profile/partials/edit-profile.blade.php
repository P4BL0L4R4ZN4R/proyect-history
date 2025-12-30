<!-- FORMULARIO PERFIL -->
<p class="text-muted mb-3">
    Puedes actualizar tu información personal haciendo clic en <strong>"Editar"</strong>. Los cambios no se guardarán hasta que presiones <strong>"Actualizar perfil"</strong>.
</p>

<form id="profile-form" method="POST" enctype="multipart/form-data" novalidate class="p-4 bg-white rounded-4 shadow border">
    @csrf
    @method('PUT')

    <!-- BOTÓN EDITAR -->
    <div class="mb-4 text-end">
        <button type="button" id="edit-button" class="btn btn-primary btn-lg">
            <i class="bi bi-pencil-fill me-2"></i>Editar
        </button>
    </div>

    <div class="row g-5 align-items-start">
        <!-- FOTO DE PERFIL -->
        <div class="col-md-4 text-center">
            <div class="position-relative d-inline-block">
                <img
                    id="photo-preview"
                    src="{{ $user->profile_photo_path && \App\Helpers\StorageHelper::fileExists($user->profile_photo_path) ? \App\Helpers\StorageHelper::getPublicUrl($user->profile_photo_path) : asset('images/default-profile.png') }}"
                    class="rounded-circle shadow border border-3"
                    style="width: 180px; height: 180px; object-fit: cover; transition: all 0.3s ease-in-out;"
                    alt="Foto de perfil"
                >
                <label
                    for="photo"
                    class="position-absolute bottom-0 end-0 p-2 bg-white rounded-circle border shadow-sm"
                    style="cursor: pointer;"
                    title="Cambiar foto"
                >
                    <i class="bi bi-camera-fill text-primary"></i>
                </label>
            </div>

            <input
                type="file"
                id="photo"
                name="photo"
                class="d-none"
                accept="image/*"
                onchange="previewPhoto(event)"
                disabled
            >
            <p id="file-name" class="form-text text-muted mt-2">Ningún archivo seleccionado</p>
            @error('photo')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block">Formatos permitidos: JPG, PNG, máx. 2MB</small>
        </div>

        <!-- INFORMACIÓN PERSONAL -->
        <div class="col-md-8">
          <h4 class="mb-4 text-naranja fw-bold">
    <i class="bi bi-person-vcard-fill me-2"></i> Información Personal
</h4>

            <div class="row g-4">
                <!-- Nombre -->
                <div class="col-md-6">
                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person-fill"></i></span>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}"
                            required
                            disabled
                        >
                    </div>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope-fill"></i></span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}"
                            required
                            disabled
                        >
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div class="col-md-6">
                    <label for="phone_number" class="form-label">Teléfono</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-telephone-fill"></i></span>
                        <input
                            type="text"
                            id="phone_number"
                            name="phone_number"
                            class="form-control @error('phone_number') is-invalid @enderror"
                            value="{{ old('phone_number', $user->phone_number) }}"
                            disabled
                        >
                    </div>
                    @error('phone_number')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Dirección -->
                <div class="col-md-6">
                    <label for="address" class="form-label">Dirección</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-geo-alt-fill"></i></span>
                        <textarea
                            id="address"
                            name="address"
                            rows="1"
                            class="form-control @error('address') is-invalid @enderror"
                            disabled
                        >{{ old('address', $user->address) }}</textarea>
                    </div>
                    @error('address')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <!-- BOTONES GUARDAR / CANCELAR -->
    <div id="action-buttons" class="save-button-container d-none flex-wrap justify-content-center gap-3">
        <button
            id="save-button"
            type="submit"
            class="btn btn-primary btn-lg d-flex justify-content-center align-items-center gap-2 shadow-sm"
        >
            <i class="bi bi-person-lines-fill"></i> Actualizar perfil
        </button>

        <button
            type="button"
            id="cancel-button"
            class="btn btn-danger btn-lg d-flex justify-content-center align-items-center gap-2 shadow-sm"
        >
            <i class="bi bi-x-circle-fill"></i> Cancelar
        </button>
    </div>
</form>

<style>
    .save-button-container {
        margin-top: 3rem;
        display: flex;
        justify-content: center;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
.text-naranja {
    color: #ff6f00 !important;
}

    #save-button,
    #cancel-button {
        width: 100%;
        max-width: 280px;
        font-size: 1.1rem;
        box-shadow: 0 4px 8px rgb(0 0 0 / 0.15);
        transition: background-color 0.3s ease;
    }

    #save-button:hover {
        background-color: #153bb7;
    }
</style>

<!-- SweetAlert2 (asegúrate que esta librería esté incluida en tu layout o antes de este script) -->

<script>
    let originalValues = {};
    let originalPhoto = document.getElementById('photo-preview').src;

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('profile-form');
        const inputs = form.querySelectorAll('input, textarea');

        inputs.forEach(input => {
            if (input.type !== 'file') {
                originalValues[input.name] = input.value;
            }
        });
    });

    function previewPhoto(event) {
        const input = event.target;
        const preview = document.getElementById('photo-preview');
        const fileNameDisplay = document.getElementById('file-name');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            const file = input.files[0];

            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(file);
            fileNameDisplay.textContent = file.name;
        } else {
            fileNameDisplay.textContent = "Ningún archivo seleccionado";
        }
    }

    document.getElementById('edit-button').addEventListener('click', () => {
        const form = document.getElementById('profile-form');
        const inputs = form.querySelectorAll('input, textarea, #photo');

        inputs.forEach(input => input.disabled = false);

        document.getElementById('edit-button').style.display = 'none';
        document.getElementById('action-buttons').classList.remove('d-none');
    });

document.getElementById('cancel-button').addEventListener('click', () => {
    const form = document.getElementById('profile-form');
    const inputs = form.querySelectorAll('input, textarea');

    inputs.forEach(input => {
        if (input.name && originalValues[input.name] !== undefined) {
            input.value = originalValues[input.name];
        } else if (input.type === 'file') {
            // Para el input file, limpia el valor para que no quede ningún archivo seleccionado
            input.value = '';
        }
        input.disabled = true;
    });

    // Restaurar la foto al valor original
    document.getElementById('photo-preview').src = originalPhoto;

    // Actualizar texto del nombre del archivo
    document.getElementById('file-name').textContent = "Ningún archivo seleccionado";

    // Mostrar botón Editar y ocultar botones Guardar/Cancelar
    document.getElementById('edit-button').style.display = 'inline-block';
    document.getElementById('action-buttons').classList.add('d-none');
});


    document.getElementById('profile-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback.d-block').forEach(el => el.remove());

        fetch("{{ route('profile.update') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
            const isJson = response.headers.get("content-type")?.includes("application/json");

            if (!response.ok) {
                const errorData = isJson ? await response.json() : { message: 'Error desconocido' };
                throw errorData;
            }

            return isJson ? response.json() : { message: 'Perfil actualizado correctamente' };
        })
        .then(data => {
            Swal.fire({
                type: 'success',
                title: '¡Éxito!',
                text: data.message || 'Perfil actualizado correctamente',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            });

            const inputs = document.querySelectorAll('#profile-form input, #profile-form textarea');
            inputs.forEach(input => {
                input.disabled = true;
                if (input.name) originalValues[input.name] = input.value;
            });

            originalPhoto = document.getElementById('photo-preview').src;

            document.getElementById('edit-button').style.display = 'inline-block';
            document.getElementById('action-buttons').classList.add('d-none');
        })
        .catch(error => {
            let errorMsg = 'Hubo un error al actualizar el perfil.';

            if (error.errors) {
                errorMsg = Object.values(error.errors).flat().join('\n');

                Object.keys(error.errors).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');

                        const errorDiv = document.createElement('div');
                        errorDiv.classList.add('invalid-feedback', 'd-block');
                        errorDiv.textContent = error.errors[field][0];

                        input.closest('.input-group')?.after(errorDiv) || input.after(errorDiv);
                    }
                });
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
</script>
