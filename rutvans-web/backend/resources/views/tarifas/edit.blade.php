<!-- Modal para Editar Tipo de Tarifa -->
<div class="modal fade" id="editTipoTarifaModal" tabindex="-1" aria-labelledby="editTipoTarifaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="editTipoTarifaForm" autocomplete="off">
      @csrf
      @method('PUT')
      <input type="hidden" name="id" id="editTipoTarifaId">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editTipoTarifaModalLabel">Editar Tipo de Tarifa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <!-- Campo: Nombre -->
          <div class="mb-3">
            <label for="editTipoTarifaNombre" class="form-label">Nombre de la Tarifa</label>
            <input type="text" class="form-control" id="editTipoTarifaNombre" name="name" required>
          </div>

          <!-- Campo: Porcentaje -->
          <div class="mb-3">
            <label for="editTipoTarifaPorcentaje" class="form-label">Porcentaje (%)</label>
            <input type="number" class="form-control" id="editTipoTarifaPorcentaje" name="percentage" step="0.01" min="0" max="100" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </div>
    </form>
  </div>
</div>

