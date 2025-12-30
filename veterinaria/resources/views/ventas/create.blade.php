@extends('adminlte::page')

@section('title', 'Registrar Venta')

@section('content_header')
    <h1 class="mb-4">Registrar Nueva Venta</h1>
@stop

@section('content')
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form id="formVenta" method="POST" action="{{ route('ventas.store') }}">
                @csrf
                <input type="hidden" name="usuario_id" value="{{ Auth::user()?->id }}" id="usuario_id">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="producto_id" class="form-label">Producto</label>
                        <select name="producto_id" id="producto_id" class="form-control select2">
                            <option value="">Selecciona un producto</option>
                            @foreach($productos as $producto)
                                <option data-precio_venta="{{ $producto->precio_venta }}" value="{{ $producto->id }}">
                                    {{ $producto->nombre }} - ${{ number_format($producto->precio_venta, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad" value="1" min="1" class="form-control">
                    </div>




                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="agregarProducto" class="btn btn-primary w-100">
                            <i class="fas fa-plus-circle"></i> Agregar Producto
                        </button>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped" id="tablaProductosSeleccionados">
                        <thead class="thead-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Productos dinámicos --}}
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total:</th>
                                <th id="totalVenta" class="text-success">$0.00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="tipo_pago" class="form-label">Tipo de Pago</label>
                        <select name="tipo_pago" id="tipo_pago" class="form-control" required>
                            <option value="">Selecciona tipo de pago</option>
                            <option value="1" selected>Efectivo</option>
                            <option value="2">Tarjeta</option>
                            <option value="3">Transferencia</option>
                        </select>
                    </div>


                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="imprimirTicket" name="imprimir_ticket" value="1"
                                {{ $imprimirTicket ? 'checked' : '' }}>
                            <label class="form-check-label" for="imprimirTicket">Imprimir Ticket</label>
                        </div>
                    </div>

                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check-circle"></i> Finalizar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

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

                        console.log('Intentando cargar:', sources[index]);
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

    {{-- Procesos --}}

    <script>

            document.addEventListener('DOMContentLoaded', function () {
            const productos = @json($productos);
            const imprimirTicket = @json($imprimirTicket); // ← Cambiado a imprimirTicket

            const formVenta = document.getElementById('formVenta');
            let carrito = [];

            const productoSelect = document.getElementById('producto_id');
            const cantidadInput = document.getElementById('cantidad');
            const tablaBody = document.querySelector('#tablaProductosSeleccionados tbody');
            const totalVenta = document.getElementById('totalVenta');
            const imprimirTicketCheckbox = document.getElementById('imprimirTicket');

            // Función para obtener el estado actual del checkbox
            function obtenerEstadoImprimirTicket() {
                return imprimirTicketCheckbox.checked;
            }

            function actualizarTabla() {
                tablaBody.innerHTML = '';
                let total = 0;

                carrito.forEach((item, index) => {
                    const subtotal = item.precio_venta * item.cantidad;
                    total += subtotal;

                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td>${item.nombre}</td>
                        <td>$${item.precio_venta.toFixed(2)}</td>
                        <td>
                            <input type="number" class="form-control input-cantidad-tabla" value="${item.cantidad}" min="1" data-index="${index}">
                        </td>
                        <td>$${subtotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tablaBody.appendChild(fila);
                });

                totalVenta.textContent = `$${total.toFixed(2)}`;
            }

            // Agregar productos, eliminar, cambiar cantidad (igual que antes)
            document.getElementById('agregarProducto').addEventListener('click', () => {
                const productoId = parseInt(productoSelect.value);
                const cantidad = parseInt(cantidadInput.value);

                if (!productoId) {
                    Swal.fire({ icon: 'warning', title: 'Atención', text: 'Selecciona un producto primero.' });
                    return;
                }
                if (cantidad < 1) {
                    Swal.fire({ icon: 'warning', title: 'Cantidad inválida', text: 'La cantidad debe ser al menos 1.' });
                    return;
                }

                const producto = productos.find(p => p.id === productoId);
                if (!producto) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Producto no válido.' });
                    return;
                }

                const index = carrito.findIndex(item => item.id === productoId);
                if (index > -1) {
                    carrito[index].cantidad += cantidad;
                } else {
                    carrito.push({
                        id: producto.id,
                        nombre: producto.nombre,
                        precio_venta: parseFloat(producto.precio_venta),
                        cantidad: cantidad
                    });
                }

                productoSelect.value = '';
                $(productoSelect).trigger('change');
                cantidadInput.value = 1;

                actualizarTabla();
            });

            tablaBody.addEventListener('click', function(e) {
                if (e.target.closest('.btn-eliminar')) {
                    const index = parseInt(e.target.closest('.btn-eliminar').dataset.index);
                    Swal.fire({
                        title: '¿Eliminar producto?',
                        text: "Este producto se quitará de la venta.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            carrito.splice(index, 1);
                            actualizarTabla();
                            Swal.fire('Eliminado', 'El producto fue eliminado.', 'success');
                        }
                    });
                }
            });

            tablaBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('input-cantidad-tabla')) {
                    const index = parseInt(e.target.dataset.index);
                    let nuevaCantidad = parseInt(e.target.value);
                    if (isNaN(nuevaCantidad) || nuevaCantidad < 1) {
                        nuevaCantidad = 1;
                        e.target.value = nuevaCantidad;
                    }
                    carrito[index].cantidad = nuevaCantidad;
                    actualizarTabla();
                }
            });

            // Enviar formulario
            formVenta.addEventListener('submit', function(e) {
                e.preventDefault();

                if (carrito.length === 0) {
                    Swal.fire({ icon: 'error', title: 'Carrito vacío', text: 'Agrega al menos un producto antes de finalizar la venta.' });
                    return;
                }

                Swal.fire({
                    title: '¿Finalizar venta?',
                    text: "Se registrará la venta con los productos seleccionados.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, finalizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Usamos la función para obtener el estado actual
                        const imprimirTicketActual = obtenerEstadoImprimirTicket();

                        console.log('usuario_id:', document.getElementById('usuario_id').value);
                        const payload = {
                            usuario_id: document.getElementById('usuario_id').value,
                            tipo_pago: document.getElementById('tipo_pago').value,
                            imprimir_ticket: imprimirTicketActual ? 1 : 0,
                            productos_json: JSON.stringify(carrito)
                        };

                        console.log('Estado del checkbox:', imprimirTicketActual);
                        console.log('Payload a enviar:', payload);

                        fetch(formVenta.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: data.message
                                }).then(() => {
                                    carrito = [];
                                    actualizarTabla();
                                    formVenta.reset();

                                    // Restaurar el checkbox a su estado por defecto
                                    imprimirTicketCheckbox.checked = imprimirTicket;

                                    $(productoSelect).trigger('change');
                                });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Ocurrió un error inesperado.' });
                            }
                        })
                        .catch(error => {
                            Swal.fire({ icon: 'error', title: 'Error crítico', text: 'Error al procesar la venta: ' + error });
                        });
                    }
                });
            });
        });
    </script>






@stop
