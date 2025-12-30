<!-- Modal Editar Envío -->
<div class="modal fade" id="editarEnvioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content create-envio-modal">
            <div class="modal-header">
                <div class="modal-title-container">
                    <div class="modal-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Editar Envío</h5>
                        <p class="modal-subtitle">Modifique la información del envío paso a paso</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <!-- LOADING -->
                <div id="modalEditLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando información...</span>
                    </div>
                    <p class="mt-3">Cargando información...</p>
                </div>

                <!-- ERROR -->
                <div id="modalEditError" class="text-center py-5" style="display:none;">
                    <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                    <p class="text-danger" id="editErrorMessage"></p>
                    <button type="button" class="btn btn-primary" onclick="cargarDatosEdicion()">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </div>

                <!-- CONTENT -->
                <div id="modalEditContent" style="display:none;">
                    <!-- Formulario se cargará aquí dinámicamente -->
                </div>
            </div>

            <div class="modal-footer" id="modalEditFooter" style="display:none;">
                <button class="btn btn-prev" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button id="editSubmitBtn" type="submit" form="editarEnvioForm" class="btn btn-primary" disabled>
                    <i class="fas fa-save"></i> Actualizar Envío
                </button>
            </div>
        </div>
    </div>
</div>

<template id="editFormTemplate">
    <div id="editarEnvioFormWrapper">
        <div class="section-navigation">
            <div class="nav-step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-info">
                    <span class="step-title">Información General</span>
                    <span class="step-subtitle">Datos principales</span>
                </div>
            </div>
            <div class="nav-step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-info">
                    <span class="step-title">Configuración</span>
                    <span class="step-subtitle">Servicio y logística</span>
                </div>
            </div>
            <div class="nav-step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-info">
                    <span class="step-title">Detalles Finales</span>
                    <span class="step-subtitle">Paquete e imagen</span>
                </div>
            </div>
        </div>

        <form id="editarEnvioForm" enctype="multipart/form-data">
            <input type="hidden" id="editar_shipping_id" name="shipping_id">

            <!-- Paso 1: Información General -->
            <div class="form-section active" id="edit-section-1">
                <div class="section-header">
                    <h6>
                        <i class="fas fa-info-circle"></i>
                        Información General
                    </h6>
                    <p class="section-help">Complete los datos principales del envío</p>
                </div>
                <div class="section-content">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_receiver_name" class="form-label">
                                    <i class="fas fa-user-check"></i>
                                    Destinatario *
                                </label>
                                <input type="text" class="form-control custom-input" id="edit_receiver_name" name="receiver_name" required placeholder="Nombre completo del destinatario">
                                <div class="form-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status" class="form-label">
                                    <i class="fas fa-tasks"></i>
                                    Estado *
                                </label>
                                <select class="form-control custom-select" id="edit_status" name="status" required>
                                    <option value="Solicitado">Solicitado</option>
                                    <option value="En tránsito">En tránsito</option>
                                    <option value="Entregado">Entregado</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                                <div class="form-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_amount" class="form-label">
                                    <i class="fas fa-money-bill-wave"></i>
                                    Monto del Envío *
                                </label>
                                <div class="input-with-icon">
                                    <span class="input-icon">$</span>
                                    <input type="number" step="0.01" class="form-control custom-input" id="edit_amount" name="amount" required placeholder="0.00" min="0">
                                </div>
                                <div class="form-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_receiver_description" class="form-label">
                                    <i class="fas fa-align-left"></i>
                                    Descripción del Destinatario *
                                </label>
                                <textarea class="form-control custom-textarea" id="edit_receiver_description" name="receiver_description" rows="2" required placeholder="Dirección, puntos de referencia, instrucciones especiales..."></textarea>
                                <div class="char-counter">
                                    <span id="editReceiverDescCount">0</span>/500 caracteres
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section-footer">
                    <button type="button" class="btn btn-next" data-next="2">
                        Siguiente: Configuración
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Paso 2: Configuración -->
            <div class="form-section" id="edit-section-2">
                <div class="section-header">
                    <h6>
                        <i class="fas fa-cogs"></i>
                        Configuración de Servicio
                    </h6>
                    <p class="section-help">Seleccione el cliente, sitio y horario</p>
                </div>
                <div class="section-content">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_user_id" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Cliente *
                                </label>
                                <select class="form-control custom-select" id="edit_user_id" name="user_id" required>
                                    <option value="">Seleccionar cliente</option>
                                </select>
                                <div class="form-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_site_id" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Sitio de Destino *
                                </label>
                                <select class="form-control custom-select" id="edit_site_id" name="site_id" required>
                                    <option value="">Seleccionar sitio</option>
                                </select>
                                <div class="form-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_route_unit_schedule_id" class="form-label">
                                    <i class="fas fa-clock"></i>
                                    Horario de Ruta *
                                </label>
                                <select class="form-control custom-select" id="edit_route_unit_schedule_id" name="route_unit_schedule_id" required>
                                    <option value="">Seleccionar horario</option>
                                </select>
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
                                        <input type="checkbox" class="form-check-input-custom" id="edit_fragile" name="fragile" value="1">
                                        <label class="form-check-label-custom" for="edit_fragile">
                                            <i class="fas fa-glass-cheers"></i>
                                            <span>Paquete Frágil</span>
                                            <small>Manejar con cuidado</small>
                                        </label>
                                    </div>
                                </div>
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
                        Siguiente: Detalles Finales
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Paso 3: Detalles Finales -->
            <div class="form-section" id="edit-section-3">
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
                                <label for="edit_length_cm" class="form-label">
                                    <i class="fas fa-ruler-vertical"></i>
                                    Largo (cm) *
                                </label>
                                <div class="input-with-icon">
                                    <span class="input-icon">L</span>
                                    <input type="number" step="0.01" class="form-control custom-input" id="edit_length_cm" name="length_cm" required placeholder="0.00" min="0">
                                </div>
                                <div class="form-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_width_cm" class="form-label">
                                    <i class="fas fa-ruler-horizontal"></i>
                                    Ancho (cm) *
                                </label>
                                <div class="input-with-icon">
                                    <span class="input-icon">W</span>
                                    <input type="number" step="0.01" class="form-control custom-input" id="edit_width_cm" name="width_cm" required placeholder="0.00" min="0">
                                </div>
                                <div class="form-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_height_cm" class="form-label">
                                    <i class="fas fa-arrows-alt"></i>
                                    Alto (cm) *
                                </label>
                                <div class="input-with-icon">
                                    <span class="input-icon">H</span>
                                    <input type="number" step="0.01" class="form-control custom-input" id="edit_height_cm" name="height_cm" required placeholder="0.00" min="0">
                                </div>
                                <div class="form-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_weight_kg" class="form-label">
                                    <i class="fas fa-weight-hanging"></i>
                                    Peso (kg) *
                                </label>
                                <div class="input-with-icon">
                                    <span class="input-icon"><i class="fas fa-weight"></i></span>
                                    <input type="number" step="0.01" class="form-control custom-input" id="edit_weight_kg" name="weight_kg" required placeholder="0.00" min="0">
                                </div>
                                <div class="form-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_package_description" class="form-label">
                            <i class="fas fa-align-left"></i>
                            Descripción del Paquete *
                        </label>
                        <textarea class="form-control custom-textarea" id="edit_package_description" name="package_description" rows="4" required placeholder="Describa detalladamente el contenido del paquete..."></textarea>
                        <div class="char-counter">
                            <span id="editPackageDescCount">0</span>/500 caracteres
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-camera"></i>
                            Imagen del Paquete
                        </label>

                        <!-- Imagen actual -->
                        <div id="currentImageContainer" class="mb-3" style="display:none;">
                            <div class="image-preview-container">
                                <span class="image-label fw-bold d-block mb-2">Imagen actual</span>
                                <div class="current-image-wrapper">
                                    <img id="currentImagePreview" class="img-fluid rounded border" style="max-height:160px;">
                                    <button type="button" class="preview-remove" onclick="removeCurrentImage()" title="Eliminar imagen actual">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Cambiar imagen -->
                        <div class="file-upload-container">
                            <input type="file" class="file-input" id="edit_package_image" name="package_image" accept="image/*">
                            <label for="edit_package_image" class="file-upload-label">
                                <div class="file-upload-content">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span class="file-upload-text">Haga clic para cambiar la imagen del paquete</span>
                                    <span class="file-upload-hint">PNG, JPG, JPEG - Máximo 5MB</span>
                                </div>
                            </label>
                            <div class="file-preview mt-2" id="editFilePreview"></div>
                        </div>
                    </div>
                </div>
                <div class="section-footer">
                    <button type="button" class="btn btn-prev" data-prev="2">
                        <i class="fas fa-arrow-left"></i>
                        Anterior
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

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
    background: linear-gradient(135deg, #f59e0b 0%, #f59e0b 100%);
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

/* Subida de archivos y vista de imagen */
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

/* Contenedor de imagen actual */
.current-image-wrapper {
    position: relative;
    display: inline-block;
    max-width: 300px;
}

.current-image-wrapper img {
    max-width: 100%;
    max-height: 160px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
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
    z-index: 10;
}

.preview-remove:hover {
    background: #dc2626;
    transform: scale(1.1);
}

.image-label {
    color: #374151;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.image-preview-container {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
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
    cursor: pointer;
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
    background:#f59e0b;
;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-primary:hover {
    background: #4338ca;
    transform: translateY(-1px);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.btn-primary:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
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

/* Footer del modal */
.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
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

    .modal-footer {
        flex-direction: column;
        gap: 0.5rem;
    }

    .modal-footer button {
        width: 100%;
    }
}

.row.g-3 {
    --bs-gutter-x: 1rem;
    --bs-gutter-y: 1rem;
}
</style>

<!-- JavaScript para manejar el modal de edición -->
<script>
    // Variables globales para el modal de edición
    let editModalData = null;
    let currentShippingId = null;
    let editModalInstance = null;
    let editCurrentSection = 1;

    // Función para abrir el modal de edición
    function abrirModalEdicion(shippingId) {
        // Validar que shippingId esté definido
        if (!shippingId && shippingId !== 0) {
            showNotification('Error: No se proporcionó un ID válido para el envío', 'error');
            return;
        }

        // Asegurar que shippingId es un número
        shippingId = parseInt(shippingId);
        if (isNaN(shippingId)) {
            showNotification('Error: ID del envío no válido', 'error');
            return;
        }

        currentShippingId = shippingId;

        // Mostrar el modal
        const modalElement = document.getElementById('editarEnvioModal');
        if (!modalElement) {
            showNotification('Error: No se pudo encontrar el modal de edición', 'error');
            return;
        }

        try {
            // Crear nueva instancia si no existe
            if (!editModalInstance) {
                editModalInstance = new bootstrap.Modal(modalElement);
            }
            editModalInstance.show();

        } catch (error) {
            console.error('Error al abrir el modal:', error);
            showNotification('Error al abrir el modal: ' + error.message, 'error');
        }
    }

    // Función para cargar datos para edición
    async function cargarDatosEdicion() {
        if (!currentShippingId && currentShippingId !== 0) {
            mostrarErrorEdicion('No se pudo identificar el envío a editar');
            return;
        }

        try {
            // Mostrar loading, ocultar error y footer
            document.getElementById('modalEditLoading').style.display = 'block';
            document.getElementById('modalEditError').style.display = 'none';
            document.getElementById('modalEditContent').style.display = 'none';
            document.getElementById('modalEditFooter').style.display = 'none';

            // Hacer la petición a la API
            const response = await fetch(`/shippings/${currentShippingId}/edit`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();

            if (result.success && result.data) {
                editModalData = result.data;
                mostrarFormularioEdicion();
            } else {
                mostrarErrorEdicion(result.message || 'Error al cargar datos del envío');
            }

        } catch (error) {
            if (error.name === 'AbortError') {
                mostrarErrorEdicion('Tiempo de espera agotado. La solicitud tardó demasiado.');
            } else if (error.message.includes('Failed to fetch')) {
                mostrarErrorEdicion('Error de red. Verifica tu conexión a internet.');
            } else if (error.message.includes('404')) {
                mostrarErrorEdicion('Envío no encontrado. Puede haber sido eliminado.');
            } else {
                mostrarErrorEdicion('Error: ' + error.message);
            }
        }
    }

    // Función para mostrar el formulario de edición con los datos
    function mostrarFormularioEdicion() {
        const loading = document.getElementById('modalEditLoading');
        const content = document.getElementById('modalEditContent');
        const footer = document.getElementById('modalEditFooter');
        const submitBtn = document.getElementById('editSubmitBtn');

        if (!loading || !content || !submitBtn) {
            mostrarErrorEdicion('Error interno: Elementos del formulario no encontrados');
            return;
        }

        const shipping = editModalData?.shipping;
        if (!shipping) {
            mostrarErrorEdicion('Datos del envío no disponibles');
            return;
        }

        // Ocultar loading, mostrar contenido y footer
        loading.style.display = 'none';
        content.style.display = 'block';
        footer.style.display = 'flex';

        // Clonar template del formulario
        const template = document.getElementById('editFormTemplate');
        if (!template) {
            mostrarErrorEdicion('Error interno: Template del formulario no encontrado');
            return;
        }

        content.innerHTML = '';
        const clonedContent = template.content.cloneNode(true);
        content.appendChild(clonedContent);

        // Actualizar la acción del formulario
        const form = document.getElementById('editarEnvioForm');
        if (form) {
            form.action = `/shippings/${currentShippingId}`;
        }

        // Llenar selects con datos
        if (editModalData.users && editModalData.users.length > 0) {
            llenarSelectEdicion('edit_user_id', editModalData.users, 'id', 'name', shipping.user_id);
        } else {
            document.getElementById('edit_user_id').innerHTML = '<option value="">No hay usuarios disponibles</option>';
        }

        if (editModalData.sites && editModalData.sites.length > 0) {
            llenarSelectEdicion('edit_site_id', editModalData.sites, 'id', 'name', shipping.site_id);
        } else {
            document.getElementById('edit_site_id').innerHTML = '<option value="">No hay sitios disponibles</option>';
        }

        if (editModalData.schedules && editModalData.schedules.length > 0) {
            llenarSelectEdicion('edit_route_unit_schedule_id', editModalData.schedules, 'id', function(schedule) {
                const nombre = schedule.name || 'Horario';
                const hora = schedule.departure_time || schedule.schedule_time || '';
                const unidad = schedule.route_unit?.unit_number ? ` - Unidad ${schedule.route_unit.unit_number}` : '';
                return `${nombre} ${hora}${unidad}`;
            }, shipping.route_unit_schedule_id);
        } else {
            document.getElementById('edit_route_unit_schedule_id').innerHTML = '<option value="">No hay horarios disponibles</option>';
        }

        // Llenar campos del formulario
        document.getElementById('editar_shipping_id').value = shipping.id;
        document.getElementById('edit_receiver_name').value = shipping.receiver_name || '';
        document.getElementById('edit_receiver_description').value = shipping.receiver_description || '';
        document.getElementById('edit_length_cm').value = shipping.length_cm || '';
        document.getElementById('edit_width_cm').value = shipping.width_cm || '';
        document.getElementById('edit_height_cm').value = shipping.height_cm || '';
        document.getElementById('edit_weight_kg').value = shipping.weight_kg || '';
        document.getElementById('edit_amount').value = shipping.amount || '';
        document.getElementById('edit_package_description').value = shipping.package_description || '';
        document.getElementById('edit_status').value = shipping.status || 'Solicitado';

        // Checkbox frágil
        if (shipping.fragile) {
            document.getElementById('edit_fragile').checked = true;
        }

        // Mostrar imagen actual si existe
        if (shipping.package_image) {
            const imageContainer = document.getElementById('currentImageContainer');
            const imagePreview = document.getElementById('currentImagePreview');

            if (imageContainer && imagePreview) {
                imagePreview.src = `/storage/${shipping.package_image}`;
                imagePreview.onerror = function() {
                    this.src = 'https://via.placeholder.com/300x150?text=Imagen+no+disponible';
                };
                imageContainer.style.display = 'block';
            }
        }

        // Configurar vista previa para nueva imagen
        const imageInput = document.getElementById('edit_package_image');
        const editFilePreview = document.getElementById('editFilePreview');

        if (imageInput && editFilePreview) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validar tamaño (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        showNotification('La imagen no debe superar los 5MB.', 'error');
                        this.value = '';
                        editFilePreview.style.display = 'none';
                        return;
                    }

                    // Validar tipo
                    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        showNotification('Formato no válido. Use JPG, PNG o GIF.', 'error');
                        this.value = '';
                        editFilePreview.style.display = 'none';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        editFilePreview.innerHTML = `
                            <div class="preview-container">
                                <span class="image-label">Nueva imagen seleccionada</span>
                                <img src="${e.target.result}" alt="Vista previa">
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

        // Habilitar botón de enviar
        submitBtn.disabled = false;

        // Inicializar navegación y validación
        initEditNavigation();
        initEditCharacterCounters();
        configurarValidacionEdicion();

        // Inicializar contadores de caracteres con valores actuales
        updateCharCounter('editReceiverDescCount', shipping.receiver_description?.length || 0, 500);
        updateCharCounter('editPackageDescCount', shipping.package_description?.length || 0, 500);
    }

    // Función para llenar un select en el formulario de edición
    function llenarSelectEdicion(selectId, data, valueField, textFieldOrFunc, selectedValue) {
        const select = document.getElementById(selectId);
        if (!select) return;

        // Limpiar select primero (excepto la primera opción)
        const firstOption = select.querySelector('option[value=""]');
        select.innerHTML = '';
        if (firstOption) {
            select.appendChild(firstOption);
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

            if (item[valueField] == selectedValue) {
                option.selected = true;
            }

            select.appendChild(option);
        });
    }

    // Función para mostrar error en el modal de edición
    function mostrarErrorEdicion(mensaje) {
        document.getElementById('modalEditLoading').style.display = 'none';
        document.getElementById('modalEditContent').style.display = 'none';
        document.getElementById('modalEditFooter').style.display = 'none';
        document.getElementById('modalEditError').style.display = 'block';
        document.getElementById('editErrorMessage').textContent = mensaje;
    }

    // Navegación entre pasos (edición)
    function initEditNavigation() {
        showEditSection(1);

        // Botones siguiente
        document.querySelectorAll('.btn-next').forEach(btn => {
            btn.addEventListener('click', function() {
                const nextSection = parseInt(this.dataset.next);
                if (validateEditSection(editCurrentSection)) {
                    showEditSection(nextSection);
                }
            });
        });

        // Botones anterior
        document.querySelectorAll('.btn-prev').forEach(btn => {
            btn.addEventListener('click', function() {
                const prevSection = parseInt(this.dataset.prev);
                showEditSection(prevSection);
            });
        });

        // Pasos de navegación
        document.querySelectorAll('.nav-step').forEach(step => {
            step.addEventListener('click', function() {
                const targetSection = parseInt(this.dataset.step);
                if (targetSection <= editCurrentSection || validateEditSection(editCurrentSection)) {
                    showEditSection(targetSection);
                }
            });
        });
    }

    function showEditSection(sectionNumber) {
        // Ocultar todas las secciones
        document.querySelectorAll('.form-section').forEach(section => {
            section.classList.remove('active');
        });

        // Mostrar sección actual
        const section = document.getElementById(`edit-section-${sectionNumber}`);
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

        editCurrentSection = sectionNumber;
    }

    function validateEditSection(sectionNumber) {
        const section = document.getElementById(`edit-section-${sectionNumber}`);
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

    // Contadores de caracteres (edición)
    function initEditCharacterCounters() {
        const descTextarea = document.getElementById('edit_receiver_description');
        const descCharCount = document.getElementById('editReceiverDescCount');

        if (descTextarea && descCharCount) {
            descTextarea.addEventListener('input', function() {
                const length = this.value.length;
                descCharCount.textContent = length;
                updateCharCounterStyle(descCharCount, length, 500);
            });
        }

        const packageTextarea = document.getElementById('edit_package_description');
        const packageCharCount = document.getElementById('editPackageDescCount');

        if (packageTextarea && packageCharCount) {
            packageTextarea.addEventListener('input', function() {
                const length = this.value.length;
                packageCharCount.textContent = length;
                updateCharCounterStyle(packageCharCount, length, 500);
            });
        }
    }

    function updateCharCounter(counterId, length, max) {
        const counter = document.getElementById(counterId);
        if (counter) {
            counter.textContent = length;
            updateCharCounterStyle(counter, length, max);
        }
    }

    function updateCharCounterStyle(counter, length, max) {
        if (length > max) {
            counter.style.color = '#ef4444';
        } else if (length > max * 0.9) {
            counter.style.color = '#4f46e5';
        } else {
            counter.style.color = '#6b7280';
        }
    }

    // Función para configurar validación del formulario de edición
    function configurarValidacionEdicion() {
        const form = document.getElementById('editarEnvioForm');
        if (!form) return;

        // Validación en tiempo real
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        });

        // Validación al enviar
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validar todas las secciones primero
            let isValid = true;
            for (let i = 1; i <= 3; i++) {
                if (!validateEditSection(i)) {
                    isValid = false;
                    showEditSection(i);
                    break;
                }
            }

            if (!isValid) {
                e.stopPropagation();
                showNotification('Por favor complete todos los campos obligatorios.', 'warning');
                return;
            }

            // Mostrar loading en el botón de enviar
            const submitBtn = document.getElementById('editSubmitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
            submitBtn.disabled = true;

            try {
                const formData = new FormData(form);

                // Obtener CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    throw new Error('No se encontró el token CSRF');
                }

                // Enviar como POST con override PUT
                const response = await fetch(form.action, {
                    method: 'PUT',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': 'PUT'
                    }
                });

                // Verificar si la respuesta es JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();

                    if (response.status === 405) {
                        throw new Error(`Error 405: Método no permitido.`);
                    } else if (response.status === 404) {
                        throw new Error(`Error 404: No encontrado.`);
                    } else {
                        throw new Error(`El servidor respondió con ${contentType || 'text/html'} (Status: ${response.status}) en lugar de JSON`);
                    }
                }

                const result = await response.json();

                if (result.success) {
                    // Cerrar modal
                    if (editModalInstance) {
                        editModalInstance.hide();
                    }

                    showNotification('Envío actualizado correctamente', 'success');

                    // Recargar la página o actualizar la tabla
                    setTimeout(() => {
                        if (typeof recargarTabla === 'function') {
                            recargarTabla();
                        } else {
                            location.reload();
                        }
                    }, 1500);
                } else {
                    showNotification(result.message || 'Error al actualizar el envío', 'error');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }

            } catch (error) {
                showNotification('Error de conexión al actualizar el envío: ' + error.message, 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Funciones para manejar imágenes
    function removeCurrentImage() {
        const imageContainer = document.getElementById('currentImageContainer');
        const imageInput = document.getElementById('edit_package_image');

        if (imageContainer) {
            imageContainer.style.display = 'none';
        }

        // Agregar un campo oculto para indicar que se debe eliminar la imagen actual
        const form = document.getElementById('editarEnvioForm');
        if (form) {
            let deleteInput = form.querySelector('input[name="delete_current_image"]');
            if (!deleteInput) {
                deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_current_image';
                deleteInput.value = '1';
                form.appendChild(deleteInput);
            }
        }

        showNotification('La imagen actual será eliminada al guardar los cambios', 'info');
    }

    function removeEditImagePreview() {
        const fileInput = document.getElementById('edit_package_image');
        const filePreview = document.getElementById('editFilePreview');

        if (fileInput) fileInput.value = '';
        if (filePreview) {
            filePreview.style.display = 'none';
            filePreview.innerHTML = '';
        }
    }

    // Función para mostrar notificaciones
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

    // Inicialización cuando el DOM esté cargado
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('editarEnvioModal');

        if (modalElement) {
            // Crear una sola instancia del modal
            editModalInstance = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });

            // Evento cuando se abre el modal
            modalElement.addEventListener('show.bs.modal', function(event) {
                // Obtener el botón que activó el modal
                const button = event.relatedTarget;

                // Si se abrió con data-bs-toggle, capturar el ID del botón
                if (button && button.hasAttribute('data-shipping-id')) {
                    const shippingId = button.getAttribute('data-shipping-id');
                    if (shippingId) {
                        currentShippingId = parseInt(shippingId);
                    }
                }

                // Si currentShippingId ya está definido
                if (currentShippingId) {
                    cargarDatosEdicion();
                } else {
                    mostrarErrorEdicion('No se pudo determinar qué envío editar');
                }
            });

            // Resetear formulario cuando se cierra
            modalElement.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editarEnvioForm');
                if (form) {
                    form.reset();
                    form.classList.remove('was-validated');

                    // Limpiar clases de validación
                    const inputs = form.querySelectorAll('.is-invalid, .is-valid');
                    inputs.forEach(input => {
                        input.classList.remove('is-invalid', 'is-valid');
                    });
                }

                // Resetear variables
                currentShippingId = null;
                editModalData = null;
                editCurrentSection = 1;

                // Ocultar contenido, mostrar loading para próxima vez
                document.getElementById('modalEditContent').style.display = 'none';
                document.getElementById('modalEditFooter').style.display = 'none';
                document.getElementById('modalEditError').style.display = 'none';
                document.getElementById('modalEditLoading').style.display = 'block';

                // Deshabilitar botón de enviar
                const submitBtn = document.getElementById('editSubmitBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Actualizar Envío';
                }
            });
        }

        // Exponer función globalmente para ser llamada desde botones
        window.abrirModalEdicion = abrirModalEdicion;
        window.cargarDatosEdicion = cargarDatosEdicion;
        window.removeCurrentImage = removeCurrentImage;
        window.removeEditImagePreview = removeEditImagePreview;
    });

    // Función para manejar botones con data attributes
    document.addEventListener('click', function(e) {
        // Si se hace clic en un botón con data-shipping-id pero sin data-bs-toggle
        if (e.target.closest('[data-shipping-id]')) {
            const button = e.target.closest('[data-shipping-id]');
            const shippingId = button.getAttribute('data-shipping-id');

            // Solo manejar si no tiene data-bs-toggle
            if (!button.hasAttribute('data-bs-toggle')) {
                e.preventDefault();
                abrirModalEdicion(shippingId);
            }
        }
    });
</script>
