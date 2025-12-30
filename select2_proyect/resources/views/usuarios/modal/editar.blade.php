@yield('editarUser')

    @foreach ($users as $user)
        <form id="formularioActualizar{{ $user->id }}" action="{{ route('usuario.update', [$user->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal fade" id="editarModal{{ $user->id }}" tabindex="-1" aria-labelledby="editarModalLabel{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info">
                            <h5 class="modal-title " id="editarModalLabel{{ $user->id }}" >Editar usuario</h5>
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
                                    <input type="text" name="name" id="name{{ $user->id }}" placeholder="Nombre de usuario" class="form-control" value="{{ $user->name }}" required>
                                </div>

                                <label for="email">Correo</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                                    </div>                                       
                                        <input type="email" name="email" placeholder="Correo" id="email{{ $user->id }}" class="form-control" value="{{ $user->email }}" required>
                                    </div>

                                <div style="text-align: center" class="form-group" style="color: black">
                                    <label>¿Cambiar contraseña?</label>
                                </div>


                                <label for="password" >Nueva Contraseña</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-unlock"></i></span>
                                    </div>                                       
                                        <input type="password" name="password" id="password{{ $user->id }}" placeholder="Ingresa una contraseña" class="form-control" required>
                                </div>

                                <label for="confirm_password">Confirmar contraseña</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>                                       
                                    <input type="password" name="confirm_password" id="confirm_password{{ $user->id }}" placeholder="Confirma la contraseña" class="form-control" required>
                                </div>
                                

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" onclick="confirmarActualizarUsuario('formularioActualizar{{ $user->id }}', '{{ $user->id }}')" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endforeach
