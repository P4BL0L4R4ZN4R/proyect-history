


@yield('crearUser')




    <form id="formularioCrear" action="{{ route('usuario.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

    <div class="modal fade" id="crearModal" tabindex="-1" aria-labelledby="crearModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="crearModalLabel" >Registrar nuevo Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div style="text-align:left">
                <div class="modal-body">

                        <label for="name">Nombre</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>                                       
                            <input type="text" name="name" id="name" class="form-control" placeholder="Inserta un nombre de usuario" value="" >
                        </div>

                        <label for="email">Correo</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-at"></i></span>
                            </div>                                       
                                <input type="email" name="email" id="email" class="form-control" placeholder="Inserta un correo valido" value="" >
                            </div>

                        <div style="text-align: center" class="form-group" style="color: black">
                            <label>¿Cambiar contraseña?</label>
                        </div>

                        <label for="password">Contraseña</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-unlock"></i></span>
                            </div>                                       
                            <input type="password" name="password" id="password" class="form-control" placeholder="Ingresa una contraseña" value="" >
                        </div>

                        <label for="confirm_password">Confirmar contraseña</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>                                       
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirma la contraseña" value="" >
                        </div>


                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" onclick="confirmarCrearUsuario('formularioCrear')" class="btn btn-primary">Guardar</button>
                </div>
                </form>
            </div>
        </div>
    </div>





