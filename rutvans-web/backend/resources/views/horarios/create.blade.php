<div class="modal fade" id="createHorarioModal" tabindex="-1" aria-labelledby="createHorarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg animate__animated animate__fadeInDown">
        <form id="createHorarioForm" action="{{ route('horarios.store') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="modal-content shadow-lg rounded-4 border-0">
                <div class="modal-header bg-gradient bg-primary text-white rounded-top-4 px-4 py-3">
                    <h5 class="modal-title d-flex align-items-center" id="createHorarioModalLabel">
                        <i class="fas fa-clock me-2"></i> Crear Horario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-4">
                        <label for="horaLlegada" class="form-label">Hora de Llegada</label>
                        <input type="time" class="form-control form-control-lg rounded-3" id="horaLlegada" name="horaLlegada" required>
                        <div class="invalid-feedback">Este campo es obligatorio.</div>
                    </div>
                    <div class="mb-4">
                        <label for="horaSalida" class="form-label">Hora de Salida</label>
                        <input type="time" class="form-control form-control-lg rounded-3" id="horaSalida" name="horaSalida" required>
                        <div class="invalid-feedback">Este campo es obligatorio.</div>
                    </div>
                    <div class="mb-4">
                        <label for="dia" class="form-label">Día</label>
                        <select class="form-select form-select-lg rounded-3" id="dia" name="dia" required>
                            <option value="" disabled selected>Seleccione un día</option>
                            <option>Lunes</option>
                            <option>Martes</option>
                            <option>Miércoles</option>
                            <option>Jueves</option>
                            <option>Viernes</option>
                            <option>Sábado</option>
                            <option>Domingo</option>
                        </select>
                        <div class="invalid-feedback">Seleccione un día.</div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> Guardar Horario
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
