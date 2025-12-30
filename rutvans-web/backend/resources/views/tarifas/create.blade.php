<!-- Modal para Crear Tipo de Tarifa -->
<div class="modal fade" id="createTipoTarifaModal" tabindex="-1" aria-labelledby="createTipoTarifaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('tarifas.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createTipoTarifaModalLabel">Crear Tipo de Tarifa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <!-- Campo: Nombre -->
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Tarifa</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>

          <!-- Campo: Porcentaje -->
          <div class="mb-3">
            <label for="porcentaje" class="form-label">Porcentaje (%)</label>
            <input type="number" class="form-control" id="porcentaje" name="porcentaje" step="0.01" min="0" max="100" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>

