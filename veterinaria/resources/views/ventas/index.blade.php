@hasanyrole(['superadmin' , 'admin'])
@extends('adminlte::page')

@section('title', 'Ventas')

@section('content_header')
    <h1>Ventas</h1>
@stop


@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center p-2"> <!-- Añadido p-2 para reducir padding -->
            <h3 class="card-title mb-0 flex-grow-1">Listado de Ventas</h3> <!-- flex-grow-1 para que ocupe el espacio disponible -->
            <div class="d-flex align-items-center ms-auto"> <!-- ms-auto para empujar a la derecha -->
                <button type="button" class="btn btn-light me-2" data-bs-toggle="collapse" data-bs-target="#filtrosSection">
                    <i class="fas fa-filter text-secondary"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>

        <!-- Sección de Filtros (colapsable) -->
        <div class="collapse" id="filtrosSection">
            <div class="card-body pt-0 pb-2">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 col-sm-6">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" id="fecha_inicio" class="form-control" max="">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" id="fecha_fin" class="form-control" max="">
                    </div>
                    <div class="col-md-6 col-sm-12 d-flex align-items-end">
                        <button id="aplicar_filtro" class="btn btn-secondary me-2">
                            <i class="fas fa-filter me-1"></i> Aplicar
                        </button>
                        <button id="limpiar_filtro" class="btn btn-outline-info me-2">
                            <i class="fas fa-sync-alt me-1"></i>
                        </button>
                        <div class="btn-group">
                            <button id="exportar_excel" class="btn btn-success">
                                <i class="fas fa-file-excel me-1"></i>
                            </button>
                            <button id="exportar_pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body pt-3 pb-2">
            <div class="table-responsive" style="margin-bottom: 20px;">
                <table id="ventasTable" class="table table-bordered table-hover table-striped mb-0" style="width:100%">
                    <thead class="bg-lightblue">
                        <tr>
                            <th class="text-center">Folio</th>
                            <th class="text-left">Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Precio Unitario</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-center">Tipo Pago</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Fecha</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-2">
            <div class="d-flex justify-content-between">
                <div class="text-muted small">
                    Mostrando <span id="registrosMostrados">0</span> registros
                </div>
                <div class="text-muted small">
                    Última actualización: <span id="ultimaActualizacion">{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

    @section('css')
        <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                /* Estilos generales */
                html, body {
                    height: 100%;
                }

                .wrapper {
                    min-height: 100%;
                    display: flex;
                    flex-direction: column;
                }

                .content-wrapper {
                    flex: 1;
                    padding-bottom: 20px; /* Espacio para el footer */
                }

                /* Estilos para la tabla */
                .table-responsive {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    margin: 0 -15px; /* Compensa el padding del card-body */
                    padding: 0 15px; /* Vuelve a agregar el padding */
                }



                /* Espaciados mejorados */
                .card {
                    margin-bottom: 20px;
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                }

                .card-header {
                    padding: 0.75rem 1.25rem;
                }

                .card-body {
                    padding: 1.25rem;
                }

                .card-footer {
                    padding: 0.75rem 1.25rem;
                    background-color: rgba(0, 0, 0, 0.03);
                }

                /* Ajustes para móviles */
                @media (max-width: 768px) {
                    .card-header h3 {
                        font-size: 1.2rem;
                    }

                    .btn {
                        padding: 0.375rem 0.75rem;
                        font-size: 0.875rem;
                        margin-bottom: 5px;
                    }

                    .card-body {
                        padding: 0.75rem;
                    }

                    .filter-section > div {
                        margin-bottom: 10px;
                    }
                }

            </style>
    @stop


@section('js')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 2. Variables globales
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const fechaFinInput = document.getElementById('fecha_fin');
        const today = new Date().toISOString().split('T')[0];
        let exportInProgress = false;

        // 3. Configuración de fechas
        function setupDateFilters() {
            fechaInicioInput.max = today;
            fechaFinInput.max = today;

            fechaInicioInput.addEventListener('change', function() {
                if (this.value) {
                    fechaFinInput.min = this.value;
                    if (fechaFinInput.value && this.value > fechaFinInput.value) {
                        fechaFinInput.value = '';
                    }
                } else {
                    fechaFinInput.min = '';
                }
            });

            fechaFinInput.addEventListener('change', function() {
                if (this.value) {
                    fechaInicioInput.max = this.value;
                    if (fechaInicioInput.value && this.value < fechaInicioInput.value) {
                        fechaInicioInput.value = '';
                    }
                } else {
                    fechaInicioInput.max = today;
                }
            });
        }

        // 4. Función para mostrar alertas
        function showAlert(message, type = 'success') {
            const alertId = 'alert-' + Date.now();
            const alertDiv = document.createElement('div');
            alertDiv.id = alertId;
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            document.querySelector('.content-header').after(alertDiv);

            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // 5. Función para mostrar loader durante exportaciones
        function showExportLoader(show = true) {
            const loaderId = 'export-loader';
            let loader = document.getElementById(loaderId);

            if (show && !loader) {
                loader = document.createElement('div');
                loader.id = loaderId;
                loader.className = 'export-loader';
                loader.innerHTML = `
                    <div class="spinner-border text-white" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Generando archivo...</span>
                    </div>
                    <p class="mt-2">Preparando descarga...</p>
                `;
                document.body.appendChild(loader);
            } else if (!show && loader) {
                loader.remove();
            }
        }

        // Función reutilizable para exportar a Excel
        function exportToExcel() {
            if (exportInProgress) {
                return;
            }

            exportInProgress = true;
            const fechaInicio = fechaInicioInput.value;
            const fechaFin = fechaFinInput.value;

            showExportLoader(true);

            // Construir URL con parámetros
            const url = new URL('/exportar/excel', window.location.origin);
            if (fechaInicio) url.searchParams.append('fecha_inicio', fechaInicio);
            if (fechaFin) url.searchParams.append('fecha_fin', fechaFin);

            fetch(url, {
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`Error ${response.status}: ${text.substring(0, 100)}`);
                    });
                }
                return response.blob();
            })
            .then(blob => {
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = `ventas_${fechaInicio || 'inicio'}_${fechaFin || 'hoy'}.xlsx`;
                document.body.appendChild(a);
                a.click();

                setTimeout(() => {
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(downloadUrl);
                }, 100);

                showAlert('Archivo Excel generado correctamente', 'success');
            })
            .catch(error => {
                showAlert(`Error al generar Excel: ${error.message}`, 'danger');
            })
            .finally(() => {
                exportInProgress = false;
                showExportLoader(false);
            });
        }

        function exportToPDF() {
            if (exportInProgress) {
                return;
            }

            exportInProgress = true;
            const fechaInicio = fechaInicioInput.value;
            const fechaFin = fechaFinInput.value;

            showExportLoader(true);

            // Construir URL con parámetros
            const url = new URL('/exportar/pdf', window.location.origin);
            if (fechaInicio) url.searchParams.append('fecha_inicio', fechaInicio);
            if (fechaFin) url.searchParams.append('fecha_fin', fechaFin);

            fetch(url, {
                headers: {
                    'Accept': 'application/json, application/pdf',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');

                if (contentType && contentType.includes('application/pdf')) {
                    return response.blob();
                }

                if (contentType && contentType.includes('application/json')) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `Error en el servidor (${response.status})`);
                }

                const text = await response.text();
                throw new Error('Error inesperado en el servidor');
            })
            .then(blob => {
                if (!blob) {
                    throw new Error('No se recibió ningún archivo PDF');
                }

                const fileName = `ventas_${fechaInicio || 'inicio'}_${fechaFin || 'hoy'}.pdf`;
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();

                setTimeout(() => {
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(downloadUrl);
                }, 100);

                showAlert('PDF generado correctamente', 'success');
            })
            .catch(error => {
                let userMessage = 'Error al generar el PDF';
                if (error.message.includes('500') || error.message.includes('servidor')) {
                    userMessage = 'Error en el servidor. Por favor intente más tarde.';
                } else if (error.message.includes('404')) {
                    userMessage = 'No se encontraron datos para las fechas seleccionadas.';
                } else {
                    userMessage = error.message;
                }

                showAlert(userMessage, 'danger');
            })
            .finally(() => {
                exportInProgress = false;
                showExportLoader(false);
            });
        }

        // 6. Configuración de DataTables
        let table = new DataTable('#ventasTable', {
            ajax: {
                url: 'detalleventa/data',
                data: function(d) {
                    const fechaInicio = fechaInicioInput.value;
                    const fechaFin = fechaFinInput.value;

                    if (fechaInicio && fechaFin) {
                        d.fecha_inicio = fechaInicio;
                        d.fecha_fin = fechaFin;
                    }
                },
                dataSrc: 'data',
                error: function(xhr, error, thrown) {
                    showAlert('Error al cargar datos. Por favor intente nuevamente.', 'danger');
                }
            },
            columns: [
                { data: 'folio', className: 'text-center' },
                { data: 'producto_nombre', className: 'text-left' },
                { data: 'cantidad', className: 'text-center' },
                {
                    data: 'precio_unitario',
                    className: 'text-right',
                    render: (data) => data ? '$' + parseFloat(data).toFixed(2) : '---'
                },
                {
                    data: 'subtotal',
                    className: 'text-right',
                    render: (data) => data ? '$' + parseFloat(data).toFixed(2) : '---'
                },
                { data: 'tipo_pago', className: 'text-center' },
                {
                    data: 'total',
                    className: 'text-right',
                    render: (data) => data ? '$' + parseFloat(data).toFixed(2) : '---'
                },
                {
                    data: 'created_at',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if(type === 'display') {
                            return new Date(data).toLocaleString('es-MX');
                        }
                        return data; 
                    }
                }

            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.3.2/i18n/es-ES.json'
            },
            dom: '<"row"<"col-md-6"B><"col-md-6"f>>rtip',
            buttons: [
                {
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success me-2',
                    action: function() {
                        exportToExcel();
                    }
                },
                {
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger',
                    action: function() {
                        exportToPDF();
                    }
                }
            ],
            responsive: true,
            order: [[7, 'desc']]
        });

        // 7. Eventos de los botones de filtro
        document.getElementById('aplicar_filtro').addEventListener('click', function() {
            const fechaInicio = fechaInicioInput.value;
            const fechaFin = fechaFinInput.value;

            if (fechaInicio && !fechaFin) {
                showAlert('Debe seleccionar una fecha final', 'warning');
                return;
            }

            if (!fechaInicio && fechaFin) {
                showAlert('Debe seleccionar una fecha inicial', 'warning');
                return;
            }

            if (fechaInicio && fechaFin) {
                if (fechaInicio > fechaFin) {
                    showAlert('La fecha inicial no puede ser mayor a la fecha final', 'warning');
                    return;
                }

                if (new Date(fechaInicio) > new Date() || new Date(fechaFin) > new Date()) {
                    showAlert('No puede seleccionar fechas futuras', 'warning');
                    return;
                }
            }

            table.ajax.reload();
        });

        document.getElementById('limpiar_filtro').addEventListener('click', function() {
            fechaInicioInput.value = '';
            fechaFinInput.value = '';
            fechaInicioInput.max = today;
            fechaFinInput.min = '';
            fechaFinInput.max = today;
            table.ajax.reload();
        });

        // Asignar eventos a los botones HTML externos
        document.getElementById('exportar_excel').addEventListener('click', exportToExcel);
        document.getElementById('exportar_pdf').addEventListener('click', exportToPDF);

        // 8. Inicialización final
        setupDateFilters();
    });

    // 9. Manejo de errores no capturados
    window.addEventListener('error', function(event) {
        showAlert('Ocurrió un error inesperado. Consulte la consola para detalles.', 'danger');
    });
</script>

@stop

@endrole
