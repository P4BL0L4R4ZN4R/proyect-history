<div class="modal fade" id="editHorarioModal" tabindex="-1" aria-labelledby="editHorarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg animate__animated animate__fadeInDown">
        <form id="editHorarioForm" method="POST" class="needs-validation" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" id="editHorarioId" name="id">
            <div class="modal-content shadow-lg rounded-4 border-0">
                <div class="modal-header bg-primary text-white rounded-top-4 px-4 py-3">
                    <h5 class="modal-title d-flex align-items-center" id="editHorarioModalLabel">
                        <i class="fas fa-edit me-2"></i> Editar Horario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-4">
                        <label for="editHoraLlegada" class="form-label">Hora de Llegada</label>
                        <input type="time" class="form-control form-control-lg rounded-3" id="editHoraLlegada" name="horaLlegada" required>
                        <div class="invalid-feedback">Este campo es obligatorio.</div>
                    </div>
                    <div class="mb-4">
                        <label for="editHoraSalida" class="form-label">Hora de Salida</label>
                        <input type="time" class="form-control form-control-lg rounded-3" id="editHoraSalida" name="horaSalida" required>
                        <div class="invalid-feedback">Este campo es obligatorio.</div>
                    </div>
                    <div class="mb-4">
                        <label for="editDia" class="form-label">Día</label>
                        <select class="form-select form-select-lg rounded-3" id="editDia" name="dia" required>
                            <option value="" disabled selected>Selecciona un día</option>
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sábado">Sábado</option>
                            <option value="Domingo">Domingo</option>
                        </select>
                        <div class="invalid-feedback">Selecciona un día válido.</div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
