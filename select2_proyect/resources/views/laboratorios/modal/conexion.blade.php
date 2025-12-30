

        
        
        
        <div class="modal fade" id="VerModal" tabindex="-1" aria-labelledby="VerModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title" id="VerModalLabel">Ver datos del laboratorio</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div style="text-align:left">
                            <input type="hidden" name="tipo" value="laboratorio">
                            <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                            
                            <label for="VerNombre">Nombre:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-vial"></i></span>
                                </div>
                                    <input type="text" class="form-control" id="VerNombre" name="VerNombre" readonly>         
                            </div>
    
                            <label for="VerNotas">Notas:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-notes-medical"></i></span>
                                </div>
                                <textarea type="text" class="form-control" id="VerNotas" name="VerNotas" readonly></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="comic-button-secondary-md" data-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>


