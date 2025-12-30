@extends('adminlte::page')

@section('title', ' Localidades')

@section('content_header')
    <h1>Gestión de Localidades</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Administrar Localidades</h3>
        </div>

        <div class="card-body">
            @livewire('localidad-component')  <!-- Carga el componente de gestión de localidades -->
            @livewire('localidades-table')    <!-- Carga la tabla con las localidades -->
        </div>
    </div>

    <!-- Carga los estilos y scripts de rappasoft/laravel-livewire-tables -->
    @rappasoftTableStyles
    @rappasoftTableThirdPartyStyles
    @rappasoftTableScripts
    @rappasoftTableThirdPartyScripts
@endsection

@section('js')
    <!-- Cargar SweetAlert2 (asegúrate de incluir este script antes que cualquier otro script que lo utilice) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let modalElement = document.getElementById('localidadModal');
            let localidadModal = new bootstrap.Modal(modalElement);

            Livewire.on('show-bootstrap-modal', function () {
                localidadModal.show();  // Abre el modal
            });

            Livewire.on('hide-bootstrap-modal', function () {
                localidadModal.hide();  // Cierra el modal
            });

            // Evento para mostrar detalles de la localidad
            Livewire.on('openShowLocalidadModal', function (localidad) {
                // Llenar los campos con los datos de la localidad
                document.getElementById('detalleNombre').innerText = localidad.nombre;
                document.getElementById('detalleDescripcion').innerText = localidad.descripcion;

                let localidadModal = new bootstrap.Modal(document.getElementById('localidadModal'));
                localidadModal.show();  // Muestra el modal con los detalles
            });
        });
    </script>

    <!-- Este script escucha el mensaje de éxito enviado desde Livewire -->
    <script>
        Livewire.on('message', message => {
            Swal.fire({
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 1500
            });
        });
    </script>

    <!-- Este script escucha los errores enviados desde Livewire -->
    <script>
        Livewire.on('error', errorMessage => {
            Swal.fire({
                icon: 'error',
                title: errorMessage,
                showConfirmButton: true
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.on('swalConfirmDelete', (data) => {
                let id = Array.isArray(data) ? data[0] : data.id; // Extraer correctamente

                if (!id) {
                    return;
                }

                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "No podrás revertir esto.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33", 
                    cancelButtonColor: "#000", 
                    confirmButtonText: "Sí, eliminar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteLocalidad', id);
                    }
                });
            });

            Livewire.on('swalSuccess', () => {
                Swal.fire({
                    title: "¡Eliminado!",
                    text: "La localidad ha sido eliminada.",
                    icon: "success",
                    confirmButtonColor: "#d33", 
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Escuchar el evento swalConfirm para mostrar el SweetAlert de confirmación
            Livewire.on('swalConfirmSave', (data) => {
                const isEditMode = data.isEditMode;  // Obtenemos si estamos en modo editar

                let mensaje = isEditMode ? "¿Deseas guardar los cambios?" : "¿Deseas crear esta localidad?";

                Swal.fire({
                    title: mensaje,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#d33", 
                    cancelButtonColor: "#000", 
                    confirmButtonText: "Sí, guardar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disparar el evento de guardado
                        Livewire.dispatch('saveLocalidad'); // Usamos dispatch para llamar al método 'save'
                    }
                });
            });

            // Evento de éxito después de guardar la localidad
            Livewire.on('swalSuccessSave', () => {
                Swal.fire({
                    title: "¡Guardado!",
                    text: "La localidad se ha guardado correctamente.",
                    icon: "success",
                    confirmButtonColor: "#d33", 
                });
            });
        });
    </script>
@endsection
