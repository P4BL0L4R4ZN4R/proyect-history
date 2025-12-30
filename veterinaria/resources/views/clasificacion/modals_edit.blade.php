
<div class="modal fade" id="modalEditarCategoria" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Editar Categoría</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditarCategoria">
                    <input type="hidden" id="categoriaId">
                    <div class="form-group">
                        <label for="categoriaNombre">Nombre</label>
                        <input type="text" class="form-control" id="categoriaNombre" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCategoria">Guardar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEditarSubcategoria" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Editar Subcategoría</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditarSubcategoria">
                    <input type="hidden" id="subcategoriaId">
                    <div class="form-group">
                        <label for="subcategoriaNombre">Nombre</label>
                        <input type="text" class="form-control" id="subcategoriaNombre" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarSubcategoria">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // EDITAR CATEGORÍA
        $(document).on('click', '.btn-editar-categoria', function() {
            let rowData = $('#tablaCategorias').DataTable().row($(this).parents('tr')).data();

            if (!rowData) {
                console.error("No se encontró data de la fila seleccionada");
                return;
            }

            $('#categoriaId').val(rowData.id);
            $('#categoriaNombre').val(rowData.nombre);
            $('#modalEditarCategoria').modal('show');
        });

        // GUARDAR CAMBIOS DE CATEGORÍA
        $('#btnGuardarCategoria').on('click', function() {
            const id = $('#categoriaId').val();
            const nombre = $('#categoriaNombre').val();

            if (!nombre) {
                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención!',
                    text: 'Por favor, ingrese un nombre para la categoría'
                });
                return;
            }

            // Confirmación antes de guardar
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se actualizará la categoría",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/categorias/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ nombre: nombre })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Actualizado!',
                                text: 'Categoría actualizada con éxito',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#modalEditarCategoria').modal('hide');
                            $('#tablaCategorias').DataTable().ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al actualizar: ' + data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error en actualización categoría:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al actualizar la categoría'
                        });
                    });
                }
            });
        });

        // EDITAR SUBCATEGORÍA
        $(document).on('click', '.btn-editar-subcategoria', function() {
            let rowData = $('#tablaSubcategorias').DataTable().row($(this).parents('tr')).data();

            if (!rowData) {
                console.error("No se encontró data de la fila seleccionada");
                return;
            }

            $('#subcategoriaId').val(rowData.id);
            $('#subcategoriaNombre').val(rowData.nombre);
            $('#modalEditarSubcategoria').modal('show');
        });

        // GUARDAR CAMBIOS DE SUBCATEGORÍA
        $('#btnGuardarSubcategoria').on('click', function() {
            const id = $('#subcategoriaId').val();
            const nombre = $('#subcategoriaNombre').val();

            if (!nombre) {
                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención!',
                    text: 'Por favor, ingrese un nombre para la subcategoría'
                });
                return;
            }

            // Confirmación antes de guardar
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se actualizará la subcategoría",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/subcategorias/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ nombre: nombre })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Actualizado!',
                                text: 'Subcategoría actualizada con éxito',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#modalEditarSubcategoria').modal('hide');
                            $('#tablaSubcategorias').DataTable().ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al actualizar: ' + data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error en actualización subcategoría:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al actualizar la subcategoría'
                        });
                    });
                }
            });
        });

    });
</script>
