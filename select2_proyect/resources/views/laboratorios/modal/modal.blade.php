
<!-- Incluye el CSRF token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

@yield('modal')


    <div class="modal fade" id="editarModal" tabindex="-1" role="dialog" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formularioActualizar">
                    @csrf
                    @method('PUT')
                    <div class="modal-header" style="text-align: left">
                        <h5 class="modal-title" id="editarModalLabel">Editar Laboratorio</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


