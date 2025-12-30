@hasanyrole(['superadmin' , 'admin'])

@extends('adminlte::page')



@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Panel de Configuración General -->
            <div class="col-md-12">
                <div class="card shadow rounded mb-4" style="background-color: #ffffff;">
                    <div class="card-header bg-primary text-white d-flex align-items-center">
                        <h3 class="card-title mb-0"><i class="fas fa-cogs mr-2"></i>Configuración General</h3>
                    </div>

                    <form method="POST" action="{{ route('configuracion.update') }}" enctype="multipart/form-data" id="configForm">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="row">
                                <!-- Columna de Imagen -->
                                <div class="col-md-5 text-center d-flex flex-column align-items-center justify-content-center">
                                    <div class="image-preview mb-3">
                                        <img src="{{ asset($currentLogo) }}?v={{ time() }}"
                                            id="logoPreview"
                                            class="img-fluid shadow rounded-circle"
                                            style="max-height: 180px; border: 4px solid #e3e3e3; padding: 6px;">
                                    </div>

                                    <div class="custom-file mb-2 w-100 px-4">
                                        <input type="file"
                                            class="custom-file-input"
                                            id="logoInput"
                                            name="logo"
                                            accept="image/jpeg, image/png">
                                        <label class="custom-file-label" for="logoInput" id="fileLabel">
                                            Seleccionar archivo (JPEG, PNG)
                                        </label>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-danger mb-2" onclick="clearImage()">
                                        <i class="fas fa-times mr-1"></i>Quitar selección
                                    </button>

                                    <small class="form-text text-muted text-center">
                                        Formatos admitidos: JPEG, PNG<br>Máximo 2MB
                                    </small>
                                    <div class="invalid-feedback text-center" id="fileError"></div>
                                </div>

                                <!-- Columna de Campos Básicos -->
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="empresaInput" class="form-label"><strong>Nombre de la Empresa</strong></label>
                                        <input type="text"
                                            name="empresa"
                                            id="empresaInput"
                                            class="form-control @error('empresa') is-invalid @enderror"
                                            value="{{ old('empresa', $config->empresa ?? '') }}"
                                            required
                                            placeholder="Ingresa el nombre de la empresa">
                                        @error('empresa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="sucursalInput" class="form-label"><strong>Nombre de la Sucursal</strong></label>
                                        <input type="text"
                                            name="sucursal"
                                            id="sucursalInput"
                                            class="form-control @error('sucursal') is-invalid @enderror"
                                            value="{{ old('sucursal', $config->sucursal ?? '') }}"
                                            required
                                            placeholder="Ingresa el nombre de la sucursal">
                                        @error('sucursal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="tipoCorteSelect" class="form-label"><strong>Tipo de Corte</strong></label>
                                        <select name="tipo_corte" id="tipoCorteSelect" class="form-control @error('tipo_corte') is-invalid @enderror" required>
                                            <option value="">Seleccione un tipo de corte</option>
                                            @foreach($tiposCorte as $tipo)
                                                <option value="{{ $tipo->id }}"
                                                    {{ (old('tipo_corte', $config->tipo_corte ?? '') == $tipo->id) ? 'selected' : '' }}
                                                    data-tipo="{{ $tipo->tipo }}"
                                                    data-horarios="{{ $tipo->horarios ?? '' }}"
                                                    data-hora="{{ $tipo->hora ?? '' }}"
                                                    data-dias="{{ $tipo->dias ?? '' }}">
                                                    {{ $tipo->tipo }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipo_corte')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="row mt-3">
                                        <!-- Campo: Impresión de Ticket -->
                                        <div class="col-md-6">
                                            <label for="impresionTicket" class="form-label"><strong>Impresión de Ticket</strong></label>
                                            <select name="impresion_ticket" id="impresionTicket" class="form-control">
                                                <option value="on"  {{ old('impresion_ticket', $config->impresion_ticket ?? '') == 'on' ? 'selected' : '' }}>Activado</option>
                                                <option value="off" {{ old('impresion_ticket', $config->impresion_ticket ?? '') == 'off' ? 'selected' : '' }}>Desactivado</option>
                                            </select>
                                        </div>

                                        <!-- Campo: Alertas -->
                                        <div class="col-md-6">
                                            <label for="alertas" class="form-label"><strong>Alertas</strong></label>
                                            <select name="alertas" id="alertas" class="form-control">
                                                <option value="on"  {{ old('alertas', $config->alertas ?? '') == 'on' ? 'selected' : '' }}>Activadas</option>
                                                <option value="off" {{ old('alertas', $config->alertas ?? '') == 'off' ? 'selected' : '' }}>Desactivadas</option>
                                            </select>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <!-- Sección de Configuración de Horarios -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="config-section">
                                        <h5 class="border-bottom pb-2 mb-3 text-primary">
                                            <i class="fas fa-clock mr-2"></i>Configuración de Horarios
                                        </h5>

                                        <div class="row">
                                            <!-- Horarios Básicos -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="horariosInput" class="form-label"><strong>Horario Principal</strong></label>
                                                    <input type="time"
                                                        name="horarios"
                                                        id="horariosInput"
                                                        class="form-control"
                                                        value="{{ old('horarios', $config->horarios ?? '') }}"
                                                        placeholder="HH:MM">
                                                    <small class="form-text text-muted">Horario único principal</small>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="horaInput" class="form-label"><strong>Hora Intervalo</strong></label>
                                                    <input type="number"
                                                        name="hora"
                                                        id="horaInput"
                                                        class="form-control"
                                                        value="{{ old('hora', $config->hora ?? '') }}"
                                                        min="1"
                                                        max="12"
                                                        oninput="validarHoraIntervalo(this)"
                                                        onblur="formatearHoraIntervalo(this)"
                                                        placeholder="01-12">
                                                    <small class="form-text text-muted">Número de horas entre cortes (01-12, ej: 04, 06, 08)</small>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Select Predefinido de Días -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="presetDiasSelect" class="form-label"><strong>Preset de Días</strong></label>
                                                    <select id="presetDiasSelect" class="form-control">
                                                        <option value="">Selecciona un preset de días</option>
                                                        <option value="lun,mar,mie,jue,vie">Lunes a Viernes</option>
                                                        <option value="lun,mar,mie,jue,vie,sab,dom">Todos los días</option>
                                                        <option value="sab,dom">Sábado y Domingo</option>
                                                        <option value="lun,mar,mie">Lunes a Miércoles</option>
                                                        <option value="jue,vie,sab">Jueves a Sábado</option>
                                                        <option value="lun,mie,vie">Lunes, Miércoles, Viernes</option>
                                                        <option value="mar,jue,sab">Martes, Jueves, Sábado</option>
                                                        <option value="custom">Personalizado...</option>
                                                    </select>
                                                    <small class="form-text text-muted">
                                                        Selecciona un grupo predefinido de días
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Select Múltiple de Días (Personalizado) -->
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="diasSelect" class="form-label"><strong>Días de Aplicación</strong></label>
                                                    <select name="dias[]" id="diasSelect"
                                                            class="form-control select2 @error('dias') is-invalid @enderror"
                                                            multiple="multiple"
                                                            data-placeholder="Selecciona los días de aplicación">
                                                        @php
                                                            $diasSemana = [
                                                                'lun' => 'Lunes',
                                                                'mar' => 'Martes',
                                                                'mie' => 'Miércoles',
                                                                'jue' => 'Jueves',
                                                                'vie' => 'Viernes',
                                                                'sab' => 'Sábado',
                                                                'dom' => 'Domingo'
                                                            ];

                                                            $diasSeleccionados = old('dias',
                                                                isset($config->dias) ? explode(',', $config->dias) : []
                                                            );
                                                        @endphp

                                                        @foreach($diasSemana as $valor => $etiqueta)
                                                            <option value="{{ $valor }}"
                                                                {{ in_array($valor, $diasSeleccionados) ? 'selected' : '' }}>
                                                                {{ $etiqueta }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('dias')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">
                                                        Días en los que aplicará el corte automático
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer del Card -->
                        <div class="card-footer d-flex gap-2" style="background-color: #ffffff;">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Guardar Configuración
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                            </a>
                            <button type="button" class="btn btn-outline-info ms-auto" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Restablecer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel de Tipos de Corte Existentes -->
            <div class="col-md-12">
                <div class="card shadow rounded mb-4">
                    <div class="card-header bg-info text-white d-flex align-items-center justify-content-between"
                        type="button" data-bs-toggle="collapse" data-bs-target="#tiposCorteCollapse"
                        aria-expanded="false" aria-controls="tiposCorteCollapse" style="cursor: pointer;">
                        <h3 class="card-title mb-0"><i class="fas fa-cut mr-2"></i>Tipos de Corte del Sistema</h3>
                        <span class="accordion-indicator"><i class="fas fa-chevron-down"></i></span>
                    </div>

                    <div class="collapse" id="tiposCorteCollapse">
                        <div class="card-body">
                            @if($tiposCorte->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Horarios</th>
                                            <th>Hora</th>
                                            <th>Días</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tiposCorte as $tipo)
                                        <tr class="{{ (isset($config->tipo_corte) && $config->tipo_corte == $tipo->id) ? 'table-success' : '' }}">
                                            <td><strong>{{ $tipo->tipo }}</strong></td>
                                            <td>{{ $tipo->horarios ?? '-' }}</td>
                                            <td>{{ $tipo->hora ?? '-' }}</td>
                                            <td>
                                                @if($tipo->dias)
                                                    @php
                                                        $diasMap = [
                                                            'lun' => 'Lun',
                                                            'mar' => 'Mar',
                                                            'mie' => 'Mié',
                                                            'jue' => 'Jue',
                                                            'vie' => 'Vie',
                                                            'sab' => 'Sáb',
                                                            'dom' => 'Dom'
                                                        ];
                                                        $dias = explode(',', $tipo->dias);
                                                        $diasMostrar = array_map(function($dia) use ($diasMap) {
                                                            return $diasMap[$dia] ?? $dia;
                                                        }, $dias);
                                                    @endphp
                                                    {{ implode(', ', $diasMostrar) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($config->tipo_corte) && $config->tipo_corte == $tipo->id)
                                                    <span class="badge bg-success">Actual</span>
                                                @else
                                                    <span class="badge bg-secondary">Disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>No hay tipos de corte configurados en el sistema.
                            </div>
                            @endif

                            <!-- Información sobre tipos de corte -->
                            <div class="accordion mt-4" id="accordionTiposCorte">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                            <i class="fas fa-question-circle me-2"></i> ¿Cómo funcionan los tipos de corte?
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionTiposCorte">
                                        <div class="accordion-body">
                                            <div class="info-section">
                                                <p class="fw-bold">Un "corte" es un cierre de caja. El sistema hace un corte correspondiente dependiendo del que este configurado actualmente.</p>

                                                <div class="corte-option recomendado">
                                                    <h5 class="text-success"><i class="fas fa-user me-2"></i>Corte Parcial</h5>
                                                    <p class="mb-1">Cierra solo las ventas del usuario actual.</p>
                                                    <small class="text-muted">Ideal para tiendas pequeñas. Se hace manualmente al cerrar la sesion.</small>
                                                </div>

                                                <div class="corte-option">
                                                    <h5 class="text-primary"><i class="fas fa-clock me-2"></i>Corte por Horario Fijo</h5>
                                                    <p class="mb-1">Corte automático a una hora específica.</p>
                                                    <small class="text-muted">Configura la hora exacta para el corte automático.</small>
                                                </div>

                                                <div class="corte-option">
                                                    <h5 class="text-primary"><i class="fas fa-history me-2"></i>Corte Cada Varias Horas</h5>
                                                    <p class="mb-1">Cortes automáticos cada cierto número de horas.</p>
                                                    <small class="text-muted">Define el intervalo de horas entre cortes.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')

    <style>

        .field-disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: #f8f9fa;
        }

        .select2-container--bootstrap4 .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            min-height: 45px;
            padding: 2px;
        }

        .select2-container--bootstrap4.select2-container--focus .select2-selection--multiple {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            border-color: #006fe6;
            color: white;
            padding: 3px 8px;
            margin: 3px;
            border-radius: 0.25rem;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
            border: none;
            background: transparent;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffcccc;
        }

        .image-preview {
            border: 2px dashed #dee2e6;
            padding: 20px;
            border-radius: 10px;
            background: #fafafa;
            transition: transform 0.3s ease;
        }

        .image-preview:hover {
            transform: scale(1.02);
        }

        .corte-option {
            border-left: 4px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }

        .corte-option:hover {
            background-color: #f0f8ff;
            transform: translateX(5px);
        }

        .corte-option.recomendado {
            border-left-color: #198754;
            background-color: #f0fff4;
        }

        .accordion-button:not(.collapsed) {
            background-color: #eaf4ff;
            color: #0d6efd;
            box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
        }

        .card-header {
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .card-header:hover {
            background-color: #0b5ed7 !important;
        }

        .accordion-indicator {
            transition: transform 0.3s ease;
        }

        .card-header[aria-expanded="true"] .accordion-indicator {
            transform: rotate(180deg);
        }

        .info-section {
            background-color: #f8f9fa;
            border-left: 4px solid #0dcaf0;
            border-radius: 5px;
            padding: 15px;
        }

        .paso {
            background-color: #e9ecef;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }

        .ejemplo {
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
            padding: 10px 15px;
            border-radius: 5px;
            margin-top: 10px;
        }

        body {
            background-color: #f4f6f9 !important;
        }

        .custom-file-label::after {
            content: "Examinar";
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .custom-file-input:focus ~ .custom-file-label {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
    </style>

@endsection

@section('js')


    <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // console.log('DOM cargado, iniciando configuración...');
                    convertirHoraExistente();

                    // Inicializar Select2 para el select múltiple de días
                    if (typeof $ !== 'undefined' && $('#diasSelect').length) {
                        $('#diasSelect').select2({
                            theme: 'bootstrap4',
                            width: '100%',
                            language: 'es',
                            placeholder: 'Selecciona los días de aplicación',
                            allowClear: true,
                            closeOnSelect: false
                        });
                    }

                    // Obtener todos los elementos del DOM
                    const elementos = {
                        logoInput: document.getElementById('logoInput'),
                        logoPreview: document.getElementById('logoPreview'),
                        fileLabel: document.getElementById('fileLabel'),
                        fileError: document.getElementById('fileError'),
                        tipoCorteSelect: document.getElementById('tipoCorteSelect'),
                        horariosInput: document.getElementById('horariosInput'),
                        horaInput: document.getElementById('horaInput'),
                        diasSelect: $('#diasSelect'),
                        presetDiasSelect: document.getElementById('presetDiasSelect'),
                        form: document.getElementById('configForm')
                    };

                    // console.log('Elementos del formulario:', elementos);

                    // Mapeo de presets de días
                    const diasPresets = {
                        'lun,mar,mie,jue,vie': ['lun', 'mar', 'mie', 'jue', 'vie'],
                        'lun,mar,mie,jue,vie,sab,dom': ['lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom'],
                        'sab,dom': ['sab', 'dom'],
                        'lun,mar,mie': ['lun', 'mar', 'mie'],
                        'jue,vie,sab': ['jue', 'vie', 'sab'],
                        'lun,mie,vie': ['lun', 'mie', 'vie'],
                        'mar,jue,sab': ['mar', 'jue', 'sab']
                    };

                    // Función para manejar required condicional
                    function manejarRequiredCondicional() {
                        if (!elementos.tipoCorteSelect) return;

                        const selectedOption = elementos.tipoCorteSelect.options[elementos.tipoCorteSelect.selectedIndex];
                        const tipo = selectedOption ? selectedOption.getAttribute('data-tipo') : '';
                        const isParcial = tipo && tipo.toLowerCase().includes('parcial');

                        // console.log('Manejando required condicional. Es parcial:', isParcial);

                        // Obtener todos los campos que podrían ser requeridos
                        const camposRequeridos = [
                            document.getElementById('horariosInput'),
                            document.getElementById('horaInput'),
                            document.getElementById('diasSelect'),
                            document.getElementById('presetDiasSelect')
                        ].filter(Boolean); // Filtrar elementos nulos

                        camposRequeridos.forEach(campo => {
                            if (!campo) return;

                            if (isParcial) {
                                // Remover required para corte parcial
                                campo.removeAttribute('required');
                                // console.log('Removed required from:', campo.id);
                            } else {
                                // Agregar required para otros tipos
                                campo.setAttribute('required', 'required');
                                // console.log('Added required to:', campo.id);
                            }
                        });
                    }

                    // Función para aplicar preset de días
                    function aplicarPresetDias(presetValue) {
                        // console.log('Aplicando preset de días:', presetValue);

                        if (presetValue === 'custom') {
                            // Permitir selección personalizada
                            if (elementos.diasSelect && elementos.diasSelect.length) {
                                elementos.diasSelect.prop('disabled', false);
                                elementos.diasSelect.next('.select2-container').css({
                                    'opacity': '1',
                                    'cursor': ''
                                });
                            }
                            return;
                        }

                        if (diasPresets[presetValue] && elementos.diasSelect && elementos.diasSelect.length) {
                            elementos.diasSelect.val(diasPresets[presetValue]).trigger('change');
                            // console.log('Días seleccionados desde preset:', diasPresets[presetValue]);

                            // Deshabilitar la selección manual cuando se usa un preset
                            elementos.diasSelect.prop('disabled', true);
                            elementos.diasSelect.next('.select2-container').css({
                                'opacity': '0.7',
                                'cursor': 'not-allowed'
                            });
                        }
                    }

                    // Event listener para el select de presets
                    if (elementos.presetDiasSelect) {
                        elementos.presetDiasSelect.addEventListener('change', function() {
                            aplicarPresetDias(this.value);
                        });
                    }

                    // Función para actualizar campos según el tipo de corte seleccionado
                    function actualizarCamposHorarios() {
                        if (!elementos.tipoCorteSelect) return;

                        const selectedOption = elementos.tipoCorteSelect.options[elementos.tipoCorteSelect.selectedIndex];
                        if (!selectedOption) return;

                        const tipo = selectedOption.getAttribute('data-tipo');
                        const isParcial = tipo && tipo.toLowerCase().includes('parcial');
                        const isHorario = tipo && tipo.toLowerCase().includes('horario');
                        const isHora = tipo && tipo.toLowerCase().includes('hora');

                        // console.log('Tipo seleccionado:', tipo, 'isParcial:', isParcial, 'isHorario:', isHorario, 'isHora:', isHora);

                        // Obtener datos del tipo seleccionado
                        const datos = {
                            horarios: selectedOption.getAttribute('data-horarios'),
                            hora: selectedOption.getAttribute('data-hora'),
                            dias: selectedOption.getAttribute('data-dias')
                        };

                        // console.log('Datos del tipo seleccionado:', datos);

                        // PRIMERO: Manejar el caso de PARCIAL (bloquea todo)
                        if (isParcial) {
                            // console.log('Configurando campos para CORTE PARCIAL - deshabilitando horarios, hora y días');

                            // Bloquear y limpiar todos los campos
                            if (elementos.horariosInput) {
                                elementos.horariosInput.setAttribute('disabled', 'disabled');
                                elementos.horariosInput.classList.add('field-disabled');
                                elementos.horariosInput.value = '';
                                // console.log('Campo horarios deshabilitado y limpiado');
                            }

                            if (elementos.horaInput) {
                                elementos.horaInput.setAttribute('disabled', 'disabled');
                                elementos.horaInput.classList.add('field-disabled');
                                elementos.horaInput.value = '';
                                // console.log('Campo hora deshabilitado y limpiado');
                            }

                            if (elementos.presetDiasSelect) {
                                elementos.presetDiasSelect.setAttribute('disabled', 'disabled');
                                elementos.presetDiasSelect.classList.add('field-disabled');
                                elementos.presetDiasSelect.value = '';
                                // console.log('Preset días deshabilitado y limpiado');
                            }

                            if (elementos.diasSelect && elementos.diasSelect.length) {
                                elementos.diasSelect.prop('disabled', true);
                                elementos.diasSelect.val(null).trigger('change');
                                elementos.diasSelect.next('.select2-container').css({
                                    'opacity': '0.6',
                                    'cursor': 'not-allowed'
                                });
                                // console.log('Select días deshabilitado y limpiado');
                            }

                            return;
                        }

                        // SEGUNDO: Manejar HORARIO y HORA (casos no parciales)
                        // console.log('Configurando campos para tipo NO PARCIAL - habilitando campos según tipo');

                        // Primero habilitar todos los campos
                        if (elementos.horariosInput) {
                            elementos.horariosInput.removeAttribute('disabled');
                            elementos.horariosInput.classList.remove('field-disabled');
                            // console.log('Campo horarios habilitado');
                        }

                        if (elementos.horaInput) {
                            elementos.horaInput.removeAttribute('disabled');
                            elementos.horaInput.classList.remove('field-disabled');
                            // console.log('Campo hora habilitado');
                        }

                        if (elementos.presetDiasSelect) {
                            elementos.presetDiasSelect.removeAttribute('disabled');
                            elementos.presetDiasSelect.classList.remove('field-disabled');
                            // console.log('Preset días habilitado');
                        }

                        if (elementos.diasSelect && elementos.diasSelect.length) {
                            elementos.diasSelect.prop('disabled', false);
                            elementos.diasSelect.next('.select2-container').css({
                                'opacity': '1',
                                'cursor': ''
                            });
                            // console.log('Select días habilitado');
                        }

                        // Lógica para bloquear campos según el tipo de horario
                        if (isHorario) {
                            // console.log('Configurando campos para CORTE POR HORARIO - deshabilitando hora');

                            if (elementos.horaInput) {
                                elementos.horaInput.setAttribute('disabled', 'disabled');
                                elementos.horaInput.classList.add('field-disabled');
                                elementos.horaInput.value = '';
                                // console.log('Campo hora deshabilitado para corte por horario');
                            }

                            if (elementos.horariosInput && datos.horarios) {
                                elementos.horariosInput.value = datos.horarios;
                                // console.log('Valor de horarios cargado:', datos.horarios);
                            }
                        } else if (isHora) {
                            // console.log('Configurando campos para CORTE POR HORA INTERVALO - deshabilitando horarios');

                            if (elementos.horariosInput) {
                                elementos.horariosInput.setAttribute('disabled', 'disabled');
                                elementos.horariosInput.classList.add('field-disabled');
                                elementos.horariosInput.value = '';
                                // console.log('Campo horarios deshabilitado para corte por hora intervalo');
                            }

                            if (elementos.horaInput && datos.hora) {
                                let horaValue = datos.hora;
                                if (horaValue.includes(':')) {
                                    horaValue = horaValue.split(':')[0];
                                }
                                elementos.horaInput.value = horaValue;
                                // console.log('Valor de hora cargado:', horaValue);
                            }
                        } else {
                            // console.log('Configurando campos para TIPO NO ESPECÍFICO - cargando todos los valores disponibles');

                            if (elementos.horariosInput && datos.horarios) {
                                elementos.horariosInput.value = datos.horarios;
                                // console.log('Valor de horarios cargado:', datos.horarios);
                            }
                            if (elementos.horaInput && datos.hora) {
                                let horaValue = datos.hora;
                                if (horaValue.includes(':')) {
                                    horaValue = horaValue.split(':')[0];
                                }
                                elementos.horaInput.value = horaValue;
                                // console.log('Valor de hora cargado:', horaValue);
                            }
                        }

                        // TERCERO: Manejar los días para casos no parciales
                        if (elementos.diasSelect && elementos.diasSelect.length) {
                            if (datos.dias) {
                                const diasArray = datos.dias.split(',');
                                elementos.diasSelect.val(diasArray).trigger('change');
                                // console.log('Días cargados:', diasArray);

                                const diasString = diasArray.sort().join(',');
                                const presetEncontrado = Object.keys(diasPresets).find(preset =>
                                    preset.split(',').sort().join(',') === diasString
                                );

                                if (presetEncontrado && elementos.presetDiasSelect) {
                                    elementos.presetDiasSelect.value = presetEncontrado;
                                    // console.log('Preset encontrado:', presetEncontrado);
                                } else if (elementos.presetDiasSelect) {
                                    elementos.presetDiasSelect.value = 'custom';
                                    // console.log('Usando selección personalizada de días');
                                }
                            }
                        }

                        // Aplicar required condicional después de actualizar campos
                        manejarRequiredCondicional();
                    }

                    function convertirHoraExistente() {
                        const horaInput = document.getElementById('horaInput');
                        if (horaInput && horaInput.value && horaInput.value.includes(':')) {
                            const horas = horaInput.value.split(':')[0];
                            horaInput.value = horas;
                            // console.log('Hora convertida de HH:MM a HH:', horas);
                        }
                    }

                    // Función para manejar la subida de imágenes
                    function handleImageUpload(e) {
                        // console.log('Manejando upload de imagen');
                        const file = e.target.files[0];
                        if (!file) {
                            // console.log('No se seleccionó ningún archivo');
                            return;
                        }

                        // console.log('Archivo seleccionado:', file.name, 'Tipo:', file.type, 'Tamaño:', file.size);

                        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (!validTypes.includes(file.type)) {
                            // console.log('Tipo de archivo no válido:', file.type);
                            if (elementos.fileError) {
                                elementos.fileError.textContent = 'Formato no válido. Solo se permiten imágenes JPEG y PNG.';
                            }
                            if (elementos.logoInput) {
                                elementos.logoInput.classList.add('is-invalid');
                            }
                            return;
                        }

                        if (file.size > 2 * 1024 * 1024) {
                            // console.log('Archivo demasiado grande:', file.size);
                            if (elementos.fileError) {
                                elementos.fileError.textContent = 'La imagen es demasiado grande. Máximo permitido: 2MB.';
                            }
                            if (elementos.logoInput) {
                                elementos.logoInput.classList.add('is-invalid');
                            }
                            return;
                        }

                        if (elementos.fileError) {
                            elementos.fileError.textContent = '';
                        }
                        if (elementos.logoInput) {
                            elementos.logoInput.classList.remove('is-invalid');
                        }
                        if (elementos.fileLabel) {
                            elementos.fileLabel.textContent = file.name;
                        }
                        // console.log('Label actualizado con nombre de archivo:', file.name);

                        const reader = new FileReader();
                        reader.onload = function(event) {
                            if (elementos.logoPreview) {
                                elementos.logoPreview.src = event.target.result;
                                elementos.logoPreview.classList.add('loaded');
                                // console.log('Vista previa de imagen cargada');
                            }
                        };

                        reader.onerror = function() {
                            console.error('Error al leer el archivo');
                            if (elementos.fileError) {
                                elementos.fileError.textContent = 'Error al leer el archivo. Intenta con otra imagen.';
                            }
                        };

                        reader.readAsDataURL(file);
                    }

                    // Función para validar el formulario antes de enviar
                    function validarFormulario(e) {
                        // console.log('Validando formulario antes de enviar...');

                        if (!elementos.tipoCorteSelect) return true;

                        const tipoCorte = elementos.tipoCorteSelect.value;
                        const selectedOption = elementos.tipoCorteSelect.options[elementos.tipoCorteSelect.selectedIndex];
                        if (!selectedOption) return true;

                        const tipo = selectedOption.getAttribute('data-tipo');
                        const isParcial = tipo && tipo.toLowerCase().includes('parcial');
                        const isHorario = tipo && tipo.toLowerCase().includes('horario');
                        const isHora = tipo && tipo.toLowerCase().includes('hora');

                        const diasSeleccionados = elementos.diasSelect && elementos.diasSelect.length ? elementos.diasSelect.val() : [];
                        const presetSeleccionado = elementos.presetDiasSelect ? elementos.presetDiasSelect.value : '';

                        // console.log('Tipo de corte seleccionado:', tipoCorte, 'Tipo:', tipo, 'Es parcial:', isParcial, 'Es horario:', isHorario, 'Es hora:', isHora);
                        // console.log('Días seleccionados:', diasSeleccionados);
                        // console.log('Preset seleccionado:', presetSeleccionado);

                        // Validación condicional basada en el tipo de corte
                        if (!isParcial) {
                            // console.log('Validando campos para tipo NO parcial');

                            // Validar días (requerido para todos los tipos no parciales)
                            if ((!diasSeleccionados || diasSeleccionados.length === 0) && presetSeleccionado === 'custom') {
                                // console.log('ERROR: No hay días seleccionados para tipo no parcial');
                                e.preventDefault();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Días requeridos',
                                        text: 'Debes seleccionar al menos un día o un preset de días para aplicar el corte automático.',
                                        confirmButtonColor: '#007bff'
                                    });
                                }
                                return false;
                            }

                            // 🔥 CORRECCIÓN: Validar SOLO horarios para corte por HORARIO
                            if (isHorario) {
                                // console.log('Validando campos específicos para CORTE POR HORARIO');
                                if (elementos.horariosInput && !elementos.horariosInput.value.trim()) {
                                    // console.log('ERROR: Horarios requeridos para corte por horario');
                                    e.preventDefault();
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Horarios requeridos',
                                            text: 'Debes especificar los horarios para el corte por horario (ej: 09:00, 14:00, 18:00).',
                                            confirmButtonColor: '#007bff'
                                        });
                                    }
                                    return false;
                                }
                            }
                            // 🔥 CORRECCIÓN: Validar SOLO hora para corte por HORA INTERVALO
                            else if (isHora) {
                                // console.log('Validando campos específicos para CORTE POR HORA INTERVALO');
                                if (elementos.horaInput && (!elementos.horaInput.value || elementos.horaInput.value.trim() === '')) {
                                    // console.log('ERROR: Hora requerida para corte por hora intervalo');
                                    e.preventDefault();
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Hora requerida',
                                            text: 'Debes especificar la hora intervalo para el corte por hora (ej: 02 para cada 2 horas).',
                                            confirmButtonColor: '#007bff'
                                        });
                                    }
                                    return false;
                                }
                            }
                            // 🔥 CORRECCIÓN: Para otros tipos no específicos, validar ambos campos
                            else {
                                // console.log('Validando campos para TIPO NO ESPECÍFICO');
                                if (elementos.horariosInput && !elementos.horariosInput.value.trim() &&
                                    elementos.horaInput && (!elementos.horaInput.value || elementos.horaInput.value.trim() === '')) {
                                    // console.log('ERROR: Se requiere al menos horarios o hora intervalo');
                                    e.preventDefault();
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Configuración requerida',
                                            text: 'Debes especificar horarios o hora intervalo para la configuración seleccionada.',
                                            confirmButtonColor: '#007bff'
                                        });
                                    }
                                    return false;
                                }
                            }
                        }

                        // Asegurarnos de que los días seleccionados se incluyan en el FormData
                        if (diasSeleccionados && diasSeleccionados.length > 0 && elementos.form) {
                            // Eliminar cualquier input de días existente
                            document.querySelectorAll('input[name="dias[]"]').forEach(input => input.remove());

                            // Agregar los días seleccionados como inputs hidden al formulario
                            diasSeleccionados.forEach(dia => {
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'dias[]';
                                hiddenInput.value = dia;
                                elementos.form.appendChild(hiddenInput);
                            });

                            // console.log('Días agregados al formulario:', diasSeleccionados);
                        }

                        // Si hay preset seleccionado, asegurarnos de que se envíe
                        if (presetSeleccionado && presetSeleccionado !== 'custom' && elementos.form) {
                            // Eliminar cualquier input de preset existente
                            document.querySelectorAll('input[name="preset_dias"]').forEach(input => input.remove());

                            // Agregar el preset como input hidden
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'preset_dias';
                            hiddenInput.value = presetSeleccionado;
                            elementos.form.appendChild(hiddenInput);

                            // console.log('Preset agregado al formulario:', presetSeleccionado);
                        }

                        // Mostrar todos los datos que se enviarán
                        if (elementos.form) {
                            const formData = new FormData(elementos.form);
                            // console.log('=== DATOS QUE SE ENVIARÁN AL BACKEND ===');

                            // Mostrar campos simples
                            // console.log('Empresa:', formData.get('empresa'));
                            // console.log('Sucursal:', formData.get('sucursal'));
                            // console.log('Tipo Corte:', formData.get('tipo_corte'));
                            // console.log('Horarios:', formData.get('horarios'));
                            // console.log('Hora:', formData.get('hora'));
                            // console.log('Preset Días:', formData.get('preset_dias'));

                            // Mostrar días seleccionados (array)
                            const diasValues = formData.getAll('dias[]');
                            // console.log('Días (FormData):', diasValues);

                            // Mostrar información de archivo
                            const logoFile = elementos.logoInput ? elementos.logoInput.files[0] : null;
                            if (logoFile) {
                                // console.log('Logo:', logoFile.name, 'Tamaño:', logoFile.size, 'Tipo:', logoFile.type);
                            } else {
                                // console.log('Logo: No se seleccionó archivo');
                            }

                            // console.log('========================================');
                        }

                        return true;
                    }

                    // Event Listeners
                    if (elementos.tipoCorteSelect) {
                        elementos.tipoCorteSelect.addEventListener('change', function() {
                            actualizarCamposHorarios();
                            manejarRequiredCondicional();
                        });
                        // console.log('Event listener agregado para cambio de tipo de corte');
                    }

                    if (elementos.logoInput) {
                        elementos.logoInput.addEventListener('change', handleImageUpload);
                        // console.log('Event listener agregado para cambio de logo');
                    }

                    if (elementos.form) {
                        elementos.form.addEventListener('submit', validarFormulario);
                        // console.log('Event listener agregado para envío de formulario');
                    }

                    // Permitir selección manual cuando el usuario interactúa con el select múltiple
                    if (elementos.diasSelect && elementos.diasSelect.length) {
                        elementos.diasSelect.on('select2:opening', function() {
                            // console.log('Select2 de días abierto, cambiando a modo personalizado');
                            if (elementos.presetDiasSelect) {
                                elementos.presetDiasSelect.value = 'custom';
                            }
                            elementos.diasSelect.prop('disabled', false);
                            elementos.diasSelect.next('.select2-container').css({
                                'opacity': '1',
                                'cursor': ''
                            });
                        });
                    }

                    // Inicializar campos al cargar la página
                    // console.log('Inicializando campos al cargar la página...');
                    actualizarCamposHorarios();
                    manejarRequiredCondicional();
                    // console.log('Configuración inicial completada');
                });

                function clearImage() {
                    // console.log('Limpiando imagen seleccionada');
                    const logoInput = document.getElementById('logoInput');
                    const logoPreview = document.getElementById('logoPreview');
                    const fileLabel = document.getElementById('fileLabel');
                    const fileError = document.getElementById('fileError');

                    if (logoInput) {
                        logoInput.value = '';
                        logoInput.classList.remove('is-invalid');
                    }

                    if (fileLabel) fileLabel.textContent = 'Seleccionar archivo (JPEG, PNG)';
                    if (fileError) fileError.textContent = '';

                    if (logoPreview) {
                        logoPreview.classList.remove('loaded');
                    }

                    // console.log('Imagen limpiada correctamente');
                }

                function resetForm() {
                    // console.log('Iniciando restablecimiento de formulario');
                    if (typeof Swal === 'undefined') {
                        console.error('SweetAlert2 no está cargado');
                        return;
                    }

                    Swal.fire({
                        title: '¿Restablecer configuración?',
                        text: 'Todos los cambios no guardados se perderán. ¿Estás seguro?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, restablecer',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('configForm');
                            if (form) {
                                form.reset();

                                if (typeof $ !== 'undefined' && $('#diasSelect').length) {
                                    $('#diasSelect').val(null).trigger('change');
                                }

                                clearImage();

                                // Forzar actualización de campos
                                const tipoCorteSelect = document.getElementById('tipoCorteSelect');
                                if (tipoCorteSelect) {
                                    const event = new Event('change');
                                    tipoCorteSelect.dispatchEvent(event);
                                }

                                // console.log('Formulario restablecido correctamente');

                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Restablecido!',
                                    text: 'El formulario ha sido restablecido a los valores predeterminados.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        } else {
                            // console.log('Restablecimiento cancelado por el usuario');
                        }
                    });
                }
    </script>

    <script>
        // Función para validar la hora intervalo (01-12)
        function validarHoraIntervalo(input) {
            // console.log('Validando hora intervalo:', input.value);
            let value = input.value;

            if (value === '') {
                // console.log('Campo hora vacío');
                return;
            }

            let numValue = parseInt(value);

            if (isNaN(numValue)) {
                // console.log('Valor de hora no es un número:', value);
                input.value = '';
                return;
            }

            // Aplicar límites 1-12
            if (numValue < 1) {
                // console.log('Valor de hora muy bajo, ajustando a 1');
                input.value = '1';
            } else if (numValue > 12) {
                // console.log('Valor de hora muy alto, ajustando a 12');
                input.value = '12';
            } else {
                // console.log('Valor de hora válido:', input.value);
            }
        }

        // Función para formatear la hora con dos dígitos
        function formatearHoraIntervalo(input) {
            // console.log('Formateando hora intervalo:', input.value);
            if (input.value && input.value !== '') {
                let numValue = parseInt(input.value);
                if (!isNaN(numValue)) {
                    // Asegurar que esté en el rango correcto
                    if (numValue < 1) numValue = 1;
                    if (numValue > 12) numValue = 12;
                    // Formatear a dos dígitos
                    input.value = numValue.toString().padStart(2, '0');
                    // console.log('Hora formateada a dos dígitos:', input.value);
                }
            }
        }

        // Si necesitas inicializar el valor al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // console.log('Inicializando validación de hora al cargar la página');
            const horaInput = document.getElementById('horaInput');
            if (horaInput && horaInput.value) {
                // console.log('Valor inicial de hora:', horaInput.value);
                formatearHoraIntervalo(horaInput);
            }
        });
    </script>

@endsection

@endrole
