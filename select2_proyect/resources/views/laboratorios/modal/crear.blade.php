
@yield('crear')





<!-- Modal con pestañas -->
<div class="modal fade" id="modalTabs" tabindex="-1" role="dialog" aria-labelledby="modalTabsLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header bg-info">
        <!-- Título del modal -->
        <h4 class="modal-title">Agregar un nuevo laboratorio</h4>
        <!-- Botón de cierre -->
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        
        <!-- Contenido del modal -->
        <div class="modal-body">
        <!-- Pestañas -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
            <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Laboratorio</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Conexión a base de datos</a>
            </li>
            {{-- Agrega más pestañas según sea necesario --}}
        </ul>
        
        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="myTabContent">
            <!-- Pestaña 1 -->
            <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
            

            <!-- Formulario dentro de la pestaña -->
            <form id="formularioCrear" action="{{ route('laboratorio.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tipo" value="laboratorio">
                <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">


                <label for="nombre">Nombre</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-vial"></i></span>
                        </div>         
                            <input type="text" name="nombre" id="nombre" placeholder="Ingresa el nombre del nuevo laboratorio" class="form-control" value="">
                    </div>


                {{-- <button type="button" onclick="confirmarCrear('formularioCrear')" class="btn btn-primary">Guardar</button> --}}
            </form>
            </div>
            
            <!-- Pestaña 2 -->
            <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
            
                <form id="formularioCrearConexion" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tipo" value="laboratorio">
                    <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                
                    <label for="servidor_sql">Servidor SQL</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-server"></i></span>
                            </div>          
                                <input type="text" name="servidor_sql" id="servidor_sql" placeholder="Ingresa la direccion o IP del servidor" class="form-control" value="">
                        </div>

                    <label for="base_de_datos">Base de datos</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-database"></i></span>
                            </div>       
                                <input type="text" name="base_de_datos" id="base_de_datos" placeholder="Ingresa el nombre de la base de datos" class="form-control" value="">  
                        </div>

                    <label for="usuario_sql">Usuario SQL</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user-cog"></i></span>
                            </div>  
                                <input type="text" name="usuario_sql" id="usuario_sql" placeholder="Ingresa el usuario SQL" class="form-control" value="">
                        </div>

                    <label for="password_sql">Contraseña SQL</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>                                       
                                <input type="password" name="password_sql" id="password_sql" placeholder="Ingresa la contraseña del servidor" class="form-control" value="">
                        </div>
                    




                <div>
                    
                    <button type="button" title="Probar la conexion" class="comic-button-success-md probarConexion"> <i class="fas fa-exchange-alt"></i> Probar Conexión </button>
                </div>
                </form>
                
            </div>
            
            {{-- Agrega más pestañas según sea necesario --}}
            
        </div>
        </div>
        
        <!-- Pie del modal -->
        <div class="modal-footer">
            <button type="button" title="Guarda el laboratorio junto con su conexion" onclick="guardarDatos()" class="comic-button-primary-md"> <i class="fas fa-save"></i> Guardar</button>
            
            {{-- <button type="button" onclick="confirmarCrear('formularioCrear')" class="btn btn-primary">Guardar</button> --}}
        <button type="button" class="comic-button-secondary-md" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
    </div>
</div>





