@yield('config')



<!-- Modal de Edición -->
<div class="modal fade" id="configurarModal" tabindex="-1" role="dialog" aria-labelledby="configurarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="configurarModalLabel">Editar Conexion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align: left">
                <form id="ConfigForm"> 
                    <input type="hidden" id="laboratorio_id" name="laboratorio_id">
                    <input type="hidden" id="nombreLab" name="nombreLab">
                    <input type="hidden" id="usuario" name="usuario" value="{{ Auth::user()->name }}">
                    
                    <label for="Editservidor_sql">Servidor:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-server"></i></span>
                        </div>                                       
                        <input type="text" class="form-control" id="Editservidor_sql" name="Editservidor_sql" placeholder="Servidor SQL">
                    </div>

                    <label for="Editbase_de_datos">Base de datos:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-database"></i></span>
                        </div>                                       
                        <input type="text" class="form-control" id="Editbase_de_datos" name="Editbase_de_datos" placeholder="Nombre de la base de datos">
                    </div>

                    <label for="Editusuario_sql">Usuario SQL:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user-cog"></i></span>
                        </div>  
                        <input type="text" class="form-control" id="Editusuario_sql" name="Editusuario_sql" placeholder="Usuario SQL">
                    </div>

                    <label for="Editcontrasena_sql">Contraseña SQL:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="text" class="form-control" id="Editcontrasena_sql" name="Editcontrasena_sql" placeholder="Contraseña SQL">
                    </div>
                </form> <!-- Aquí se cierra el formulario -->
            </div>
            <div class="modal-footer">
                <button type="button" title="Probar la conexion" class="comic-button-success-md testearConexion"> <i class="fas fa-exchange-alt"></i> Probar Conexión</button>
                <button type="button" title="Guardar la conexion" class="comic-button-primary-md" id="saveEditBtn" onclick="actualizarLaConexion()"> <i class="fas fa-save"></i> Guardar Cambios</button>
                <button type="button" class="comic-button-secondary-md" data-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

