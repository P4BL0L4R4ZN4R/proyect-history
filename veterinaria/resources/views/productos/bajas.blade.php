

@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')

@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success small">{{ session('success') }}</div> <!-- Reducido tamaño de fuente -->
@endif

@include('productos.create')
@include('productos.edit')

    <!-- Tabla de productos -->
    <div class="card mt-2" style="margin-top: -2px; border-radius: 0.75rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); overflow: hidden; border: 1px solid rgba(0, 0, 0, 0.1);">

        <div class="card-header p-2 bg-secondary" style="padding: 0.5rem 1rem;"> <!-- Corregido error de clase duplicada, reducido padding -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-1"> <!-- Reducido gap a 1 -->
                <!-- Título -->
                <h6 class="h6 mb-0 text-white small"> <!-- Mantenido h6 para consistencia -->
                    <i class="fas fa-boxes me-1"></i> Lista de Productos
                </h6>

                <!-- Controles superiores -->
                <div class="d-flex align-items-center gap-1"> <!-- Mantenido gap reducido -->
                    <!-- Botón de filtros -->
                    <button class="btn btn-light btn-sm mb-1" type="button" data-bs-toggle="collapse" data-bs-target="#panelFiltros"> <!-- Reducido mb-2 a mb-1 -->
                        <i class="fas fa-filter me-1 text-secondary"></i>
                    </button>

                    @hasanyrole(['superadmin' , 'admin'])
                    <button class="btn btn-success btn-sm mb-1" id="AbrirModalProducto"> <!-- Reducido mb-2 a mb-1 -->
                        <i class="fas fa-plus me-1"></i> Nuevo
                    </button>
                    @endrole
                </div>
            </div>

            <!-- Panel de Filtros desplegable - Versión compacta -->
            <div class="collapse mt-1 pt-0" id="panelFiltros"> <!-- Reducido margin top y padding -->
                <div class="row g-1 align-items-end"> <!-- Mantenido gap reducido -->
                    <!-- Filtro Categoría -->
                    <div class="col-md-3 col-6">
                        <label for="filtroCategoria" class="form-label fs-6 text-white mb-0 d-block">Categoría</label> <!-- Eliminado mb-1 -->
                        <div class="input-group input-group-sm" style="width: 100%;">
                            <select id="filtroCategoria" class="form-select form-select-sm select2">
                                <option value="">Todas</option>
                                @foreach($categorias as $categoria)
                                <option value="{{ $categoria->nombre }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filtro Precio -->
                    <div class="col-md-3 col-6">
                        <label for="filtroPrecio" class="form-label fs-6 text-white mb-0 d-block">Precio</label> <!-- Eliminado mb-1 -->
                        <select id="filtroPrecio" class="form-select form-select-sm select2">
                            <option value="">Todos</option>
                            <option value="0-50">$0-$50</option>
                            <option value="50-100">$50-$100</option>
                            <option value="100-500">$100-$500</option>
                            <option value="500+">$500+</option>
                        </select>
                    </div>

                    <!-- Filtro Caducidad -->
                    <div class="col-md-2 col-6">
                        <label for="filtroCaducidad" class="form-label fs-6 text-white mb-0 d-block">Caducidad</label> <!-- Eliminado mb-1 -->
                        <select id="filtroCaducidad" class="form-select form-select-sm select2">
                            <option value="">Todas</option>
                            <option value="vigentes">Vigentes</option>
                            <option value="proximas">Proximas</option>
                            <option value="vencidas">Vencidas</option>
                        </select>
                    </div>

                    <!-- Botones de acción -->
                    <div class="col-md-2 col-12 mt-md-0 mt-0">
                        <div class="d-flex gap-1">
                            <button onclick="resetFiltros()" class="btn btn-light btn-sm flex-grow-1"> <!-- Eliminado mb-2 -->
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-2" style="background-color: #ffffff; padding: 1rem;"> <!-- Mantenido padding original -->
            <div class="table-responsive" style="max-height: calc(100vh - 150px); overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 0.5rem;">
                <table id="productosTable" class="table table-bordered mb-0 table-sm small" style="margin-bottom: 0; font-size: 0.875rem;">
                    <thead class="sticky-top bg-light"> <!-- Mantenido bg-secondary y text-white -->
                        <tr>
                            <th class="text-nowrap p-1 th-custom">Nombre</th> <!-- Agregada clase th-custom para personalización -->
                            <th class="text-nowrap p-1 th-custom">Stock</th>
                            <th class="text-nowrap p-1 th-custom">Precio</th>
                            <th class="text-nowrap p-1 th-custom">Código</th>
                            <th class="text-nowrap p-1 th-custom">Categoría</th>
                            <th class="text-nowrap p-1 th-custom">Lote</th>
                            <th class="text-nowrap p-1 th-custom">Caducidad</th>
                            @hasanyrole(['superadmin' , 'admin'])
                            <th class="text-nowrap p-1 th-custom">Acciones</th> <!-- Agregada clase th-custom -->
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Filas dinámicas -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

    @section('js')


        {{-- Select2 --}}
        <script>
                    // 3. Implementación profesional con triple verificación
                    function initSelect2() {
                        // Verificación jQuery
                        if (typeof jQuery === 'undefined') {
                            // console.error('jQuery no está disponible');
                            return;
                        }

                        // console.log('jQuery confirmado:', jQuery.fn.jquery);

                        // Verificación Select2
                        if (typeof jQuery.fn.select2 === 'function') {
                            initializeSelect2();
                            return;
                        }

                        // console.warn('Select2 no disponible. Cargando alternativas...');
                        loadSelect2Alternatives();
                    }

                    // 4. Inicialización segura
                    function initializeSelect2() {
                        try {
                            jQuery('.select2').select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                placeholder: 'Seleccione...',
                                allowClear: true,
                                language: 'es'
                            });
                            // console.log('Select2 inicializado correctamente');
                        } catch(e) {
                            // console.error('Error en inicialización:', e);
                        }
                    }

                    // 5. Sistema de 3 niveles de fallback
                    function loadSelect2Alternatives() {
                        const sources = [
                            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js',
                            '/js/select2.min.js', // Ruta local alternativa
                            'https://raw.githubusercontent.com/select2/select2/4.0.13/dist/js/select2.min.js'
                        ];

                        tryLoadSource(0);

                        function tryLoadSource(index) {
                            if (index >= sources.length) {
                                // console.error('Todos los intentos fallaron');
                                showErrorAlert();
                                return;
                            }

                            // console.log('Intentando cargar:', sources[index]);
                            jQuery.ajax({
                                url: sources[index],
                                dataType: 'script',
                                cache: true
                            })
                            .done(function() {
                                // console.log('Éxito cargando:', sources[index]);
                                initializeSelect2();
                            })
                            .fail(function() {
                                // console.warn('Falló:', sources[index]);
                                tryLoadSource(index + 1);
                            });
                        }
                    }

                    // 6. Notificación al usuario
                    function showErrorAlert() {
                        // Puedes personalizar este mensaje
                        alert('Error al cargar componentes. Por favor recarga la página.');
                    }

                    // 7. Disparadores de inicialización
                    if (document.readyState === 'complete') {
                        initSelect2();
                    } else {
                        jQuery(document).ready(initSelect2);
                    }
        </script>

        {{-- modales --}}

        <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                    // --- Abrir modal de creación ---
                    function toggleProductModal() {
                        const modal = new bootstrap.Modal(document.getElementById('modalProducto'));
                        modal.toggle();
                    }
                    document.getElementById('AbrirModalProducto')?.addEventListener('click', toggleProductModal);

                    // --- Abrir modal de edición ---
                    window.abrirModalEditarProducto = function(id) {
                        const modalEl = document.getElementById('modalEditarProducto');
                        if (!modalEl) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'No se encontró el modal de edición de producto.' });
                            return;
                        }

                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();

                        fetch(`/productos/${id}/edit`)
                            .then(res => {
                                if (!res.ok) throw new Error('No se pudo obtener los datos del producto');
                                return res.json();
                            })
                            .then(data => {
                                const producto = data.producto;

                                // Rellenar campos del formulario
                                ['nombre','stock','minimo_stock','codigo','precio_compra','precio_venta','lote','caducidad','descripcion'].forEach(name => {
                                    const el = modalEl.querySelector(`[name="${name}"]`);
                                    if(el) el.value = producto[name] || '';
                                });

                                // Categoría y subcategoría
                                ['categoria_id','subcategoria_id'].forEach(name => {
                                    const select = modalEl.querySelector(`[name="${name}"]`);
                                    if(select) {
                                        select.value = producto[name] || '';
                                        select.dispatchEvent(new Event('change'));
                                    }
                                });

                                // Reset toggles
                                ['categoria','subcategoria'].forEach(tipo => {
                                    const select = document.getElementById(`${tipo}-select-edit`);
                                    const input = document.getElementById(`${tipo}-input-edit`);
                                    const btn = document.getElementById(`btnToggle${tipo.charAt(0).toUpperCase()+tipo.slice(1)}Edit`);
                                    if(select && input && btn){
                                        select.style.display = 'block';
                                        input.style.display = 'none';
                                        btn.innerHTML = '<i class="fas fa-plus"></i>';
                                        btn.classList.remove('btn-danger');
                                        btn.classList.add(tipo==='categoria' ? 'btn-success':'btn-primary');
                                    }
                                });

                                // Acción del form
                                const form = modalEl.querySelector('#formEditarProducto');
                                if(form) form.action = `/productos/${producto.id}`;
                            })
                            .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los datos del producto', footer: err.message }));
                    };

                    // --- Toggle categoría/subcategoría ---
                    function toggleField(tipo, modo) {
                        const select = document.getElementById(`${tipo}-select-${modo}`);
                        const input = document.getElementById(`${tipo}-input-${modo}`);
                        const btn = document.getElementById(`btnToggle${tipo.charAt(0).toUpperCase()+tipo.slice(1)}${modo.charAt(0).toUpperCase()+modo.slice(1)}`);
                        if(select.style.display==='none'){
                            select.style.display='block';
                            input.style.display='none';
                            btn.innerHTML='<i class="fas fa-plus"></i>';
                            btn.classList.remove('btn-danger');
                            btn.classList.add(tipo==='categoria'?'btn-success':'btn-primary');
                            input.value='';
                        } else {
                            select.style.display='none';
                            input.style.display='block';
                            btn.innerHTML='<i class="fas fa-minus"></i>';
                            btn.classList.remove(tipo==='categoria'?'btn-success':'btn-primary');
                            btn.classList.add('btn-danger');
                            select.selectedIndex = 0;
                        }
                    }
                    window.toggleCategoria = (modo) => toggleField('categoria', modo);
                    window.toggleSubcategoria = (modo) => toggleField('subcategoria', modo);

                    // --- Validación y envío de formularios ---
                    const forms = [
                        document.querySelector('#modalProducto form'),       // Creación
                        document.querySelector('#modalEditarProducto form')  // Edición
                    ];

                    forms.forEach(form => {
                        if(!form) return;

                        form.addEventListener('submit', function(e){
                            e.preventDefault();
                            const nombre = this.elements.nombre.value.trim();
                            if(!nombre){
                                Swal.fire({ icon:'warning', title:'Campo obligatorio', text:'El campo Nombre es obligatorio' })
                                    .then(()=>this.elements.nombre.focus());
                                return;
                            }

                            const isEdit = this.id==='formEditarProducto';
                            const actionText = isEdit ? 'editar' : 'crear';
                            const method = isEdit ? 'PUT' : 'POST'; // ← MÉTODO CORRECTO

                            Swal.fire({
                                title:`¿Deseas ${actionText} este producto?`,
                                icon:'question',
                                showCancelButton:true,
                                confirmButtonText:'Sí, confirmar',
                                cancelButtonText:'Cancelar'
                            }).then(result=>{
                                if(result.isConfirmed){
                                    const formData = new FormData(this);


                                    if (isEdit) {
                                        formData.append('_method', 'PUT');

                                    }

                                    fetch(this.action, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken,
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Error del servidor: ' + response.status);
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        if(data.success){
                                            Swal.fire('Éxito', `Producto ${actionText} correctamente`, 'success');
                                            const modal = bootstrap.Modal.getInstance(this.closest('.modal'));
                                            table.ajax.reload();
                                            modal.hide();

                                            if(window.table) {
                                                table.ajax.reload(null, false);
                                            }
                                        } else {
                                            Swal.fire('Error', data.message || 'No se pudo procesar la solicitud', 'error');
                                               table.ajax.reload();
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        table.ajax.reload();
                                        Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
                                    });
                                }
                            });
                        });
                    });


                    ['modalProducto','modalEditarProducto'].forEach(id => {
                        const modalEl = document.getElementById(id);
                        modalEl?.addEventListener('hidden.bs.modal', function() {
                            const form = this.querySelector('form');
                            if(form) form.reset();

                            document.querySelectorAll('[id^="categoria-select-"], [id^="subcategoria-select-"]').forEach(el => el.style.display='block');
                            document.querySelectorAll('[id^="categoria-input-"], [id^="subcategoria-input-"]').forEach(el => { el.style.display='none'; el.value=''; });
                            document.querySelectorAll('[id^="btnToggleCategoria"], [id^="btnToggleSubcategoria"]').forEach(btn => btn.innerHTML='<i class="fas fa-plus"></i>');
                        });
                    });
                });
        </script>




    {{-- Datatables --}}

    <script>

        let table;
        $(document).ready(function () {
            const userHasRole = @json(Auth::user()->hasAnyRole(['admin', 'superadmin']));
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            table = new DataTable('#productosTable', {
                serverSide: true,
                processing: true,
                ajax: {
                    url: '{{ route("productos.databajas") }}',
                    type: 'GET',
                    data: function (d) {
                        return $.extend({}, d, {
                            categoriaFiltro: $('#filtroCategoria').val(),
                            estadoFiltro: $('#filtroEstado').val(),
                            precioFiltro: $('#filtroPrecio').val(),
                            caducidadFiltro: $('#filtroCaducidad').val(),
                            subcategoriaFiltro: $('#filtroSubcategoria').val()
                        });
                    },
                    error: function(xhr, error, thrown) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en la solicitud',
                            text: 'No se pudieron cargar los productos. Intenta nuevamente.',
                            footer: `<pre>${xhr.responseText}</pre>`
                        });
                    }
                },
                order: [[8, 'desc']],
                columns: [
                    { data: 'nombre', name: 'nombre' },
                    {
                        data: 'stock',
                        name: 'stock',
                        orderable: true,
                        searchable: false,
                        render: function(data) {
                            const [cantidad, alerta] = data.split('|');
                            const icono = alerta == '1' ? '<i class="fas fa-arrow-down text-warning ms-1"></i>' : '';

                            return cantidad + icono;
                        }
                    },

                    {
                        data: 'precio_venta',
                        name: 'precio_venta',
                        render: data => data ? `$${parseFloat(data).toFixed(2)}` : '$0.00',
                        orderable: true,
                        searchable: false
                    },
                    { data: 'codigo', name: 'codigo', orderable: true },
                    {
                        data: null,
                        name: 'categoria_subcategoria',
                        render: function(data, type, row) {

                            return `${row.categoria || 'Sin categoría'}${row.subcategoria ? '/' + row.subcategoria : ''}`;
                        },
                        orderable: true
                    },
                    { data: 'lote', name: 'lote', orderable: true },
                    {
                        data: 'caducidad',
                        name: 'caducidad',
                        render: function(data) {
                            const [fecha, estado] = data.split('|');
                            let icono = '';
                            if (estado === 'vencido') icono = '<i class="fas fa-skull-crossbones ms-2 text-danger p-1 rounded"></i>';
                            else if (estado === 'proximo') icono = '<i class="fas fa-exclamation-triangle ms-2 text-warning p-1 rounded"></i>';
                            return `<div class="d-flex align-items-center">${fecha}${icono}</div>`;
                        },
                        orderable: true
                    },

                    {
                        data: null,
                        render: function(data, type, row) {
                            const rowData = encodeURIComponent(JSON.stringify(row));
                            return `
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-warning" onclick="abrirModalEditarProducto('${row.id}')">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(${row.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>`;
                        },
                        orderable: false,
                        searchable: false
                    },
                    { data: 'updated_at', name: 'updated_at', visible: false, orderable: true }
                ],
                columnDefs: [
                    { targets: 7, visible: userHasRole }
                ],
                    createdRow: function(row, data) {
                        // Limpiamos clases anteriores
                        $(row).removeClass('table-danger table-warning table-success');

                        // Obtenemos el estado de caducidad (vencido/proximo/vigente)
                        const estado = data.caducidad.split('|')[1];

                        if (estado === 'vencido') {
                            $(row).addClass('table-danger');   // rojo
                        } else if (estado === 'proximo') {
                            $(row).addClass('table-warning');  // amarillo
                        }
                    }


            });


                window.confirmDelete = function(id) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡Esta acción no se puede deshacer!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, dar de baja',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/productos/bajadefinitiva/${id}`,
                                type: 'POST', // Cambiar a POST
                                data: {
                                    _token: csrfToken,
                                    _method: 'DELETE' // Cambiar a DELETE
                                },
                                success: function(response) {
                                    if (response.success) {
                                        table.ajax.reload();
                                        Swal.fire('¡Éxito!', 'El producto ha sido dado de baja exitosamente.', 'success');
                                    } else {
                                          table.ajax.reload();
                                        // Swal.fire('Error', response.message || 'Error al dar de baja', 'error');
                                    }
                                },
                                error: function(xhr) {
                                    let errorMessage = 'No se pudo dar de baja el producto.';
                                    
                                    // Mostrar más detalles del error
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    } else if (xhr.responseText) {
                                        errorMessage = xhr.responseText;
                                    }
                                    
                                    Swal.fire('Error', errorMessage, 'error');
                                    console.error('Error completo:', xhr);
                                }
                            });
                        }
                    });
                };

            // Carga dinámica de subcategorías
            $('#filtroCategoria').change(function() {
                const categoria = $(this).val();
                const $subcat = $('#filtroSubcategoria');
                if (categoria) {
                    $subcat.prop('disabled', false);
                    $.get('/subcategorias-por-categoria', { categoria }, function(data) {
                        let options = '<option value="">Todas</option>';
                        data.forEach(sub => options += `<option value="${sub.nombre}">${sub.nombre}</option>`);
                        $subcat.html(options);
                    }).fail(() => Swal.fire('Error', 'No se pudieron cargar las subcategorías', 'error'));
                } else {
                    $subcat.prop('disabled', true).html('<option value="">Todas</option>');
                }
            });

            $('#filtroCategoria, #filtroSubcategoria, #filtroEstado, #filtroPrecio, #filtroCaducidad').on('change', function() {
                table.ajax.reload();
            });

            // Función global para resetear filtros
            window.resetFiltros = function() {
                $('.filtro-select').val(null);
                $('.select2').each(function() { $(this).val(null).trigger('change.select2'); });
                $('#filtroSubcategoria').val(null).prop('disabled', true).trigger('change.select2').html('<option value="">Todas</option>');
                table.ajax.reload(null, false);
                Swal.fire('Filtros', 'Los filtros se han reiniciado correctamente', 'info');
            };
        });
    </script>



    @endsection

    @section('css')


        <style>


            .table thead th {
                background-color: #f8f9fa;
                position: sticky;
                top: 0;
                z-index: 2;
                vertical-align: middle;
            }

            .form-select {
                border-radius: 0.375rem;
                padding: 0.375rem 0.75rem;
                border: 1px solid #ced4da;
            }


            /* Estilos personalizados para los filtros */
            .input-group-text {
                min-width: 80px;
                font-size: 0.8rem;
                background-color: #ffffff;
                border: 1px solid #007bff;
                color: #007bff;
                font-weight: 500;
            }




            .table-striped tbody tr:nth-of-type(odd) {
                background-color: rgba(0, 123, 255, 0.03);
            }

            /* Mejoras para móviles */
            @media (max-width: 768px) {
                .card-header {
                    padding: 0.75rem;
                }

                .input-group-text {
                    min-width: 70px;
                    font-size: 0.75rem;
                }

                .card-body {
                    padding: 0.75rem;
                }

                .btn-sm {
                    padding: 0.25rem 0.5rem;
                    font-size: 0.8rem;
                }
            }

            /* Efecto hover para filas de la tabla */
            .table-hover tbody tr:hover {
                background-color: rgba(0, 123, 255, 0.08);
            }
        </style>


        <style>
            .bg-vencido {
                background-color: #ffdddd !important;  /* Rojo claro */
            }

            .bg-proximo-vencer {
                background-color: #fff3cd !important;  /* Amarillo claro */
            }

            /* Para mantener la visibilidad en filas alternas */
            .table-striped tbody tr.bg-vencido:nth-child(odd),
            .table-striped tbody tr.bg-proximo-vencer:nth-child(odd) {
                --bs-table-accent-bg: transparent !important;
            }
        </style>

    @stop







