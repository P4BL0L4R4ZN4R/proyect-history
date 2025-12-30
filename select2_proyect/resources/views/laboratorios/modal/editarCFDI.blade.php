@yield('editarCFDI')

<div class="modal fade" id="editarModalCFDI" tabindex="-1" role="dialog" aria-labelledby="editarModalCFDILabel" aria-modal="true" style="text-align:left!important;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="editarModalCFDILabel">Editar Laboratorio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align: left">
                <form id="formularioActualizarCFDI">
                    <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                    <input type="hidden" id="CFDIid" name="CFDIid">
                    <input type="hidden" id="idSucursal" name="idSucursal">
                    <input type="hidden" id="idLab" name="idLab">
                
                <label for="editSucursal">Sucursal:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                            <input type="text" class="form-control" id="editSucursal" name="editSucursal">
                    </div>

                <label for="editVersion">Version:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-laptop"></i></span>
                        </div>
                            <input type="text" class="form-control" id="editVersion" name="editVersion">     
                    </div>

                <label for="editTimesession">Tiempo de sesion:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                        </div>
                            <input type="number" class="form-control" id="editTimesession" name="editTimesession">        
                    </div>

                <label for="editMesMax">Limite de ordenes mensuales:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-minus"></i></span>
                        </div>
                            <input type="number" class="form-control" id="editMesMax" name="editMesMax">         
                    </div>

                <label for="editsesioneslimite">Sesiones limite:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-laptop-medical"></i></span>
                        </div>
                            <input type="number" class="form-control" id="editsesioneslimite" name="editsesioneslimite">
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="comic-button-primary-md" onclick="ConfirmarActualizarCFDI()"> <i class="fas fa-save"></i> Guardar Cambios</button>
                <button type="button" class="comic-button-secondary-md" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>