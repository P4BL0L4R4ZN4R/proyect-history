<!-- resources/views/shippings/modal-create.blade.php -->
<div class="modal fade" id="crearEnvioModal" tabindex="-1">
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
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="crearEnvioForm" action="{{ route('shippings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <!-- Loading state -->
                    <div id="modalLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando formulario...</span>
                        </div>
                        <p class="mt-3">Cargando formulario...</p>
                    </div>

                    <!-- Error state -->
                    <div id="modalError" class="text-center py-5" style="display: none;">
                        <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                        <p class="text-danger" id="errorMessage"></p>
                        <button type="button" class="btn btn-primary" onclick="cargarDatosModal()">
                            <i class="fas fa-redo"></i> Reintentar
                        </button>
                    </div>

                    <!-- Form content -->
                    <div id="modalContent" style="display: none;">
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

                        <!-- Paso 1: Información Básica -->
                        <div class="form-section active" id="section-1">
                            <div class="section-header">
                                <h6>
                                    <i class="fas fa-info-circle"></i>
                                    Información Básica del Envío
                                </h6>
                                <p class="section-help">Complete los datos principales del cliente y destino</p>
                            </div>
                            <div class="section-content">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_id" class="form-label">
                                                <i class="fas fa-user"></i>
                                                Cliente *
                                            </label>
                                            <select class="form-control custom-select" id="user_id" name="user_id" required>
                                                <option value="">Seleccionar cliente</option>
                                                <!-- Se llena con JavaScript -->
                                            </select>
                                            <div class="form-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="site_id" class="form-label">
                                                <i class="fas fa-map-marker-alt"></i>
                                                Sitio de Destino *
                                            </label>
                                            <select class="form-control custom-select" id="site_id" name="site_id" required>
                                                <option value="">Seleccionar sitio</option>
                                                <!-- Se llena con JavaScript -->
                                            </select>
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
                                            <label for="receiver_name" class="form-label">
                                                <i class="fas fa-user-check"></i>
                                                Destinatario *
                                            </label>
                                            <input type="text" class="form-control custom-input" id="receiver_name" name="receiver_name" required placeholder="Nombre completo del destinatario">
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

                        <!-- Paso 2: Logística -->
                        <div class="form-section" id="section-2">
                            <div class="section-header">
                                <h6>
                                    <i class="fas fa-truck"></i>
                                    Configuración de Logística
                                </h6>
                                <p class="section-help">Seleccione el horario y ruta para el transporte</p>
                            </div>
                            <div class="section-content">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="route_unit_schedule_id" class="form-label">
                                                <i class="fas fa-clock"></i>
                                                Horario de Ruta *
                                            </label>
                                            <select class="form-control custom-select" id="route_unit_schedule_id" name="route_unit_schedule_id" required>
                                                <option value="">Seleccionar horario</option>
                                                <!-- Se llena con JavaScript -->
                                            </select>
                                            <div class="form-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="receiver_description" class="form-label">
                                        <i class="fas fa-align-left"></i>
                                        Descripción del Destinatario *
                                    </label>
                                    <textarea class="form-control custom-textarea" id="receiver_description" name="receiver_description" rows="3" required placeholder="Dirección, puntos de referencia, instrucciones especiales..."></textarea>
                                    <div class="char-counter">
                                        <span id="receiverDescCount">0</span>/500 caracteres
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

                        <!-- Paso 3: Detalles del Paquete -->
                        <div class="form-section" id="section-3">
                            <div class="section-header">
                                <h6>
                                    <i class="fas fa-box-open"></i>
                                    Detalles del Paquete
                                </h6>
                                <p class="section-help">Especifique las dimensiones, peso y contenido del paquete</p>
                            </div>
                            <div class="section-content">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="length_cm" class="form-label">
                                                <i class="fas fa-ruler-vertical"></i>
                                                Largo (cm) *
                                            </label>
                                            <div class="input-with-icon">
                                                <span class="input-icon">L</span>
                                                <input type="number" step="0.01" class="form-control custom-input" id="length_cm" name="length_cm" required placeholder="0.00" min="0">
                                            </div>
                                            <div class="form-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="width_cm" class="form-label">
                                                <i class="fas fa-ruler-horizontal"></i>
                                                Ancho (cm) *
                                            </label>
                                            <div class="input-with-icon">
                                                <span class="input-icon">W</span>
                                                <input type="number" step="0.01" class="form-control custom-input" id="width_cm" name="width_cm" required placeholder="0.00" min="0">
                                            </div>
                                            <div class="form-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="height_cm" class="form-label">
                                                <i class="fas fa-arrows-alt"></i>
                                                Alto (cm) *
                                            </label>
                                            <div class="input-with-icon">
                                                <span class="input-icon">H</span>
                                                <input type="number" step="0.01" class="form-control custom-input" id="height_cm" name="height_cm" required placeholder="0.00" min="0">
                                            </div>
                                            <div class="form-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="weight_kg" class="form-label">
                                                <i class="fas fa-weight-hanging"></i>
                                                Peso (kg) *
                                            </label>
                                            <div class="input-with-icon">
                                                <span class="input-icon"><i class="fas fa-weight"></i></span>
                                                <input type="number" step="0.01" class="form-control custom-input" id="weight_kg" name="weight_kg" required placeholder="0.00" min="0">
                                            </div>
                                            <div class="form-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Tipo de Paquete
                                            </label>
                                            <div class="checkbox-group">
                                                <div class="form-check-custom">
                                                    <input type="checkbox" class="form-check-input-custom" id="fragile" name="fragile" value="1">
                                                    <label class="form-check-label-custom" for="fragile">
                                                        <i class="fas fa-glass-cheers"></i>
                                                        <span>Paquete Frágil</span>
                                                        <small>Manejar con cuidado</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="package_description" class="form-label">
                                        <i class="fas fa-align-left"></i>
                                        Descripción del Paquete *
                                    </label>
                                    <textarea class="form-control custom-textarea" id="package_description" name="package_description" rows="4" required placeholder="Describa detalladamente el contenido del paquete, dimensiones, peso, y cualquier información relevante..."></textarea>
                                    <div class="char-counter">
                                        <span id="packageDescCount">0</span>/500 caracteres
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
                    </div>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="status" value="Solicitado">
            </form>
        </div>
    </div>
