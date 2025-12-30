



<!-- Modal de Edición -->
<div class="modal fade" id="consumos" tabindex="-1" role="dialog" aria-labelledby="consumosLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="consumosLabel">Consultar ordenes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align: left">
                <form id="ConsultaConsumos" action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                   
                    <input type="hidden" id="idlaboratorio" name="idlaboratorio">
                    <input type="hidden" id="idSucursal" name="idSucursal">
                    <input type="hidden" id="usuario" name="usuario" value="{{ Auth::user()->name }}">

                    <label for="LaboratorioNombre">Laboratorio:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                            <input type="text"  class="form-control" id="LaboratorioNombre" name="LaboratorioNombre" readonly> 

                        </div>


                            <label for="SucursalNombre">Sucursal:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                    <input type="text"  class="form-control" id="SucursalNombre" name="SucursalNombre"  readonly> 
                                </div>

                        <div class="row">

                            <div class="col-lg-6">
                                
                                <label for="fechaInicio">Fecha de inicio:</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                        <input type="date"  class="form-control" id="fechaInicio" name="fechaInicio" >         
                                </div>
        
                                
                            </div>
                            
                            <div class="col-lg-6">
                                
                                
                                <label for="fechaFin">Fecha final:</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                        <input type="date" class="form-control" id="fechaFin" name="fechaFin">         
                                </div>
                            
                            </div>
                            
                        </div>


                </form>




                <div class="col-5" >
                <label for="ConsumosTotales" style="text-align: center">Consumos totales:</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-poll"></i></span>
                    </div>
                        <input type="text" class="form-control" id="ConsumosTotales" name="ConsumosTotales" readonly>         
                </div>

            </div>

       
            </div>
            <div class="modal-footer">
                
                <button type="button" class="comic-button-success-md consultarOrdenes" id="consultarOrdenes" name="consultarOrdenes">
                    <i class="fas fa-search"></i> Consultar 
                </button>
                <button type="button" class="comic-button-secondary-md" data-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>



