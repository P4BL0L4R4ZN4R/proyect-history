<div class="modal fade" id="createEnvioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content create-envio-modal">
            <div class="modal-header">
                <div class="modal-title-container">
                    <div class="modal-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Nuevo Envío</h5>
                        <p class="modal-subtitle">Complete la información del envío paso a paso</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
    <i class="fas fa-times"></i>
</button>
            </div>
            <form action="{{ route('envios.store') }}" method="POST" enctype="multipart/form-data" id="createEnvioForm">
                @csrf
                <div class="modal-body">
                    
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

                    <div class="form-section active" id="section-1">
                        <div class="section-header">
                            <h6>
                                <i class="fas fa-info-circle"></i>
                                Información Básica del Envío
                            </h6>
                            <p class="section-help">Complete los datos principales del remitente y destinatario</p>
                        </div>
                        <div class="section-content">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sender_name" class="form-label">
                                            <i class="fas fa-user"></i>
                                            Remitente *
                                        </label>
                                        <input type="text" class="form-control custom-input" id="sender_name" name="sender_name" required placeholder="Nombre completo del remitente">
                                        <div class="form-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="receiver_name" class="form-label">
                                            <i class="fas fa-user-check"></i>
                                            Destinatario *
                                        </label>
                                        <input type="text" class="form-control custom-input" id="receiver_name" name="receiver_name" required placeholder="Nombre completo del destinatario">
                                        <div class="form-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount" class="form-label">
                                            <i class="fas fa-money-bill-wave"></i>
                                            Monto del Envío *
                                        </label>
                                        <div class="input-with-icon">
                                            <span class="input-icon">$</span>
                                            <input type="number" step="0.01" class="form-control custom-input" id="amount" name="amount" required placeholder="0.00" min="0">
                                        </div>
                                        <div class="form-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label">
                                            <i class="fas fa-tasks"></i>
                                            Estado Inicial *
                                        </label>
                                        <select class="form-control custom-select" id="status" name="status" required>
                                            <option value="" disabled selected>Seleccione un Estado Inicial</option> 
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

                    <div class="form-section" id="section-2">
                        <div class="section-header">
                            <h6>
                                <i class="fas fa-truck"></i>
                                Configuración de Logística
                            </h6>
                            <p class="section-help">Seleccione la unidad y servicio para el transporte</p>
                        </div>
                        <div class="section-content">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="route_unit_id" class="form-label">
                                            <i class="fas fa-truck-loading"></i>
                                            Unidad de Ruta *
                                        </label>
                                        <select class="form-control custom-select" id="route_unit_id" name="route_unit_id" required>
                                            <option value="">Seleccionar unidad de transporte</option>
                                            @foreach($rutasUnidades as $unidad)
                                                <option value="{{ $unidad->id }}">Unidad #{{ $unidad->id }} - {{ $unidad->tipo ?? 'Transporte' }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service_id" class="form-label">
                                            <i class="fas fa-concierge-bell"></i>
                                            Tipo de Servicio *
                                        </label>
                                        <select class="form-control custom-select" id="service_id" name="service_id" required>
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

                    <div class="form-section" id="section-3">
                        <div class="section-header">
                            <h6>
                                <i class="fas fa-box-open"></i>
                                Detalles del Contenido
                            </h6>
                            <p class="section-help">Describa el paquete y agregue una imagen de referencia</p>
                        </div>
                        <div class="section-content">
                            <div class="form-group">
                                <label for="package_description" class="form-label">
                                    <i class="fas fa-align-left"></i>
                                    Descripción del Paquete
                                </label>
                                <textarea class="form-control custom-textarea" id="package_description" name="package_description" rows="4" placeholder="Describa detalladamente el contenido del paquete, dimensiones, peso, y cualquier información relevante..."></textarea>
                                <div class="char-counter">
                                    <span id="charCount">0</span>/500 caracteres
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="package_image" class="form-label">
                                    <i class="fas fa-camera"></i>
                                    Imagen del Paquete (Opcional)
                                </label>
                                <div class="file-upload-container">
                                    <input type="file" class="file-input" id="package_image" name="package_image" accept="image/*">
                                    <label for="package_image" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span class="file-upload-text">Haga clic para subir una imagen del paquete</span>
                                            <span class="file-upload-hint">PNG, JPG, JPEG - Máximo 5MB</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="filePreview"></div>
                                </div>
                            </div>
                        </div>
                        <div class="section-footer">
                            <button type="button" class="btn btn-prev" data-prev="2">
                                <i class="fas fa-arrow-left"></i>
                                Anterior
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i>
                                Crear Envío
                            </button>
                        </div>
                    </div>
                    
                    <input type="hidden" name="schedule_id" value="1">
                    <input type="hidden" name="route_id" value="1">
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ---------------------------------------------------- */
/* ESTILOS DE MODAL Y NAVEGACIÓN (SIN CAMBIOS) */
/* ---------------------------------------------------- */
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
.create-envio-modal {
    border: none;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
    overflow: hidden;
}

