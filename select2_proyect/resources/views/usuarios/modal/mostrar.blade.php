


@yield('mostrarUser')

        @foreach ($users as $user)


    <form id="formularioActualizar{{ $user->id }}" action="{{ route('usuario.update', [$user->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
    <div class="modal fade" id="mostrarModal{{ $user->id }}" tabindex="-1" aria-labelledby="mostrarModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="mostrarModalLabel{{ $user->id }}" >Mostrar usuario</h5>
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
                        <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" readonly>
                    </div>

                    <label for="email">Correo</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                    </div>                                       
                        <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" readonly>
                    </div>

                    <label for="password">Contraseña</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>                                       
                        <input type="password" name="password" id="password" class="form-control" value="" readonly>
                    </div>
                   
                </div>
                     </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

                </div>
                </form>
            </div>
        </div>
    </div>

@endforeach