</div>

<style>

    /* ---------------------------------------------------- */
    /* ESTILOS DE MODAL Y NAVEGACIÓN */
    /* ---------------------------------------------------- */
    .btn-close-custom {
        background: none !important;
        border: none !important;
        padding: 0.5rem !important;
        margin: -0.5rem -0.5rem -0.5rem auto !important;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.15s ease-in-out;
        color: white;
        font-size: 1.25rem;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .btn-close-custom:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.1) !important;
    }

    .create-envio-modal {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .create-envio-modal .modal-header {
        background: linear-gradient(135deg,#f59e0b 0%, #f59e0b 100%);
        color: white;
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: none;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
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
        background: rgba(79, 70, 229, 0.1);
        border: 1px solid rgba(79, 70, 229, 0.2);
    }

    .step-number {
        width: 32px;
        height: 32px;
        background: #e5e7eb;
        color: #4b5563;
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
        background: #f59e0b;
        color: white;
    }

    .step-info {
        display: flex;
        flex-direction: column;
    }

    .step-title {
        font-weight: 600;
        font-size: 0.875rem;
        color: #1f2937;
    }

    .step-subtitle {
        font-size: 0.75rem;
        color: #6b7280;
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
        border-bottom: 1px solid #e5e7eb;
    }

    .section-header h6 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        color: #1f2937;
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
    }

    .section-header h6 i {
        color: #f59e0b;
    }

    .section-help {
        color: #6b7280;
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
        border-top: 1px solid #e5e7eb;
    }

    /* ---------------------------------------------------- */
    /* ESTILOS DE FORMULARIO */
    /* ---------------------------------------------------- */
    .form-group {
        margin-bottom: 2.5rem !important;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-label i {
        color: #f59e0b;
        font-size: 0.875rem;
        width: 16px;
    }

    .custom-input, .custom-select, .custom-textarea {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background: white;
        width: 100%;
    }

    .custom-input:focus, .custom-select:focus, .custom-textarea:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
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
        color: #6b7280;
        font-weight: 600;
        z-index: 2;
    }

    .input-with-icon .custom-input {
        padding-left: 2.5rem;
    }

    .custom-select {
        appearance: none !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 16px 12px;
        height: auto !important;
        min-height: 40px !important;
        padding: 0.75rem 2.5rem 0.75rem 1rem !important;
        line-height: 1.4 !important;
        background-position: right 1rem center !important;
    }

    .custom-textarea {
        resize: vertical;
        min-height: 120px;
    }

    .char-counter {
        text-align: right;
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    /* Checkbox personalizado */
    .checkbox-group {
        margin-top: 0.5rem;
    }

    .form-check-custom {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .form-check-custom:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .form-check-input-custom {
        margin-right: 0.75rem;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .form-check-label-custom {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        flex: 1;
    }

    .form-check-label-custom i {
        color: #f59e0b;
        font-size: 1rem;
    }

    .form-check-label-custom span {
        font-weight: 600;
        color: #374151;
    }

    .form-check-label-custom small {
        margin-left: auto;
        color: #6b7280;
        font-size: 0.75rem;
    }

    /* Subida de archivos */
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
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f9fafb;
        width: 100%;
    }

    .file-upload-label:hover {
        border-color: #f59e0b;
        background: rgba(79, 70, 229, 0.05);
    }

    .file-upload-content i {
        font-size: 2rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
        display: block;
    }

    .file-upload-text {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.25rem;
    }

    .file-upload-hint {
        font-size: 0.75rem;
        color: #6b7280;
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
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    .preview-remove {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
        background: #ef4444;
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

    /* Botones */
    .btn-prev, .btn-next {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: 1px solid #d1d5db;
        background: white;
        color: #374151;
    }

    .btn-next {
        background: #f59e0b;
        color: white;
        border: none;
    }

    .btn-next:hover {
        background: #f59e0b;
        transform: translateY(-1px);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    .btn-prev:hover {
        background: #f3f4f6;
    }

    .btn-primary {
        background: #f3f4f6;
        color: black;
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
        background: #f59e0b;
        transform: translateY(-1px);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    /* Form feedback */
    .form-feedback {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        min-height: 1rem;
        color: #ef4444;
    }

    .is-invalid {
        border-color: #ef4444 !important;
    }

    .is-valid {
        border-color: #10b981 !important;
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

        .btn-close-custom {
            position: absolute;
            top: 1rem;
            right: 1rem;
            margin: 0 !important;
        }
    }

    .row.g-3 {
        --bs-gutter-x: 1rem;
        --bs-gutter-y: 1rem;
    }
</style>

<script>
    // Variables globales
    let modalData = null;
    let isModalLoaded = false;
    let currentSection = 1;

    // Función para cargar datos del modal
    async function cargarDatosModal() {
        try {
            document.getElementById('modalLoading').style.display = 'block';
            document.getElementById('modalError').style.display = 'none';
            document.getElementById('modalContent').style.display = 'none';

            const response = await fetch('{{ route("shippings.create") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                modalData = result.data;
                isModalLoaded = true;
                mostrarFormulario();
            } else {
                mostrarError(result.message || 'Error al cargar los datos del formulario');
            }

        } catch (error) {
            console.error('Error cargando datos:', error);
            mostrarError('Error de conexión. Verifique su conexión a internet y vuelva a intentar.');
        }
    }

    // Función para mostrar el formulario
    function mostrarFormulario() {
        document.getElementById('modalLoading').style.display = 'none';
        document.getElementById('modalError').style.display = 'none';
        document.getElementById('modalContent').style.display = 'block';

        // Llenar selects con datos
        if (modalData) {
            llenarSelect('user_id', modalData.users, 'id', 'name');
            llenarSelect('site_id', modalData.sites, 'id', 'name');
            llenarSelect('route_unit_schedule_id', modalData.schedules, 'id', function(schedule) {
                let texto = schedule.name || 'Horario';
                if (schedule.departure_time) {
                    texto += ` - ${schedule.departure_time}`;
                }
                if (schedule.route_unit?.unit_number) {
                    texto += ` (Unidad ${schedule.route_unit.unit_number})`;
                }
                return texto;
            });
        }

        // Inicializar navegación
        initNavigation();
    }

    // Función para llenar selects
    function llenarSelect(selectId, data, valueField, textFieldOrFunc) {
        const select = document.getElementById(selectId);
        if (!select) return;

        // Limpiar opciones excepto la primera
        while (select.options.length > 1) {
            select.remove(1);
        }

        // Agregar opciones
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueField];

            if (typeof textFieldOrFunc === 'function') {
                option.textContent = textFieldOrFunc(item);
            } else {
                option.textContent = item[textFieldOrFunc];
            }

            select.appendChild(option);
        });
    }

    // Función para mostrar error
    function mostrarError(mensaje) {
        document.getElementById('modalLoading').style.display = 'none';
        document.getElementById('modalContent').style.display = 'none';
        document.getElementById('modalError').style.display = 'block';
        document.getElementById('errorMessage').textContent = mensaje;
    }

    // Navegación entre pasos
    function initNavigation() {
        showSection(1);

        // Botones siguiente
        document.querySelectorAll('.btn-next').forEach(btn => {
            btn.addEventListener('click', function() {
                const nextSection = parseInt(this.dataset.next);
                if (validateSection(currentSection)) {
                    showSection(nextSection);
                }
            });
        });

        // Botones anterior
        document.querySelectorAll('.btn-prev').forEach(btn => {
            btn.addEventListener('click', function() {
                const prevSection = parseInt(this.dataset.prev);
                showSection(prevSection);
            });
        });

        // Pasos de navegación
        document.querySelectorAll('.nav-step').forEach(step => {
            step.addEventListener('click', function() {
                const targetSection = parseInt(this.dataset.step);
                if (targetSection <= currentSection || validateSection(currentSection)) {
                    showSection(targetSection);
                }
            });
        });

        // Inicializar contadores de caracteres
        initCharacterCounters();

        // Inicializar subida de archivos
        initFileUpload();

        // Configurar validación del formulario
        configurarValidacion();
    }

    function showSection(sectionNumber) {
        // Ocultar todas las secciones
        document.querySelectorAll('.form-section').forEach(section => {
            section.classList.remove('active');
        });

        // Mostrar sección actual
        const section = document.getElementById(`section-${sectionNumber}`);
        if (section) {
            section.classList.add('active');
        }

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
        if (!section) return true;

        const requiredFields = section.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            field.classList.remove('is-invalid', 'is-valid');
            const formGroup = field.closest('.form-group');
            const feedback = formGroup ? formGroup.querySelector('.form-feedback') : null;

            if (feedback) {
                feedback.textContent = '';
            }

            // Validar campos vacíos
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = 'Este campo es obligatorio.';
                }
            }
            // Validar números negativos
            else if (field.type === 'number' && parseFloat(field.value) < 0) {
                isValid = false;
                field.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = 'El valor no puede ser negativo.';
                }
            }
            // Validar selects
            else if (field.tagName === 'SELECT' && !field.value) {
                isValid = false;
                field.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = 'Debe seleccionar una opción.';
                }
            }
            else {
                field.classList.add('is-valid');
            }
        });

        if (!isValid) {
            showNotification('Por favor complete todos los campos obligatorios de esta sección.', 'warning');
        }

        return isValid;
    }

    // Contadores de caracteres
    function initCharacterCounters() {
        const descTextarea = document.getElementById('receiver_description');
        const descCharCount = document.getElementById('receiverDescCount');

        if (descTextarea && descCharCount) {
            descTextarea.addEventListener('input', function() {
                const length = this.value.length;
                descCharCount.textContent = length;
                updateCharCounterStyle(descCharCount, length, 500);
            });
            // Inicializar contador
            descCharCount.textContent = descTextarea.value.length;
            updateCharCounterStyle(descCharCount, descTextarea.value.length, 500);
        }

        const packageTextarea = document.getElementById('package_description');
        const packageCharCount = document.getElementById('packageDescCount');

        if (packageTextarea && packageCharCount) {
            packageTextarea.addEventListener('input', function() {
                const length = this.value.length;
                packageCharCount.textContent = length;
                updateCharCounterStyle(packageCharCount, length, 500);
            });
            // Inicializar contador
            packageCharCount.textContent = packageTextarea.value.length;
            updateCharCounterStyle(packageCharCount, packageTextarea.value.length, 500);
        }
    }

    function updateCharCounterStyle(counter, length, max) {
        if (length > max) {
            counter.style.color = '#ef4444';
        } else if (length > max * 0.9) {
            counter.style.color = '#f59e0b';
        } else {
            counter.style.color = '#6b7280';
        }
    }

    // Subida de archivos
    function initFileUpload() {
        const fileInput = document.getElementById('package_image');
        const filePreview = document.getElementById('filePreview');

        if (fileInput && filePreview) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validar tamaño (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        showNotification('La imagen no debe superar los 5MB.', 'error');
                        this.value = '';
                        filePreview.style.display = 'none';
                        return;
                    }

                    // Validar tipo
                    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        showNotification('Formato no válido. Use JPG, PNG o GIF.', 'error');
                        this.value = '';
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
    }

    // Configurar validación del formulario
    function configurarValidacion() {
        const form = document.getElementById('crearEnvioForm');
        if (!form) return;

        // Validación en tiempo real
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });

        // Validación al enviar
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Validar todas las secciones
            for (let i = 1; i <= 3; i++) {
                if (!validateSection(i)) {
                    isValid = false;
                    showSection(i);
                    break;
                }
            }

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
                showNotification('Por favor complete todos los campos obligatorios.', 'warning');
            }
        });
    }

    function validateField(field) {
        const formGroup = field.closest('.form-group');
        const feedback = formGroup ? formGroup.querySelector('.form-feedback') : null;

        field.classList.remove('is-invalid', 'is-valid');

        if (field.hasAttribute('required') && !field.value.trim()) {
            field.classList.add('is-invalid');
            if (feedback) feedback.textContent = 'Este campo es obligatorio.';
            return false;
        }

        if (field.type === 'number' && parseFloat(field.value) < 0) {
            field.classList.add('is-invalid');
            if (feedback) feedback.textContent = 'El valor no puede ser negativo.';
            return false;
        }

        field.classList.add('is-valid');
        if (feedback) feedback.textContent = '';
        return true;
    }

    // Función para eliminar vista previa
    function removeImagePreview() {
        const fileInput = document.getElementById('package_image');
        const filePreview = document.getElementById('filePreview');

        if (fileInput) fileInput.value = '';
        if (filePreview) {
            filePreview.style.display = 'none';
            filePreview.innerHTML = '';
        }
    }

    // Mostrar notificaciones
    function showNotification(message, type) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        } else {
            alert(message);
        }
    }

    // Eventos del modal
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('crearEnvioModal');

        if (modal) {
            modal.addEventListener('show.bs.modal', function() {
                if (!isModalLoaded) {
                    cargarDatosModal();
                } else {
                    mostrarFormulario();
                }
            });

            modal.addEventListener('hidden.bs.modal', function() {
                // Resetear a primera sección
                currentSection = 1;
                showSection(1);

                // Limpiar vista previa
                removeImagePreview();
            });
        }
    });
</script>
