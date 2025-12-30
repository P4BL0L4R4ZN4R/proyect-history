<!-- Modal crear producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('productos.store') }}" method="POST">
                @csrf

                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalProductoLabel">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">

                    @if ($errors->any() && session('modal') === 'create')
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
                            <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Stock</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Stock minimo</label>
                            <input type="number" name="minimo_stock" class="form-control" value="{{ old('minimo_stock', 0) }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Código</label>
                            <input type="text" name="codigo" class="form-control" value="{{ old('codigo') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Precio Compra</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="precio_compra" class="form-control" value="{{ old('precio_compra', 0) }}">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Precio Venta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="precio_venta" class="form-control" value="{{ old('precio_venta', 0) }}">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Categoría</label>
                            <div class="d-flex align-items-center">
                                <select name="categoria_id" id="categoria-select-create" class="form-select flex-grow-1 me-2" style="display: block;">
                                    <option value="">Seleccione categoría</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="nueva_categoria" id="categoria-input-create" class="form-control flex-grow-1 me-2" placeholder="Nueva categoría" style="display: none;" value="{{ old('nueva_categoria') }}">
                                    <button type="button" id="btnToggleCategoriaCreate" class="btn btn-success btn-sm" title="Agregar nueva categoría" onclick="toggleCategoria('create')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Subcategoría</label>
                            <div class="d-flex align-items-center">
                                <select name="subcategoria_id" id="subcategoria-select-create" class="form-select flex-grow-1 me-2" style="display: block;">
                                    <option value="">Seleccione subcategoría</option>
                                    @foreach($subcategorias as $sub)
                                        <option value="{{ $sub->id }}" {{ old('subcategoria_id') == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="nueva_subcategoria" id="subcategoria-input-create" class="form-control flex-grow-1 me-2" placeholder="Nueva subcategoría" style="display: none;" value="{{ old('nueva_subcategoria') }}">
                                    <button type="button" id="btnToggleSubcategoriaCreate" class="btn btn-success btn-sm" title="Agregar nueva subcategoría" onclick="toggleSubcategoria('create')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Lote</label>
                            <input type="text" name="lote" class="form-control" value="{{ old('lote') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Caducidad</label>
                            <input type="date" name="caducidad" class="form-control" value="{{ old('caducidad') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Descripción</label>
                            <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion') }}">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