.create-envio-modal .modal-header {
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

.create-envio-modal .btn-close {
    filter: invert(1);
    opacity: 0.8;
}

.create-envio-modal .modal-body {
    padding: 1.5rem;
    max-height: 70vh;
    overflow-y: auto;
}

/* Navegación de secciones */
.section-navigation {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    padding: 0 0.5rem;
}

.nav-step {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
    max-width: 200px;
    opacity: 0.6;
}

.nav-step.active {
    opacity: 1;
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.2);
}

.step-number {
    width: 32px;
    height: 32px;
    background: var(--gray-300);
    color: var(--gray-600);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.nav-step.active .step-number {
    background: var(--primary);
    color: white;
}

.step-info {
    display: flex;
    flex-direction: column;
}

.step-title {
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--gray-800);
}

.step-subtitle {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.125rem;
}

/* Secciones del formulario */
.form-section {
    display: none;
    animation: fadeIn 0.3s ease;
}

.form-section.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.section-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--gray-200);
}

.section-header h6 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 700;
    color: var(--gray-800);
    font-size: 1.125rem;
    margin-bottom: 0.5rem;
}

.section-header h6 i {
    color: var(--primary);
}

.section-help {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin: 0;
    line-height: 1.4;
}

.section-content {
    margin-bottom: 2rem;
}

.section-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
}


/* ---------------------------------------------------- */
/* ESTILOS DE FORMULARIO (MODIFICADOS PARA ARREGLAR RECORTE) */
/* ---------------------------------------------------- */

.form-group {
    /* CORRECCIÓN #1: Aumento de margen para arreglar recorte de borde inferior */
    margin-bottom: 2.5rem !important;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-label i {
    color: var(--primary);
    font-size: 0.875rem;
    width: 16px;
}

.custom-input, .custom-select, .custom-textarea {
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    /* Usamos el padding base, pero lo ajustamos en el custom-select para anular conflictos */
    padding: 0.75rem 1rem; 
    font-size: 0.9rem;
    transition: all 0.2s ease;
    background: white;
    width: 100%;
}

.custom-input:focus, .custom-select:focus, .custom-textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    outline: none;
}

.input-with-icon {
    position: relative;
    width: 100%;
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-500);
    font-weight: 600;
    z-index: 2;
}

.input-with-icon .custom-input {
    padding-left: 2.5rem;
}

.custom-select {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 16px 12px;
    
    height: auto !important;
    min-height: 40px !important; /* Igual a un input normal */
    
    padding: 0.75rem 2.5rem 0.75rem 1rem !important;

    line-height: 1.4 !important;
    box-sizing: border-box !important;

    display: flex;
    align-items: center;

    background-position: right 1rem center !important;
}

.custom-textarea {
    resize: vertical;
    min-height: 120px;
}

.char-counter {
    text-align: right;
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

.file-upload-container {
    position: relative;
    width: 100%;
}

.file-input {
    position: absolute;
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    z-index: -1;
}

.file-upload-label {
    display: block;
    border: 2px dashed var(--gray-300);
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--gray-50);
    width: 100%;
}

.file-upload-label:hover {
    border-color: var(--primary);
    background: rgba(99, 102, 241, 0.05);
}

.file-upload-content i {
    font-size: 2rem;
    color: var(--gray-400);
    margin-bottom: 0.5rem;
    display: block;
}

.file-upload-text {
    display: block;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.25rem;
}

.file-upload-hint {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.file-preview {
    margin-top: 1rem;
    display: none;
    text-align: center;
}

.preview-container {
    position: relative;
    display: inline-block;
}

.file-preview img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: var(--shadow);
}

