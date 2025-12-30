<!-- Modal editar producto -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-labelledby="modalEditarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content">
            <form method="POST" id="formEditarProducto">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="modalEditarProductoLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                    @if ($errors->any() && session('modal') === 'edit')
                        <div class="alert alert-danger shadow-sm">
                            <strong>Errores:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Nombre *</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Stock</label>
                            <input type="number" name="stock" class="form-control" >
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Stock minimo</label>
                            <input type="number" name="minimo_stock" class="form-control" >
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Código</label>
                            <input type="text" name="codigo" class="form-control" >
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Precio Compra</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="precio_compra" class="form-control" >
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Precio Venta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="precio_venta" class="form-control" >
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Categoría</label>
                            <div class="d-flex align-items-center">
                                <select name="categoria_id" id="categoria-select-edit" class="form-select flex-grow-1 me-2" style="display: block;">
                                    <option value="">Seleccione categoría</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="nueva_categoria" id="categoria-input-edit" class="form-control flex-grow-1 me-2" placeholder="Nueva categoría" style="display: none;" >
                                <button type="button" id="btnToggleCategoriaEdit" class="btn btn-success btn-sm" title="Agregar nueva categoría" onclick="toggleCategoria('edit')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Subcategoría</label>
                            <div class="d-flex align-items-center">
                                <select name="subcategoria_id" id="subcategoria-select-edit" class="form-select flex-grow-1 me-2" style="display: block;">
                                    <option value="">Seleccione subcategoría</option>
                                    @foreach($subcategorias as $sub)
                                        <option value="{{ $sub->id }}" {{ old('subcategoria_id') == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="nueva_subcategoria" id="subcategoria-input-edit" class="form-control flex-grow-1 me-2" placeholder="Nueva subcategoría" style="display: none;" >
                                <button type="button" id="btnToggleSubcategoriaEdit" class="btn btn-success btn-sm" title="Agregar nueva subcategoría" onclick="toggleSubcategoria('edit')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Lote</label>
                            <input type="text" name="lote" class="form-control" >
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Caducidad</label>
                            <input type="date" name="caducidad" class="form-control" >
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Descripción</label>
                            <input type="text" name="descripcion" class="form-control" >
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

