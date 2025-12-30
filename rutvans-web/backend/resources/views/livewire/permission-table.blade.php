<div>
    <br>
    <button wire:click="crear" class="btn btn-success mb-3"> ➕ Nuevo Permiso </button>

    <input type="text" wire:model="search" placeholder="Buscar permiso..." class="form-control mb-3">

    {{-- {{ $permisos->links() }} --}}
    {{ $permisos->links('pagination::bootstrap-5') }}

    <table class="table table-striped">
        <thead>
            <tr>
                <th> ID </th>
                <th> Permiso </th>
                <th> Acciones </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permisos as $permiso)
                <tr>
                    <td> {{ $permiso->id }} </td>
                    <td> {{ $permiso->name }} </td>
                    <td>
                        <button wire:click="editar({{ $permiso->id }})" class="btn btn-primary btn-sm"> ✏️ Editar </button>
                        <button wire:click="eliminar({{ $permiso->id }})" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este permiso?')"> 🗑 Eliminar </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal -->
    @if ($modal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> {{ $id ? 'Editar Permiso' : 'Crear Permiso' }} </h5>
                        <button type="button" class="close" wire:click="cerrarModal"> × </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" wire:model.defer="nombre" class="form-control" placeholder="Nombre del permiso">
                        @error('nombre') <span class="text-danger"> {{ $message }} </span> @enderror
                    </div>
                    <div class="modal-footer">
                        <button wire:click="cerrarModal" class="btn btn-secondary"> Cancelar </button>
                        <button type="button" class="btn btn-primary" wire:click="guardar()">
                            {{ $id ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
