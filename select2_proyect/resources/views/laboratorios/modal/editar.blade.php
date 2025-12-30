@yield('editar')




<!-- Modal de Edición -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="editModalLabel">Editar Laboratorio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align: left">
                <form id="formularioActualizar" action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                   
                    <input type="hidden" id="idlaboratorio" name="idlaboratorio">
                    <input type="hidden" id="usuario" name="usuario" value="{{ Auth::user()->name }}">

                    <label for="editNombre">Nombre:</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-vial"></i></span>
                            </div>
                                <input type="text" class="form-control" id="editNombre" name="editNombre">         
                        </div>

                        <label for="editNotas">Notas:</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-notes-medical"></i></span>
                            </div>
                            <textarea type="text" class="form-control" id="editNotas" name="editNotas"></textarea>
                        </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="comic-button-primary-md" onclick="actualizarLaboratorio()"> <i class="fas fa-save"></i> Guardar Cambios</button>
                <button type="button" class="comic-button-secondary-md" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