.preview-remove {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    background: var(--danger);
    color: white;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.preview-remove:hover {
    background: #dc2626;
    transform: scale(1.1);
}

/* Botones de navegación */
.btn-prev, .btn-next {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    border: 1px solid var(--gray-300);
    background: white;
    color: var(--gray-700);
}

.btn-next {
    background: var(--primary);
    color: white;
    border: none;
}

.btn-next:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

.btn-prev:hover {
    background: var(--gray-100);
}

.btn-primary {
    background: var(--primary);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

/* Responsive */
@media (max-width: 768px) {
    .section-navigation {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .nav-step {
        max-width: none;
    }
    
    .section-footer {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-prev, .btn-next, .btn-primary {
        width: 100%;
        justify-content: center;
    }
}

.row.g-3 {
    --bs-gutter-x: 1rem;
    --bs-gutter-y: 1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentSection = 1;
    const totalSections = 3;
    
    // Inicializar navegación
    initNavigation();

    function initNavigation() {
        // Mostrar solo la primera sección
        showSection(1);

        // Event listeners para botones de navegación
        document.querySelectorAll('.btn-next').forEach(btn => {
            btn.addEventListener('click', function() {
                const nextSection = parseInt(this.dataset.next);
                if (validateSection(currentSection)) {
                    showSection(nextSection);
                }
            });
        });

        document.querySelectorAll('.btn-prev').forEach(btn => {
            btn.addEventListener('click', function() {
                const prevSection = parseInt(this.dataset.prev);
                showSection(prevSection);
            });
        });

        // Event listeners para pasos de navegación
        document.querySelectorAll('.nav-step').forEach(step => {
            step.addEventListener('click', function() {
                const targetSection = parseInt(this.dataset.step);
                if (targetSection <= currentSection || validateSection(currentSection)) {
                    showSection(targetSection);
                }
            });
        });
    }

    function showSection(sectionNumber) {
        // Ocultar todas las secciones
        document.querySelectorAll('.form-section').forEach(section => {
            section.classList.remove('active');
        });

        // Mostrar sección actual
        document.getElementById(`section-${sectionNumber}`).classList.add('active');

        // Actualizar navegación
        document.querySelectorAll('.nav-step').forEach(step => {
            step.classList.remove('active');
            if (parseInt(step.dataset.step) === sectionNumber) {
                step.classList.add('active');
            }
        });

        currentSection = sectionNumber;
    }

    function validateSection(sectionNumber) {
        const section = document.getElementById(`section-${sectionNumber}`);
        const requiredFields = section.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            field.classList.remove('is-invalid', 'is-valid');
            const feedback = field.closest('.form-group')?.querySelector('.form-feedback');
            if (feedback) {
                feedback.textContent = '';
            }

            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = 'Este campo es obligatorio.';
                }
            } else {
                field.classList.add('is-valid');
            }

            // VALIDACIÓN ESPECÍFICA PARA EL CAMPO AMOUNT
            if (field.id === 'amount' && parseFloat(field.value) < 0) {
                isValid = false;
                field.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = 'El monto no puede ser negativo.';
                }
            }
            
            // VALIDACIÓN ESPECÍFICA PARA CAMPOS SELECT
            if (field.tagName === 'SELECT' && !field.value) {
                isValid = false;
                field.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = 'Debe seleccionar una opción.';
                }
            }
        });

        if (!isValid) {
            showNotification('Por favor complete todos los campos obligatorios de esta sección.', 'warning');
        }

        return isValid;
    }

    // Contador de caracteres
    const descriptionTextarea = document.getElementById('package_description');
    const charCount = document.getElementById('charCount');
    
    if (descriptionTextarea && charCount) {
        descriptionTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            
            if (this.value.length > 500) {
                this.value = this.value.substring(0, 500);
                charCount.textContent = 500;
                charCount.style.color = 'var(--danger)';
            } else if (this.value.length > 450) {
                charCount.style.color = 'var(--warning)';
            } else {
                charCount.style.color = 'var(--gray-500)';
            }
        });
    }

    // Vista previa de imagen
    const fileInput = document.getElementById('package_image');
    const filePreview = document.getElementById('filePreview');
    
    if (fileInput && filePreview) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    showNotification('Por favor, seleccione un archivo de imagen válido.', 'error');
                    fileInput.value = '';
                    filePreview.style.display = 'none';
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    showNotification('La imagen no debe superar los 5MB.', 'error');
                    fileInput.value = '';
                    filePreview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    filePreview.innerHTML = `
                        <div class="preview-container">
                            <img src="${e.target.result}" alt="Vista previa">
                            <button type="button" class="preview-remove" onclick="removeImagePreview()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    filePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                filePreview.style.display = 'none';
            }
        });
    }

    // Validación del formulario completo al enviar
    const form = document.getElementById('createEnvioForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validar todas las secciones
            for (let i = 1; i <= totalSections; i++) {
                if (!validateSection(i)) {
                    isValid = false;
                    showSection(i); // Ir a la sección con error
                    break;
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Por favor complete todos los campos obligatorios antes de enviar.', 'warning');
            }
        });
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

// Función global para eliminar vista previa de imagen
function removeImagePreview() {
    const fileInput = document.getElementById('package_image');
    const filePreview = document.getElementById('filePreview');
    
    if (fileInput) fileInput.value = '';
    if (filePreview) {
        filePreview.style.display = 'none';
        filePreview.innerHTML = '';
    }
}
</script>