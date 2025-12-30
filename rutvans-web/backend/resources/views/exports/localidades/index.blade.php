@extends('adminlte::page')

@section('title', 'Datos Localidades')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center p-3 mb-3" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div>
            <h1 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem;">
                <i class="fas fa-table me-2"></i> Datos de Localidades
            </h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 0.95rem;">Consulta y exporta información de localidades registradas</p>
        </div>
    </div>
@stop

@section('content')
    <div class="card shadow-sm" style="border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ff6600, #e55a00); color: white; border-radius: 12px 12px 0 0; padding: 1.5rem;">
            <h3 class="mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <i class="fas fa-list me-2"></i> Lista de Localidades
            </h3>
            <div class="ml-auto d-flex align-items-center">
                <label for="filter-date" class="text-white mx-2" style="font-weight: 500;">Fecha específica:</label>
                <input type="date" id="filter-date" class="form-control mx-2" style="display: inline-block; width: auto; border-radius: 6px;">
                
                <button id="export-pdf" class="btn btn-light mx-2" style="color: #dc3545; font-weight: 600; border-radius: 6px;">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <button id="export-excel" class="btn btn-light mx-2" style="color: #28a745; font-weight: 600; border-radius: 6px;">
                    <i class="fas fa-file-excel me-1"></i> Excel
                </button>
            </div>
        </div>
        <div class="card-body" style="padding: 2rem;">
            <!-- Tabla de Localidades -->
            <div class="table-responsive">
                <table id="localidades-table" class="table table-bordered table-striped table-hover" style="border-radius: 8px; overflow: hidden;">
                    <thead style="background: linear-gradient(135deg, #6c757d, #495057); color: white;">
                        <tr>
                            <th style="width: 10%">#</th>
                            <th>Longitud</th>
                            <th>Latitud</th>
                            <th>Localidad</th>
                            <th>Calle</th>
                            <th>Código Postal</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($localidades as $localidad)
                            <tr style="transition: all 0.3s ease;">
                                <td><span class="badge bg-primary" style="font-size: 0.85rem;">{{ $localidad->id }}</span></td>
                                <td style="font-weight: 500;">{{ $localidad->longitude }}</td>
                                <td style="font-weight: 500;">{{ $localidad->latitude }}</td>
                                <td style="font-weight: 600; color: #495057;">{{ $localidad->locality }}</td>
                                <td>{{ $localidad->street }}</td>
                                <td><span class="badge bg-secondary">{{ $localidad->postal_code }}</span></td>
                                <td style="color: #6c757d;">{{ \Carbon\Carbon::parse($localidad->created_at)->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-3">
        <div style="background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            {{ $localidades->links() }}
        </div>
    </div>
@stop

@section('css')
    <!-- Estilos de DataTables con Bootstrap -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .content-wrapper {
            background-color: #f8f9fa;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            const table = $('#localidades-table').DataTable({
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "url": "/assets/datatables/Spanish.json" // Ruta local del archivo de idioma
                },
                "paging": false, // Desactivamos la paginación de DataTables porque Laravel la maneja
                "searching": true, // Habilitar búsqueda dinámica
                "ordering": true // Habilitar ordenamiento
            });

            // Filtrar por fecha específica
            $('#filter-date').on('change', function() {
                const selectedDate = $(this).val(); // Obtener la fecha seleccionada en formato YYYY-MM-DD
                if (selectedDate) {
                    table.column(6).search('^' + selectedDate, true, false)
                        .draw(); // Filtrar buscando al inicio de la columna Fecha
                } else {
                    table.column(6).search('').draw(); // Reiniciar el filtro si se borra la fecha
                }
            });


            // Exportar PDF con el filtro activo
            $('#export-pdf').on('click', function() {
                const search = table.search(); // Obtiene el término de búsqueda
                const selectedDate = $('#filter-date').val(); // Obtiene la fecha seleccionada

                // Construye la URL con parámetros
                let url = "{{ route('exports.pdf.localidades') }}";

                if (search) {
                    url += "?search=" + encodeURIComponent(search);
                }
                if (selectedDate) {
                    url += (search ? "&" : "?") + "start_date=" + encodeURIComponent(selectedDate);
                }

                // Mostrar SweetAlert de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Exportando PDF...',
                    text: 'La descarga comenzará en breve',
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 2000,
                    width: '300px'
                });

                window.location.href = url; // Redirige al controlador con los parámetros
            });
            
            $('#export-excel').on('click', function() {
                const search = table.search(); // Obtiene el término de búsqueda
                const selectedDate = $('#filter-date').val(); // Obtiene la fecha seleccionada

                // Construye la URL con parámetros
                let url = "{{ route('exports.excel.localidades') }}";

                if (search) {
                    url += "?search=" + encodeURIComponent(search);
                }
                if (selectedDate) {
                    url += search ? "&" : "?";
                    url += "start_date=" + encodeURIComponent(selectedDate);
                }

                // Mostrar SweetAlert de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Exportando Excel...',
                    text: 'La descarga comenzará en breve',
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 2000,
                    width: '300px'
                });

                window.location.href = url; // Redirige al controlador con los parámetros
            });

            // SweetAlert: Toasts para mensajes de éxito y error
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
        });
    </script>
@stop
