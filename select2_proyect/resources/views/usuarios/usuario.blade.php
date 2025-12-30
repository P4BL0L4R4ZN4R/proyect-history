@include('layouts.plantilla')

@extends('adminlte::page')

@section('plugins.Sweetalert2', true)

@section('plugins.Datatables', true)

@section('title', 'PanelLab | Administrador de laboratorios')

@section('content_header')

@endsection
@section('content')

<h1>Usuarios</h1> 

@include('usuarios.modal.crear')
    <!-- Botón para abrir el modal de creación -->
    
    <div class="d-flex justify-content-end align-items-center gap-3" style="margin-right: 10px; margin-bottom:10px">
    {{-- <div class="text-right"> --}}
        <button type="button" class="btn btn-info"
        data-toggle="modal" data-target="#crearModal" title="Agregar nuevo registro" style="font-family: Arial, Helvetica, sans-serif">
        <i class="fas fa-plus"></i>
        Agregar nuevo Usuario
    </button>
    </div>


    <div class="container-fluid" style="  width: 100%; overflow-x: auto; margin: auto; ">

        <table id="usuarios" class=" table-striped" style="border: 0px; border: none; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="width: 40%;">Nombre</th>
                    <th style="width: 40%;">Correo</th>
                    <th style="width: 20%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>

                        <td>
                            <div style="display: inline-block; text-align:center">

                                <div class="btn-group text-right" role="group" >
                                    @include('usuarios.modal.editar')

                                    <div class="tooltip-container">
                                        <a class="btn btn-warning btn-sm" href="#" style=" border-radius: 0%;" data-toggle="modal" title="Editar" data-target="#editarModal{{ $user->id }}">
                                            <i class="fas fa-edit" style="color: white"></i></a>
                                        <span class="tooltipUI bg-warning">Editar</span>
                                        <span class="text"></span>
                                    </div>

                                    @include('usuarios.modal.mostrar')

                                    <div class="tooltip-container">
                                        <a class="btn btn-primary btn-sm" href="#" style=" border-radius: 0%;" data-toggle="modal" title="Ver" data-target="#mostrarModal{{ $user->id  }}">
                                            <i class="fas fa-eye" style="color: white"></i>
                                        </a>
                                        <span class="tooltipUI bg-primary">Ver datos</span>
                                        <span class="text"></span>
                                    </div>


                                    <div class="tooltip-container">
                                        <button style="border-radius: 0%" class="btn btn-danger btn-sm" onclick="confirmarEliminarUsuario('deleteFormUser{{ $user->id }}')" type="button">
                                            <form id="deleteFormUser{{ $user->id }}" action="{{ route('usuario.destroy', $user->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <i class="fas fa-trash"></i>
                                            </form>
                                            <span class="tooltipUI bg-danger">Eliminar Usuario</span>
                                            <span class="text"></span>
                                        </button>
                                    </div>

                                </div>

                            </div>
                        </td>
                    </tr>
                    @endforeach
            </tbody>
        </table>
    </div>
 


<script>
    $(document).ready(function() {
        var groupColumn = 0;

        var table = $('#usuarios').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.0/i18n/es-MX.json'
            },
        });
    });
</script>



<script>
    function confirmarEliminarUsuario(formId) {
        Swal.fire({
            title: "¿Estás seguro de eliminar el Usuario?",
            text: "Esta acción no se puede revertir",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>



    <script>
        function confirmarActualizarUsuario(formId, userId) {
          //  console.log("Función confirmarActualizarUsuario llamada con el ID del formulario: " + formId);
            var form = document.getElementById(formId);
            if (!form) {
           //     console.error("El formulario con el ID " + formId + " no fue encontrado.");
                return;
            }

            // Validar que ningún campo esté vacío
            var name = document.getElementById('name' + userId).value.trim();
            var email = document.getElementById('email' + userId).value.trim();
            var password = document.getElementById('password' + userId).value.trim();
            var confirmPassword = document.getElementById('confirm_password' + userId).value.trim();

            if (!name || !email ) {
                Swal.fire({
                    title: "Error",
                    text: "Ingresa por lo menos el nombre y correo",
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Aceptar"
                });
                return;
            }

            // Validar que las contraseñas coincidan antes de mostrar el SweetAlert
            if (password !== confirmPassword) {
                Swal.fire({
                    title: "Error",
                    text: "Las contraseñas no coinciden",
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Aceptar"
                });
                return;
            }

            Swal.fire({
                title: "¿Quieres actualizar el registro?",
                text: "Se actualizara la información",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, actualizar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
             
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

    <script>
        function confirmarCrearUsuario(formId) {
          // console.log("Función confirmarCrear llamada con el ID del formulario: " + formId);
            var form = document.getElementById(formId);
            if (!form) {
            //    console.error("El formulario con el ID " + formId + " no fue encontrado.");
                return;
            }

            // Validar que ningún campo esté vacío
            var name = document.getElementById('name').value.trim();
            var email = document.getElementById('email').value.trim();
            var password = document.getElementById('password').value.trim();
            var confirmPassword = document.getElementById('confirm_password').value.trim();

            if (!name || !email || !password || !confirmPassword) {
                Swal.fire({
                    title: "Error",
                    text: "Todos los campos deben estar llenos",
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Aceptar"
                });
                return;
            }

            // Validar que las contraseñas coincidan antes de mostrar el SweetAlert
            if (password !== confirmPassword) {
                Swal.fire({
                    title: "Error",
                    text: "Las contraseñas no coinciden",
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Aceptar"
                });
                return;
            }

            Swal.fire({
                title: "¿Quieres crear un nuevo registro?",
                text: "Se actualizara la información",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, enviar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
             
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

@endsection
