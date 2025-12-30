<!-- Modal Editar Envío -->



<div class="modal fade" id="editEnvioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content edit-envio-modal">
            <div class="modal-header">
                <div class="modal-title-container">
                    <div class="modal-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Editar Envío</h5>
                        <p class="modal-subtitle">Modifique la información del envío</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
    <i class="fas fa-times"></i>
</button>
            </div>
            <form id="editEnvioForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editEnvioId" name="id">
                <div class="modal-body">

                    <!-- Navegación de secciones -->
                    <div class="section-navigation">
                        <div class="nav-step active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-info">
                                <span class="step-title">Información Básica</span>
                                <span class="step-subtitle">Datos principales</span>
                            </div>
                        </div>
                        <div class="nav-step" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-info">
                                <span class="step-title">Logística</span>
                                <span class="step-subtitle">Transporte y servicio</span>
                            </div>
                        </div>
                        <div class="nav-step" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-info">
                                <span class="step-title">Detalles del Paquete</span>
                                <span class="step-subtitle">Contenido e imagen</span>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
                    <div class="form-section active" id="edit-section-1">
                        <div class="section-header">
                            <h6>
                                <i class="fas fa-info-circle"></i>
                                Información Básica del Envío
                            </h6>
                            <p class="section-help">Modifique los datos principales del remitente y destinatario</p>
                        </div>
                        <div class="section-content">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editSenderName" class="form-label">
                                            <i class="fas fa-user"></i>
                                            Remitente *
                                        </label>
                                        <input type="text" class="form-control custom-input" id="editSenderName" name="sender_name" required placeholder="Nombre completo del remitente">
                                        <div class="form-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editReceiverName" class="form-label">
                                            <i class="fas fa-user-check"></i>
                                            Destinatario *
                                        </label>
                                        <input type="text" class="form-control custom-input" id="editReceiverName" name="receiver_name" required placeholder="Nombre completo del destinatario">
                                        <div class="form-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editAmount" class="form-label">
                                            <i class="fas fa-money-bill-wave"></i>
                                            Monto del Envío *
                                        </label>
                                        <div class="input-with-icon">
                                            <span class="input-icon">$</span>
                                            <input type="number" step="0.01" class="form-control custom-input" id="editAmount" name="amount" required placeholder="0.00" min="0">
                                        </div>
                                        <div class="form-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editStatus" class="form-label">
                                            <i class="fas fa-tasks"></i>
                                            Estado *
                                        </label>
                                        <select class="form-control custom-select" id="editStatus" name="status" required>
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="En camino">En Camino</option>
                                            <option value="Entregado">Entregado</option>
                                            <option value="Cancelado">Cancelado</option>
                                        </select>
                                        <div class="form-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="section-footer">
                            <button type="button" class="btn btn-next" data-next="2">
                                Siguiente: Logística
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: LOGÍSTICA -->
                    <div class="form-section" id="edit-section-2">
                        <div class="section-header">
                            <h6>
                                <i class="fas fa-truck"></i>
                                Configuración de Logística
                            </h6>
                            <p class="section-help">Actualice la unidad y servicio para el transporte</p>
                        </div>
                        <div class="section-content">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editRouteUnitId" class="form-label">
                                            <i class="fas fa-truck-loading"></i>
                                            Unidad de Ruta *
                                        </label>
                                        <select class="form-control custom-select" id="editRouteUnitId" name="route_unit_id" required>
                                            <option value="">Seleccionar unidad de transporte</option>
                                            @foreach($rutasUnidades as $unidad)
                                                <option value="{{ $unidad->id }}">Unidad #{{ $unidad->id }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editServiceId" class="form-label">
                                            <i class="fas fa-concierge-bell"></i>
                                            Tipo de Servicio *
                                        </label>
                                        <select class="form-control custom-select" id="editServiceId" name="service_id" required>
                                            <option value="">Seleccionar tipo de servicio</option>
                                            @foreach($servicios as $servicio)
                                                <option value="{{ $servicio->id }}">{{ $servicio->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="section-footer">
                            <button type="button" class="btn btn-prev" data-prev="1">
                                <i class="fas fa-arrow-left"></i>
                                Anterior
                            </button>
                            <button type="button" class="btn btn-next" data-next="3">
                                Siguiente: Detalles del Paquete
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: DETALLES DEL PAQUETE -->
                    <div class="form-section" id="edit-section-3">
                        <div class="section-header">
                            <h6>
                                <i class="fas fa-box-open"></i>
                                Detalles del Contenido
                            </h6>
                            <p class="section-help">Actualice la descripción e imagen del paquete</p>
                        </div>
                        <div class="section-content">
                            <div class="form-group">
                                <label for="editPackageDescription" class="form-label">
                                    <i class="fas fa-align-left"></i>
                                    Descripción del Paquete
                                </label>
                                <textarea class="form-control custom-textarea" id="editPackageDescription" name="package_description" rows="4" placeholder="Describa detalladamente el contenido del paquete..."></textarea>
                                <div class="char-counter">
                                    <span id="editCharCount">0</span>/500 caracteres
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="editPackageImage" class="form-label">
                                    <i class="fas fa-camera"></i>
                                    Imagen del Paquete
                                </label>
                                <div class="file-upload-container">
                                    <input type="file" class="file-input" id="editPackageImage" name="package_image" accept="image/*">
                                    <label for="editPackageImage" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span class="file-upload-text">Haga clic para cambiar la imagen</span>
                                            <span class="file-upload-hint">PNG, JPG, JPEG - Máximo 5MB</span>
                                        </div>
                                    </label>
                                    <div class="current-image-preview mt-3">
                                        <h6 class="current-image-title">Imagen Actual:</h6>
                                        <div id="currentImage" class="current-image-container">
                                            <!-- Imagen actual se cargará aquí -->
                                        </div>
                                        <div class="form-text">Deje en blanco para mantener la imagen actual</div>
                                    </div>
                                    <div class="file-preview" id="editFilePreview"></div>
                                </div>
                            </div>
                        </div>
                        <div class="section-footer">
                            <button type="button" class="btn btn-prev" data-prev="2">
                                <i class="fas fa-arrow-left"></i>
                                Anterior
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Actualizar Envío
                            </button>
                        </div>
                    </div>

                    <!-- Campos ocultos requeridos -->
                    <input type="hidden" name="schedule_id" value="1">
                    <input type="hidden" name="route_id" value="1">
                </div>
            </form>
        </div>
    </div>
</div>


<style>

        /* Estilos para el nuevo botón de cerrar con ícono X */
    .btn-close-custom {
        padding: 0;
        cursor: pointer;
        background: transparent; /* QUITAMOS EL FONDO */
        border: 0;
        opacity: 0.8;
        transition: opacity 0.15s ease-in-out;
        color: white; /* Color del icono X (ya estaba en blanco) */
        font-size: 1.25rem; /* Tamaño del icono X */
    }

    .btn-close-custom:hover {
        opacity: 1;
    }

    /* Asegura que el ícono esté centrado y visible */
    .create-envio-modal .modal-header {
        /* ... (tus estilos existentes) ... */
        /* Aseguramos que el botón X esté alineado correctamente */
        display: flex;
        justify-content: space-between;
    }
    /* Estilos específicos para el modal de edición */
    .edit-envio-modal {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        overflow: hidden;
    }

    .edit-envio-modal .modal-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: none;
    }

    .modal-title-container {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .modal-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
    }

    .modal-icon i {
        font-size: 1.5rem;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .modal-subtitle {
        opacity: 0.9;
        font-size: 0.875rem;
        margin: 0;
    }

    .edit-envio-modal .btn-close {
        filter: invert(1);
        opacity: 0.8;
    }

    .edit-envio-modal .modal-body {
        padding: 1.5rem;
        max-height: 70vh;
        overflow-y: auto;
    }

    /* Vista previa de imagen actual */
    .current-image-preview {
        border-top: 1px solid var(--gray-200);
        padding-top: 1rem;
    }

    .current-image-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.75rem;
    }

    .current-image-container {
        text-align: center;
        margin-bottom: 1rem;
    }

    .current-image {
        max-width: 200px;
        max-height: 150px;
        border-radius: 8px;
        box-shadow: var(--shadow);
        border: 2px solid var(--gray-200);
    }

    .no-image {
        padding: 2rem;
        background: var(--gray-100);
        border-radius: 8px;
        color: var(--gray-500);
        font-style: italic;
    }

    /* Reutilizar estilos del modal de crear */
    .edit-envio-modal .section-navigation,
    .edit-envio-modal .form-section,
    .edit-envio-modal .section-header,
    .edit-envio-modal .section-content,
    .edit-envio-modal .section-footer,
    .edit-envio-modal .form-group,
    .edit-envio-modal .form-label,
    .edit-envio-modal .custom-input,
    .edit-envio-modal .custom-select,
    .edit-envio-modal .custom-textarea,
    .edit-envio-modal .input-with-icon,
    .edit-envio-modal .file-upload-container,
    .edit-envio-modal .btn-prev,
    .edit-envio-modal .btn-next,
    .edit-envio-modal .btn-primary {
        /* Todos estos estilos son los mismos que en el modal de crear */
    }

    /* Personalización específica para edición */
    .edit-envio-modal .nav-step.active .step-number {
        background: var(--primary);
    }

    .edit-envio-modal .btn-next {
        background: var(--primary);
    }

    .edit-envio-modal .btn-next:hover {
        background: var(--primary);
    }

    .edit-envio-modal .btn-primary {
        background: var(--primary);
    }

    .edit-envio-modal .btn-primary:hover {
        background: var(--primary);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentEditSection = 1;
        const totalEditSections = 3;

        // Inicializar navegación para edición
        initEditNavigation();

        // Cargar datos en el modal de edición
        $('#editEnvioModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const modal = $(this);

            // Configurar el formulario
            const envioId = button.data('envio-id');
            modal.find('#editEnvioForm').attr('action', `/envios/${envioId}`);
            modal.find('#editEnvioId').val(envioId);
            modal.find('#editSenderName').val(button.data('envio-sender'));
            modal.find('#editReceiverName').val(button.data('envio-receiver'));
            modal.find('#editAmount').val(button.data('envio-amount'));
            modal.find('#editPackageDescription').val(button.data('envio-description'));
            modal.find('#editRouteUnitId').val(button.data('envio-route-unit'));
            modal.find('#editStatus').val(button.data('envio-status'));
            modal.find('#editServiceId').val(button.data('envio-service'));

            // Actualizar contador de caracteres
            const description = button.data('envio-description') || '';
            $('#editCharCount').text(description.length);

            // Cargar imagen actual si existe
            loadCurrentImage(envioId);

            // Reiniciar navegación
            showEditSection(1);
        });

        function initEditNavigation() {
            // Mostrar solo la primera sección
            showEditSection(1);

            // Event listeners para botones de navegación
            document.querySelectorAll('#editEnvioModal .btn-next').forEach(btn => {
                btn.addEventListener('click', function() {
                    const nextSection = parseInt(this.dataset.next);
                    if (validateEditSection(currentEditSection)) {
                        showEditSection(nextSection);
                    }
                });
            });

            document.querySelectorAll('#editEnvioModal .btn-prev').forEach(btn => {
                btn.addEventListener('click', function() {
                    const prevSection = parseInt(this.dataset.prev);
                    showEditSection(prevSection);
                });
            });

            // Event listeners para pasos de navegación
            document.querySelectorAll('#editEnvioModal .nav-step').forEach(step => {
                step.addEventListener('click', function() {
                    const targetSection = parseInt(this.dataset.step);
                    if (targetSection <= currentEditSection || validateEditSection(currentEditSection)) {
                        showEditSection(targetSection);
                    }
                });
            });
        }

        function showEditSection(sectionNumber) {
            // Ocultar todas las secciones
            document.querySelectorAll('#editEnvioModal .form-section').forEach(section => {
                section.classList.remove('active');
            });

            // Mostrar sección actual
            document.getElementById(`edit-section-${sectionNumber}`).classList.add('active');

            // Actualizar navegación
            document.querySelectorAll('#editEnvioModal .nav-step').forEach(step => {
                step.classList.remove('active');
                if (parseInt(step.dataset.step) === sectionNumber) {
                    step.classList.add('active');
                }
            });

            currentEditSection = sectionNumber;
        }

        function validateEditSection(sectionNumber) {
            const section = document.getElementById(`edit-section-${sectionNumber}`);
            const requiredFields = section.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');

                    const feedback = field.closest('.form-group')?.querySelector('.form-feedback');
                    if (feedback) {
                        feedback.textContent = 'Este campo es obligatorio';
                    }
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');

                    const feedback = field.closest('.form-group')?.querySelector('.form-feedback');
                    if (feedback) {
                        feedback.textContent = '';
                    }
                }
            });

            if (!isValid) {
                showNotification('Por favor complete todos los campos obligatorios de esta sección.', 'warning');
            }

            return isValid;
        }

        // Contador de caracteres para edición
        const editDescriptionTextarea = document.getElementById('editPackageDescription');
        const editCharCount = document.getElementById('editCharCount');

        if (editDescriptionTextarea && editCharCount) {
            editDescriptionTextarea.addEventListener('input', function() {
                editCharCount.textContent = this.value.length;

                if (this.value.length > 500) {
                    this.value = this.value.substring(0, 500);
                    editCharCount.textContent = 500;
                    editCharCount.style.color = 'var(--danger)';
                } else if (this.value.length > 450) {
                    editCharCount.style.color = 'var(--warning)';
                } else {
                    editCharCount.style.color = 'var(--gray-500)';
                }
            });
        }

        // Vista previa de imagen para edición
        const editFileInput = document.getElementById('editPackageImage');
        const editFilePreview = document.getElementById('editFilePreview');

        if (editFileInput && editFilePreview) {
            editFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (!file.type.startsWith('image/')) {
                        showNotification('Por favor, seleccione un archivo de imagen válido.', 'error');
                        editFileInput.value = '';
                        editFilePreview.style.display = 'none';
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        showNotification('La imagen no debe superar los 5MB.', 'error');
                        editFileInput.value = '';
                        editFilePreview.style.display = 'none';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        editFilePreview.innerHTML = `
                            <div class="preview-container">
                                <h6 class="current-image-title">Nueva Imagen:</h6>
                                <img src="${e.target.result}" alt="Vista previa de nueva imagen">
                                <button type="button" class="preview-remove" onclick="removeEditImagePreview()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        editFilePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    editFilePreview.style.display = 'none';
                }
            });
        }

        // Validación del formulario completo al enviar
        const editForm = document.getElementById('editEnvioForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                let isValid = true;

                // Validar todas las secciones
                for (let i = 1; i <= totalEditSections; i++) {
                    if (!validateEditSection(i)) {
                        isValid = false;
                        showEditSection(i); // Ir a la sección con error
                        break;
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    showNotification('Por favor complete todos los campos obligatorios antes de actualizar.', 'warning');
                } else {
                    // Mostrar loading
                    const submitBtn = editForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
                    submitBtn.disabled = true;

                    // Restaurar botón después de 3 segundos (por si hay error)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 3000);
                }
            });
        }

        function loadCurrentImage(envioId) {
            // Aquí puedes hacer una petición AJAX para obtener la imagen actual del envío
            // Por ahora, mostramos un placeholder
            const currentImageContainer = document.getElementById('currentImage');
            currentImageContainer.innerHTML = `
                <div class="no-image">
                    <i class="fas fa-image fa-2x mb-2"></i>
                    <div>Imagen no disponible</div>
                </div>
            `;

            // En una implementación real, harías:
            // fetch(`/envios/${envioId}/image`)
            //     .then(response => response.json())
            //     .then(data => {
            //         if (data.image_url) {
            //             currentImageContainer.innerHTML = `
            //                 <img src="${data.image_url}" alt="Imagen actual" class="current-image">
            //             `;
            //         }
            //     });
        }

        function showNotification(message, type) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });

            Toast.fire({
                icon: type,
                title: message
            });
        }
    });

    // Función global para eliminar vista previa de imagen en edición
    function removeEditImagePreview() {
        const editFileInput = document.getElementById('editPackageImage');
        const editFilePreview = document.getElementById('editFilePreview');

        if (editFileInput) editFileInput.value = '';
        if (editFilePreview) {
            editFilePreview.style.display = 'none';
            editFilePreview.innerHTML = '';
        }
    }
</script>
