<form action="{{ route('localidades.store') }}" method="POST">
    @csrf

    <div class="row">
        <!-- Campos ocultos para coordenadas -->
        <input type="hidden" id="longitude" name="longitude" required>
        <input type="hidden" id="latitude" name="latitude" required>
        
        <!-- Campo visible para la localidad/municipio -->
        <div class="col-md-12 mb-3">
            <label for="locality_display" class="form-label fw-bold">
                <i class="fas fa-map-marker-alt text-primary"></i> Ubicación Seleccionada:
            </label>
            <input type="text" 
                   id="locality_display" 
                   class="form-control form-control-lg text-center" 
                   readonly 
                   placeholder="Haz clic en el mapa para seleccionar una ubicación"
                   style="background-color: #f8f9fa; border: 2px solid #e9ecef; font-weight: 600; color: #495057;">
        </div>

        <!-- Campos ocultos para enviar todos los datos -->
        <input type="hidden" id="locality" name="locality">
        <input type="hidden" id="street" name="street">
        <input type="hidden" id="postal_code" name="postal_code">
        <input type="hidden" id="municipality" name="municipality">
        <input type="hidden" id="state" name="state">
        <input type="hidden" id="country" name="country">
        <input type="hidden" id="locality_type" name="locality_type">


        <!-- Botón Guardar -->
        <div class="col-md-12 mt-3">
            <button type="submit" class="btn btn-primary w-100">Guardar Ubicación</button>
        </div>
    </div>
</form>
