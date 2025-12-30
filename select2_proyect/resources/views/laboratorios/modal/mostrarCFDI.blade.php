
@yield('mostrarCFDI')




<div class="modal fade" id="mostrarModalCFDI" tabindex="-1" role="dialog" aria-labelledby="mostrarModalCFDILabel" aria-hidden="true" style="text-align:left!important;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="mostrarModalCFDILabel">Mostrar datos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align: left">
                <form id="formularioVerCFDI">

                <label for="VerSucursal">Sucursal:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                            <input type="text" class="form-control" id="VerSucursal" name="VerSucursal" readonly>
                    </div>

                <label for="VerVersion">Version:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-laptop"></i></span>
                        </div>
                            <input type="text" class="form-control" id="VerVersion" name="VerVersion" readonly>
                    </div>

                <label for="VerTimesession">Tiempo de sesion:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                        </div>
                            <input type="number" class="form-control" id="VerTimesession" name="VerTimesession" readonly>
                    </div>


                <label for="VerMesMax">Limite de ordenes mensuales:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-minus"></i></span>
                        </div>
                            <input type="number" class="form-control" id="VerMesMax" name="VerMesMax" readonly>
                    </div>

                <label for="Versesioneslimite">Sesiones limite:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-laptop-medical"></i></span>
                        </div>
                            <input type="number" class="form-control" id="Versesioneslimite" name="Versesioneslimite" readonly>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="comic-button-secondary-md" data-dismiss="modal">Cerrar</button>
                
            </div>
        </div>
    </div>
</div>
