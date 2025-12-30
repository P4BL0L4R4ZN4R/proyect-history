<div class="modal fade" id="modalCrearCategoria" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Crear Categoría</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCrearCategoria">
                    <div class="form-group">
                        <label for="categoriaNombreNueva">Nombre</label>
                        <input type="text" class="form-control" id="categoriaNombreNueva" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnCrearCategoria">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCrearSubcategoria" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Crear Subcategoría</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCrearSubcategoria">
                    <div class="form-group">
                        <label for="subcategoriaNombreNueva">Nombre</label>
                        <input type="text" class="form-control" id="subcategoriaNombreNueva" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnCrearSubcategoria">Guardar</button>
            </div>
        </div>
    </div>
</div>


<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function enviarDatos(url, datos, modalId, tablaId) {
        console.log("📤 Enviando datos a:", url, datos);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            console.log("📥 Respuesta bruta:", response);
            if (!response.ok) {
                throw new Error('Error en la solicitud: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log("✅ JSON recibido:", data);
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Creado!',
                    text: 'Registro creado con éxito',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Cerrar modal
                if (modalId) {
                    document.querySelector(`${modalId} [data-bs-dismiss="modal"]`).click();
                }

                // Recargar tabla
                if (tablaId) {
                    $(tablaId).DataTable().ajax.reload();
                }

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al crear el registro: ' + data.message
                });
            }
        })
        .catch(error => {
            console.error('❌ Error en fetch:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al crear el registro, revisa la consola para más detalles'
            });
        });
    }

    // Crear categoría
    document.getElementById('btnCrearCategoria').addEventListener('click', function() {
        const nombre = document.getElementById('categoriaNombreNueva').value;
        if (!nombre) {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Por favor, ingrese un nombre para la categoría'
            });
            return;
        }

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se creará una nueva categoría",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, crear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviarDatos(
                    '{{ route("clasificacion.categorias.store") }}',
                    { nombre: nombre },
                    '#modalCrearCategoria',
                    '#tablaCategorias' // recarga tabla categorías
                );
            }
        });
    });

    // Crear subcategoría
    document.getElementById('btnCrearSubcategoria').addEventListener('click', function() {
        const nombre = document.getElementById('subcategoriaNombreNueva').value;
        if (!nombre) {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Por favor, ingrese un nombre para la subcategoría'
            });
            return;
        }

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se creará una nueva subcategoría",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, crear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviarDatos(
                    '{{ route("clasificacion.subcategorias.store") }}',
                    { nombre: nombre },
                    '#modalCrearSubcategoria',
                    '#tablaSubcategorias' // recarga tabla subcategorías
                );
            }
        });
    });

});
</script>
