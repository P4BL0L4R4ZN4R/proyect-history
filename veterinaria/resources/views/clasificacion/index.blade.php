@extends('adminlte::page')

@section('title', 'Administrar Clasificación')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><i class="fas fa-tags mr-2 text-primary"></i>Administrar Clasificación</h1>
        </div>
        <div class="col-sm-6">

        </div>
    </div>
</div>
@stop

@section('content')

@include('clasificacion.modals_edit')
@include('clasificacion.modals_create')

<div class="container-fluid">
    <div class="row">
        <!-- Card Categorías - Columna Izquierda -->
        <div class="col-md-6">
            <div class="card card-info collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-folder-open mr-2"></i><strong>Categorías Principales</strong>
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-chevron-down"></i> <!-- Icono cambiado a chevron -->
                        </button>
                        <button class="btn btn-sm btn-success ml-2" id="btnCrearCategoria" data-bs-toggle="modal" data-bs-target="#modalCrearCategoria">
                            <i class="fas fa-plus-square mr-1"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body p-0" style="display: none;">
                    <div class="table-responsive">
                        <table id="tablaCategorias" class="table table-hover mb-0 w-100">
                            <thead class="bg-light">
                                <tr>
                                    <th class="pl-3">Nombre</th>
                                    <th class="text-center" style="width: 120px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-2">
                    <small class="text-muted">Total: <span id="countCategorias">0</span> registros</small>
                </div>
            </div>
        </div>

        <!-- Card Subcategorías - Columna Derecha -->
        <div class="col-md-6">
            <div class="card card-secondary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-folder mr-2"></i><strong>Subcategorías</strong>
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-chevron-down"></i> <!-- Icono cambiado a chevron -->
                        </button>
                        <button class="btn btn-sm btn-success ml-2" data-bs-toggle="modal" data-bs-target="#modalCrearSubcategoria">
                            <i class="fas fa-plus-square mr-1"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body p-0" style="display: none;">
                    <div class="table-responsive">
                        <table id="tablaSubcategorias" class="table table-hover mb-0 w-100">
                            <thead class="bg-light">
                                <tr>
                                    <th class="pl-3">Nombre</th>
                                    <th class="text-center" style="width: 120px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-2">
                    <small class="text-muted">Total: <span id="countSubcategorias">0</span> registros</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
<style>
    /* Estilos mínimos necesarios */
    .table th {
        border-top: none;
    }
    .btn-action {
        margin-right: 5px;
    }
    .btn-action:last-child {
        margin-right: 0;
    }
    .card {
        margin-bottom: 20px;
        height: 100%; /* Asegura misma altura */
    }
    .row {
        display: flex;
        flex-wrap: wrap;
    }
    .col-md-6 {
        display: flex;
        flex-direction: column;
    }
</style>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>
                    $(document).ready(function() {
                    // Variables para DataTables
                    var tablaCategorias, tablaSubcategorias;

                    // Configuración de Toastr
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "timeOut": 5000
                    };

                    // ==================== INICIALIZACIÓN DE DATATABLES ====================
                    function initTablaCategorias() {
                        if ($.fn.DataTable.isDataTable('#tablaCategorias')) {
                            $('#tablaCategorias').DataTable().destroy(true);
                        }

                        tablaCategorias = $('#tablaCategorias').DataTable({
                            responsive: true,
                            language: {
                                url: 'https://cdn.datatables.net/plug-ins/2.3.2/i18n/es-ES.json'
                            },
                            ajax: {
                                url: "{{ route('clasificacion.categorias.ajax') }}",
                                dataSrc: '',
                                error: function(xhr) {
                                    toastr.error(xhr.responseJSON?.error || 'Error al cargar categorías');
                                }
                            },
                            columns: [
                                { data: 'nombre' },
                                {
                                    data: null,
                                    render: function(data, type, row) {
                                        return `
                                        <div class="text-center">
                                            <button class="btn btn-warning btn-sm btn-editar-categoria" data-id="${row.id}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-eliminar-categoria ml-1" data-id="${row.id}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>`;
                                    }
                                }
                            ]
                        });
                    }

                    function initTablaSubcategorias() {
                        if ($.fn.DataTable.isDataTable('#tablaSubcategorias')) {
                            $('#tablaSubcategorias').DataTable().destroy(true);
                        }

                        tablaSubcategorias = $('#tablaSubcategorias').DataTable({
                            responsive: true,
                            language: {
                                url: 'https://cdn.datatables.net/plug-ins/2.3.2/i18n/es-ES.json'
                            },
                            ajax: {
                                url: "{{ route('clasificacion.subcategorias.ajax') }}",
                                dataSrc: '',
                                error: function(xhr) {
                                    toastr.error(xhr.responseJSON?.error || 'Error al cargar subcategorías');
                                }
                            },
                            columns: [
                                { data: 'nombre' },
                                {
                                    data: null,
                                    render: function(data, type, row) {
                                        return `
                                        <div class="text-center">
                                            <button class="btn btn-warning btn-sm btn-editar-subcategoria" data-id="${row.id}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-eliminar-subcategoria ml-1" data-id="${row.id}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>`;
                                    }
                                }
                            ]
                        });
                    }

                    // ==================== ELIMINACIÓN ====================
                    $(document).on('click', '.btn-eliminar-categoria', function() {
                        let id = $(this).data('id');

                        Swal.fire({
                            title: '¿Eliminar categoría?',
                            text: "Los productos se reasignarán a 'Sin categoría'",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: `/categorias/${id}`,
                                    type: 'DELETE',
                                    data: { _token: "{{ csrf_token() }}" },
                                    success: function(response) {
                                        if (response.success) {
                                            tablaCategorias.ajax.reload();
                                            toastr.success('Categoría eliminada');
                                        } else {
                                            toastr.error(response.error);
                                        }
                                    },
                                    error: function(xhr) {
                                        toastr.error(xhr.responseJSON?.error || 'Error al eliminar');
                                    }
                                });
                            }
                        });
                    });

                    $(document).on('click', '.btn-eliminar-subcategoria', function() {
                        let id = $(this).data('id');

                        Swal.fire({
                            title: '¿Eliminar subcategoría?',
                            text: "Los productos se reasignarán a 'Sin subcategoría'",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: `/subcategorias/${id}`,
                                    type: 'DELETE',
                                    data: { _token: "{{ csrf_token() }}" },
                                    success: function(response) {
                                        if (response.success) {
                                            tablaSubcategorias.ajax.reload();
                                            toastr.success('Subcategoría eliminada');
                                        } else {
                                            toastr.error(response.error);
                                        }
                                    },
                                    error: function(xhr) {
                                        toastr.error(xhr.responseJSON?.error || 'Error al eliminar');
                                    }
                                });
                            }
                        });
                    });

                                // ==================== INICIALIZACIÓN ====================
                                initTablaCategorias();
                                initTablaSubcategorias();

                                // ==================== CIERRE DE MODALES ====================
                                $('.modal').on('hidden.bs.modal', function () {
                                    $(this).find('form').trigger('reset');
                                });
                            });

</script>




@endsection


