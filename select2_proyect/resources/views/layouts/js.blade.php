{{-- <script>
    $(document).ready(function() {
        var groupColumn = 0;

        var table = $('#laboratorios-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.0/i18n/es-MX.json',
            },
            serverSide: true,
            responsive: true,
            columnDefs: [
                { visible: false, targets: groupColumn },
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],
            lengthMenu: [ [1, 5, 10, 15, 25, -1], [1, 5, 10, 15, 25, "Todo"] ],
            pageLength: 1, // Valor inicial para el tamaño de página
            ajax: {
                url: "{{ route('laboratorio.LaboratorioIndex') }}",
                type: 'GET',
                data: function(d) {
                    // Agrega el valor de lengthMenu a los datos
                    // d.lengthMenu = d.length;
                    d.length = d.length; // Número de registros por página
                    d.start = d.start;   // Página actual (offset)
                    d.draw = d.draw;    // Número de solicitud de DataTables (para manejar solicitudes concurrentes)
                },
                dataSrc: function(json) {
                    // Ajusta los datos para el formato de DataTables
                    return json.data;
                }
            },
            columns: [
                { data: 'nombre', name: 'nombre' },
                { data: 'descripcion_sucursal', name: 'descripcion_sucursal',
                    render: function(data) {
                        return '<div style="text-align: center;">' + data + '</div>';
                    }
                },
                { data: 'VERSION', name: 'VERSION' },
                { data: 'VETERINARIA', name: 'VETERINARIA', searchable: false,
                    render: function(data, type, row) {
                        return `
                        <div class="tooltip-containerSW">
                            <div class="switch">
                                <label class="switch-VETERINARIA" id="switchLabel-${row.id}">
                                    <input type="checkbox" class="form-check-input switch-veterinaria"
                                        ${data === 'Veterinaria' ? 'checked' : ''} 
                                        data-laboratorio-id="${row.Idlaboratorio}"
                                        data-usuario="{{ Auth::user()->name }}"
                                        data-sucursal-id="${row.idSucursal}"
                                        data-laboratorio-nombre="${row.laboratorio}"
                                        data-cfdi-id="${row.CFDIid}"
                                        data-sucursal="${row.descripcion_sucursal}"
                                        value="${data}">
                                    <span class="slider-VETERINARIA"></span>
                                </label>
                            </div>
                            <span class="tooltipSW bg-info">Alternar entre veterinaria<br>&nbsp;&nbsp;&nbsp;&nbsp;y<br>&nbsp;&nbsp;&nbsp;&nbsp;laboratorio</span>
                            <span class="text"></span>
                        </div>`;
                    }
                },
                { data: 'lastUpdate', name: 'lastUpdate' },
                { data: 'conteoPorDia', name: 'conteoPorDia', searchable: false, orderable: true,
                    render: function(data) {
                        return '<div style="text-align: center;">' + data + '</div>';
                    }
                },
                { data: 'conteoPorMes', name: 'conteoPorMes', searchable: false, orderable: true,
                    render: function(data) {
                        return '<div style="text-align: center;">' + data + '</div>';
                    }
                },
                { data: 'conteoPorAnio', name: 'conteoPorAnio', searchable: false, orderable: true,
                    render: function(data) {
                        return '<div style="text-align: center;">' + data + '</div>';
                    }
                },
                { data: 'conteoMesAnterior', name: 'conteoMesAnterior', searchable: false, orderable: true,
                    render: function(data) {
                        return '<div style="text-align: center;">' + data + '</div>';
                    }
                },
                { data: 'flag_sucursales', name: 'flag_sucursales', searchable: false, orderable: true },
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                        @include('laboratorios.modal.editarCFDI')
                        <div role="group btn-group" style="display: inline-flex;">
                            <div class="tooltip-container">
                                <button class="comic-button-warning editar-cfdi" data-toggle="modal" 
                                    data-target="#editarModalCFDI${row.CFDIid}-${row.Idlaboratorio}"
                                    data-cfdi-id="${row.CFDIid}" data-laboratorio-id="${row.Idlaboratorio}">
                                    <i class="fas fa-pen" style="color: white;"></i>
                                </button>
                                <span class="tooltipUI bg-warning">Editar</span>
                                <span class="text"></span>
                            </div>
                            @include('laboratorios.modal.mostrarCFDI')
                            <div class="tooltip-container">
                                <button class="comic-button-primary ver-cfdi" data-toggle="modal" 
                                    data-target="#mostrarModalCFDI${row.CFDIid}-${row.Idlaboratorio}"
                                    data-cfdi-id="${row.CFDIid}" data-laboratorio-id="${row.Idlaboratorio}">
                                    <i class="far fa-eye"></i>
                                </button>
                                <span class="tooltipUI bg-primary">Ver datos</span>
                                <span class="text"></span>
                            </div>
                            @include('laboratorios.modal.consumoXperiodo')
                            <div class="tooltip-container">
                                <button class="comic-button-success-outline consumosX" data-toggle="modal" 
                                    data-target="#mostrarModalCFDI${row.CFDIid}-${row.Idlaboratorio}"
                                    data-sucursal-id="${row.idSucursal}"
                                    data-sucursal="${row.descripcion_sucursal}"
                                    data-nombre-laboratorio="${row.laboratorio}"
                                    data-cfdi-id="${row.CFDIid}" data-laboratorio-id="${row.Idlaboratorio}">
                                    <i class="fas fa-calendar"></i>
                                </button>
                                <span class="tooltipUI bg-success">Consultar ordenes</span>
                                <span class="text"></span>
                            </div>
                            <div class="tooltip-container">
                                <button title="Eliminar" class="eliminar-cfdi comic-button-danger"  
                                    data-laboratorio-id="${row.Idlaboratorio}" data-usuario="{{ Auth::user()->name }}" data-sucursal-id="${row.idSucursal}" data-laboratorio-nombre="${row.laboratorio}" data-cfdi-id="${row.CFDIid}" data-sucursal="${row.descripcion_sucursal}">
                                    <i class="fas fa-trash" style="color: white;"></i>
                                </button>
                                <span class="tooltipUI bg-danger">Eliminar</span>
                                <span class="text"></span>
                            </div>
                        </div>`;
                    }
                },
                {
                    data: 'SUSCRIPCION',
                    name: 'SUSCRIPCION',
                    render: function(data, type, row) {
                        return `
                        <div class="tooltip-containerSW">
                            <div class="switch">
                                <label class="switch-SUSCRIPCION" id="switchLabel-${row.id}">
                                    <input type="checkbox" class="form-check-input switch-suscripcion"
                                        ${data === 'ACTIVADO' ? 'checked' : ''} 
                                        data-laboratorio-id="${row.Idlaboratorio}"
                                        data-usuario="{{ Auth::user()->name }}"
                                        data-sucursal-id="${row.idSucursal}"
                                        data-laboratorio-nombre="${row.laboratorio}"
                                        data-cfdi-id="${row.CFDIid}"
                                        data-sucursal="${row.descripcion_sucursal}"
                                        value="${data}">
                                    <span class="slider-SUSCRIPCION"></span>
                                </label>
                            </div>
                            <span class="tooltipSW bg-info">Cambiar entre activado<br>&nbsp;&nbsp;&nbsp;&nbsp;y<br>&nbsp;&nbsp;&nbsp;&nbsp;desactivado</span>
                            <span class="text"></span>
                        </div>`;
                    }
                }
            ],
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function(group, i) {
                        let partes = group.split('|');
                        var idlab = partes[0];

                        if (last !== group) {
                            $(rows)
                                .eq(i)
                                .before(
                                    `<tr class="group">
                                        <td colspan="9" class="table-secondary">${partes[1]}</td>
                                        <td colspan="3" class="table-secondary">
                                            <div role="group" style="display: inline-flex;">
                                                @include('laboratorios.modal.editar')
                                                <div class="tooltip-container">
                                                    <button class="comic-button-warning editar-laboratorio" data-laboratorio-id="${partes[0]}">
                                                        <i class="fas fa-pen" style="color: white;"></i>
                                                    </button>
                                                    <span class="tooltipUI bg-warning">Editar</span>
                                                    <span class="text"></span>
                                                </div>
                                                @include('laboratorios.modal.conexion')
                                                <div class="tooltip-container">
                                                    <button class="comic-button-primary ver-laboratorio" data-laboratorio-id="${partes[0]}" data-nombre="${partes[1]}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <span class="tooltipUI bg-primary">Ver datos del laboratorio</span>
                                                    <span class="text"></span>
                                                </div>
                                                @include('laboratorios.modal.config')
                                                <div class="tooltip-container">
                                                    <button class="comic-button-success configurar-conexion" data-laboratorio-id="${partes[0]}" data-nombre="${partes[1]}">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <span class="tooltipUI bg-success">Configurar conexion de base de datos</span>
                                                    <span class="text"></span>
                                                </div>
                                                <div class="tooltip-container">
                                                    <button class="comic-button-danger" onclick="confirmarEliminar('deleteFormLaboratorio${partes[0]}')" data-usuario="{{ Auth::user()->name }}">
                                                        <form id="deleteFormLaboratorio${partes[0]}" action="{{ route('laboratorio.destroy', '') }}/${partes[0]}" method="post" data-usuario="{{ Auth::user()->name }}">
                                                            <input type="hidden" id="usuario" name="usuario" value="{{ Auth::user()->name }}">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                        <i class="fas fa-trash" style="color: white"></i>
                                                    </button>
                                                    <span class="tooltipUI bg-danger">Eliminar Laboratorio y conexion</span>
                                                    <span class="text"></span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>`
                                );

                            last = group;
                        }
                    });
            }
        });

        // Mostrar el loader antes de enviar la solicitud AJAX
        table.on('preXhr.dt', function() {
            $('#wave-menu').show();
        });

        // Ocultar el loader después de recibir la respuesta AJAX
        table.on('xhr.dt', function() {
            $('#wave-menu').hide();
        });
    });
</script> --}}


<script>
    $(document).ready(function() {
        var groupColumn = 0;

        var table = $('#laboratorios-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.0/i18n/es-MX.json',
            },
            serverSide: true,
            responsive: true,
            columnDefs: [
                { visible: false, targets: groupColumn },
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],
            lengthMenu: [[1, 5, 10, 15, 25, 100], [1, 5, 10, 15, 25, 100]],
            displayLength: 25,
            paging: true,
            ajax: {
                url: "{{ route('laboratorio.LaboratorioIndex') }}",
                type: 'GET',
                dataSrc: 'data',
                beforeSend: function() {
                    $('#wave-menu').show();  // Mostrar el loader antes de la solicitud
                },
                complete: function() {
                    $('#wave-menu').hide();  // Ocultar el loader después de la solicitud
                }
            },
            columns: [
                { data: 'nombre', name: 'nombre' },
                { 
                    data: 'descripcion_sucursal', 
                    name: 'descripcion_sucursal',
                    render: function(data) {
                        return '<div style="text-align: center;">' + data + '</div>';
                    }
                },
                { data: 'VERSION', name: 'VERSION' },
                { 
                    data: 'VETERINARIA', 
                    name: 'VETERINARIA', 
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="tooltip-containerSW">
                                <div class="switch">
                                    <label class="switch-VETERINARIA" id="switchLabel-${row.id}">
                                        <input type="checkbox" class="form-check-input switch-veterinaria"
                                            ${row.VETERINARIA == 'Veterinaria' ? 'checked' : ''} 
                                            data-laboratorio-id="${row.Idlaboratorio}"
                                            data-usuario="{{ Auth::user()->name }}"
                                            data-sucursal-id="${row.idSucursal}"
                                            data-laboratorio-nombre="${row.laboratorio}"
                                            data-cfdi-id="${row.CFDIid}"
                                            data-sucursal="${row.descripcion_sucursal}"
                                            value="${row.VETERINARIA}">
                                        <span class="slider-VETERINARIA"></span>
                                    </label>
                                </div>
                                <span class="tooltipSW bg-info">Alternar entre veterinaria<br>&nbsp;&nbsp;&nbsp;&nbsp;y<br>&nbsp;&nbsp;&nbsp;&nbsp;laboratorio</span>
                                <span class="text"></span>
                            </div>
                        `;
                    }
                },
                { data: 'lastUpdate', name: 'lastUpdate' },
                { 
                    data: 'conteoPorDia', 
                    name: 'conteoPorDia', 
                    searchable: false,
                    orderable: true, 
                    render: function(data) {
                        return '<div style="text-align: center;">' + data + '</div>';
                    }
                },
                { 
                    data: 'conteoPorMes', 
                    name: 'conteoPorMes', 
                    searchable: false,
                    orderable: true, 
                    render: function(data) {
                        return '<div style="text-align: center;">' + data + '</div>';
                    }
                },
                { 
                    data: 'conteoPorAnio', 
                    name: 'conteoPorAnio', 
                    searchable: false,
                    orderable: true, 
                    render: function(data) {
                        return '<div style="text-align: center;">' + data + '</div>';
                    }
                },
                { 
                    data: 'conteoMesAnterior', 
                    name: 'conteoMesAnterior', 
                    searchable: false,
                    orderable: true,
                    render: function(data) {
                        return '<div style="text-align: center;">' + data + '</div>';
                    }
                },
                { 
                    data: 'flag_sucursales', 
                    name: 'flag_sucursales', 
                    searchable: false, 
                    orderable: true
                },
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row) {
                        return ` 
                            @include('laboratorios.modal.editarCFDI')
                            <div role="group btn-group" style="display: inline-flex;">
                                <div class="tooltip-container">
                                    <button class="comic-button-warning editar-cfdi" data-toggle="modal" 
                                        data-target="#editarModalCFDI${row.CFDIid}-${row.Idlaboratorio}"
                                        data-cfdi-id="${row.CFDIid}" data-laboratorio-id="${row.Idlaboratorio}">
                                        <i class="fas fa-pen" style="color: white;"></i>
                                    </button>
                                    <span class="tooltipUI bg-warning">Editar</span>
                                    <span class="text"></span>
                                </div>
                                @include('laboratorios.modal.mostrarCFDI')
                                <div class="tooltip-container">
                                    <button class="comic-button-primary ver-cfdi" data-toggle="modal" 
                                        data-target="#mostrarModalCFDI${row.CFDIid}-${row.Idlaboratorio}"
                                        data-cfdi-id="${row.CFDIid}" data-laboratorio-id="${row.Idlaboratorio}">
                                        <i class="far fa-eye"></i>
                                    </button>
                                    <span class="tooltipUI bg-primary">Ver datos</span>
                                    <span class="text"></span>
                                </div>
                                @include('laboratorios.modal.consumoXperiodo')
                                <div class="tooltip-container">
                                    <button class="comic-button-success-outline consumosX" data-toggle="modal" 
                                        data-target="#mostrarModalCFDI${row.CFDIid}-${row.Idlaboratorio}"
                                        data-sucursal-id="${row.idSucursal}"
                                        data-sucursal="${row.descripcion_sucursal}"
                                        data-nombre-laboratorio="${row.laboratorio}"
                                        data-cfdi-id="${row.CFDIid}" data-laboratorio-id="${row.Idlaboratorio}">
                                        <i class="fas fa-calendar"></i>
                                    </button>
                                    <span class="tooltipUI bg-success">Consultar ordenes</span>
                                    <span class="text"></span>
                                </div>
                                <div class="tooltip-container">
                                    <button title="Eliminar" class="eliminar-cfdi comic-button-danger"  
                                        data-laboratorio-id="${row.Idlaboratorio}" data-usuario="{{ Auth::user()->name }}" data-sucursal-id="${row.idSucursal}" data-laboratorio-nombre="${row.laboratorio}" data-cfdi-id="${row.CFDIid}" data-sucursal="${row.descripcion_sucursal}">
                                        <i class="fas fa-trash" style="color: white;"></i>
                                    </button>
                                    <span class="tooltipUI bg-danger">Eliminar</span>
                                    <span class="text"></span>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'SUSCRIPCION',
                    name: 'SUSCRIPCION',
                    render: function(data, type, row) {
                        return `
                            <div class="tooltip-containerSW">
                                <div class="switch">
                                    <label class="switch-SUSCRIPCION" id="switchLabel-${row.id}">
                                        <input type="checkbox" class="form-check-input switch-suscripcion"
                                            ${row.SUSCRIPCION == 'ACTIVADO' ? 'checked' : ''} 
                                            data-laboratorio-id="${row.Idlaboratorio}"
                                            data-usuario="{{ Auth::user()->name }}"
                                            data-sucursal-id="${row.idSucursal}"
                                            data-laboratorio-nombre="${row.laboratorio}"
                                            data-cfdi-id="${row.CFDIid}"
                                            data-sucursal="${row.descripcion_sucursal}"
                                            value="${row.SUSCRIPCION}">
                                        <span class="slider-SUSCRIPCION"></span>
                                    </label>
                                </div>
                                <span class="tooltipSW bg-info">Cambiar entre activado<br>&nbsp;&nbsp;&nbsp;&nbsp;y<br>&nbsp;&nbsp;&nbsp;&nbsp;desactivado</span>
                                <span class="text"></span>
                            </div>
                        `;
                    }
                }
            ],
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function(group, i) {
                        let partes = group.split('|');
                        var idlab = partes[0];

                        if (last !== group) {
                            $(rows)
                                .eq(i)
                                .before(
                                    `<tr class="group">
                                        <td colspan="9" class="table-secondary">${partes[1]}</td>
                                        <td colspan="3" class="table-secondary">
                                            <div role="group" style="display: inline-flex;">
                                                @include('laboratorios.modal.editar')
                                                <div class="tooltip-container">
                                                    <button class="comic-button-warning editar-laboratorio" data-laboratorio-id="${partes[0]}">
                                                        <i class="fas fa-pen" style="color: white;"></i>
                                                    </button>
                                                    <span class="tooltipUI bg-warning">Editar</span>
                                                    <span class="text"></span>
                                                </div>
                                                @include('laboratorios.modal.conexion')
                                                <div class="tooltip-container">
                                                    <button class="comic-button-primary ver-laboratorio" data-laboratorio-id="${partes[0]}" data-nombre="${partes[1]}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <span class="tooltipUI bg-primary">Ver datos del laboratorio</span>
                                                    <span class="text"></span>
                                                </div>
                                                @include('laboratorios.modal.config')
                                                <div class="tooltip-container">
                                                    <button class="comic-button-success configurar-conexion" data-laboratorio-id="${partes[0]}" data-nombre="${partes[1]}">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <span class="tooltipUI bg-success">Configurar conexion de base de datos</span>
                                                    <span class="text"></span>
                                                </div>
                                                <div class="tooltip-container">
                                                    <button class="comic-button-danger" onclick="confirmarEliminar('deleteFormLaboratorio${partes[0]}')" data-usuario="{{ Auth::user()->name }}">
                                                        <form id="deleteFormLaboratorio${partes[0]}" action="{{ route('laboratorio.destroy', '') }}/${partes[0]}" method="post" data-usuario="{{ Auth::user()->name }}">
                                                            <input type=hidden id="usuario" name="usuario" value="{{ Auth::user()->name }}">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                        <i class="fas fa-trash" style="color: white"></i>
                                                    </button>
                                                    <span class="tooltipUI bg-danger">Eliminar Laboratorio y conexion</span>
                                                    <span class="text"></span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>`
                                );

                            last = group;
                        }
                    });
            },
        });
    });
</script>




{{-- original --}}
    {{-- <script>
        $(document).ready(function() {

            var groupColumn = 0;
            // var nes = 12;
            var table = $('#laboratorios-table').DataTable({
                language: {
                        url: '//cdn.datatables.net/plug-ins/2.1.0/i18n/es-MX.json',
                        },
                // processing: true,
                serverSide: true,
                responsive: true,
                columnDefs: [{ visible: false, targets: groupColumn},
                // { className: 'table-secondary', targets: groupColumn },
                { responsivePriority: 1, targets: 0 }, 
                { responsivePriority: 2, targets: -1 }
                ],
            
                
                // order: [[nes, 'desc']],

                lengthMenu: [ [1, 5, 10, 15, 25, -1], [1, 5, 10, 15, 25, "Todo"] ],
                // displayLength: 25,
                displayLength: 1,
                paging: true,
                ajax: {
                    url: "{{ route('laboratorio.LaboratorioIndex') }}",
                    type: 'GET',
                    dataSrc: 'data' // Nombre del objeto que contiene los datos en la respuesta JSON


                },



                

                columns: [
                    { data: 'nombre', name: 'nombre'},
                    { data: 'descripcion_sucursal', name: 'descripcion_sucursal' , 
                    render: function(data, type, row) {
            return '<div style="text-align: center;">' + data + '</div>';
        }
                    },
                    
                    { data: 'VERSION', name: 'VERSION' },
                    { data: 'VETERINARIA', name: 'VETERINARIA' , "searchable": false,
                    render: function(data, type, row, meta) {
                            // Renderiza un switch en lugar del campo SUSCRIPCION
                            return `
                            <div class="tooltip-containerSW">
                        
                                <div class="switch">
                                        <label class="switch-VETERINARIA" id="switchLabel-${row.id}">
                                            <input type="checkbox" class="form-check-input switch-veterinaria"
                                                ${row.VETERINARIA == 'Veterinaria' ? 'checked' : ''} 
                                                data-laboratorio-id="${row.Idlaboratorio}"
                                                data-usuario="{{ Auth::user()->name }}"
                                                data-sucursal-id="${row.idSucursal}"
                                                data-laboratorio-nombre="${row.laboratorio}"
                                                data-cfdi-id="${row.CFDIid}"
                                                data-sucursal="${row.descripcion_sucursal}"
                                                value="${row.VETERINARIA}">
                                            <span class="slider-VETERINARIA"></span>
                                        </label>
                                </div>
                                    <span class="tooltipSW bg-info">Alternar entre veterinaria<br>&nbsp;&nbsp;&nbsp;&nbsp;y<br>&nbsp;&nbsp;&nbsp;&nbsp;laboratorio</span>
                                    <span class="text"></span>
                            </div>

                            `;
                        }
                    },
                    { data: 'lastUpdate', name: 'lastUpdate' },
                    // { data: 'TIMESESION', name: 'TIMESESION', searchable: false },
                    
                    // { data: 'SESIONESLIMITE', name: 'SESIONESLIMITE', searchable: false },
                

                
                    { data: 'conteoPorDia', name: 'conteoPorDia', searchable: false,
                        orderable: true, 
                        render: function(data, type, row) {
                            return '<div style="text-align: center;">' + data + '</div>';
                        }
                    },
                    { data: 'conteoPorMes', name: 'conteoPorMes', searchable: false,
                        orderable: true, 
                        render: function(data, type, row) {
                            return '<div style="text-align: center;">' + data + '</div>';
                        }
                    },
                    { data: 'conteoPorAnio', name: 'conteoPorAnio', searchable: false,
                        orderable: true, 
                        render: function(data, type, row) {
                            return '<div style="text-align: center;">' + data + '</div>';
                        }
                    },
                    { data: 'conteoMesAnterior', name: 'conteoMesAnterior', searchable: false,
                        orderable: true, 
                                        render: function(data, type, row) {
                            return '<div style="text-align: center;">' + data + '</div>';
                        }
                    },
                    { data: 'flag_sucursales', name: 'flag_sucursales', searchable: false, orderable: true,},
                    {
                        // Columna de acciones
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return ` 
                            @include('laboratorios.modal.editarCFDI')

                  
                        <div role="group btn-group" style="display: inline-flex;">
                        
                        <div class="tooltip-container">

                            <button  class="comic-button-warning editar-cfdi" data-toggle="modal" 
                            data-target="#editarModalCFDI${row.CFDIid}-${row.Idlaboratorio}"
                            data-cfdi-id="${row.CFDIid}" data-laboratorio-id="${row.Idlaboratorio}">
                                <i class="fas fa-pen" style="color: white;"></i>
                            </button>
                                <span class="tooltipUI bg-warning">Editar</span>
                                <span class="text"></span>
                        </div>

                        @include('laboratorios.modal.mostrarCFDI')
                        
                        <div class="tooltip-container">
                                <button class="comic-button-primary ver-cfdi" data-toggle="modal" 
                                data-target="#mostrarModalCFDI${row.CFDIid}-${row.Idlaboratorio}"
                                data-cfdi-id="${row.CFDIid}" data-laboratorio-id="${row.Idlaboratorio}">
                                    <i class="far fa-eye"></i>
                                </button>
                                    <span class="tooltipUI bg-primary">Ver datos</span>
                                    <span class="text"></span>
                        </div>

                        @include('laboratorios.modal.consumoXperiodo')
                        
                        <div class="tooltip-container">
                                <button class="comic-button-success-outline consumosX" data-toggle="modal" 
                                data-target="#mostrarModalCFDI${row.CFDIid}-${row.Idlaboratorio}"
                                data-sucursal-id="${row.idSucursal}"
                                data-sucursal="${row.descripcion_sucursal}"
                                data-nombre-laboratorio="${row.laboratorio}"

                                data-cfdi-id="${row.CFDIid}" data-laboratorio-id="${row.Idlaboratorio}" >
                                    <i class="fas fa-calendar"></i>
                                </button>
                                    <span class="tooltipUI bg-success">Consultar ordenes </span>
                                    <span class="text"></span>
                        </div>

                        <div class="tooltip-container">
                            <button title="Eliminar" class="eliminar-cfdi comic-button-danger"  
                                data-laboratorio-id="${row.Idlaboratorio}" data-usuario="{{ Auth::user()->name }}" data-sucursal-id="${row.idSucursal}" data-laboratorio-nombre="${row.laboratorio}" data-cfdi-id="${row.CFDIid}" data-sucursal="${row.descripcion_sucursal}" ><i class="fas fa-trash" style="color: white;"></i>
                            </button>
                                <span class="tooltipUI bg-danger">Eliminar</span>
                                <span class="text"></span>
                        </div>
                    </div>
                `;
                        }
                    },
                    {
                        data: 'SUSCRIPCION',
                        name: 'SUSCRIPCION',
                        render: function(data, type, row, meta) {
                            return `
                            <div class="tooltip-containerSW">

                                <div class="switch">
                                    <label class="switch-SUSCRIPCION" id="switchLabel-${row.id}">
                                        <input type="checkbox" class="form-check-input switch-suscripcion"
                                            ${row.SUSCRIPCION == 'ACTIVADO' ? 'checked' : ''} 
                                            data-laboratorio-id="${row.Idlaboratorio}"
                                            data-usuario="{{ Auth::user()->name }}"
                                            data-sucursal-id="${row.idSucursal}"
                                            data-laboratorio-nombre="${row.laboratorio}"
                                            data-cfdi-id="${row.CFDIid}"
                                            data-sucursal="${row.descripcion_sucursal}"
                                            value="${row.SUSCRIPCION}">
                                        <span class="slider-SUSCRIPCION" ></span>
                                    </label>
                                </div>
                                
                                    <span class="tooltipSW bg-info">Cambiar entre activado<br>&nbsp;&nbsp;&nbsp;&nbsp;y<br>&nbsp;&nbsp;&nbsp;&nbsp;desactivado</span>
                                    <span class="text"></span>
                            </div>
                            `;
                        }
                    },


                ],

                drawCallback: function (settings) {
                    var api = this.api();
                    var rows = api.rows({ page: 'current' }).nodes();
                    var last = null;

                    api.column(groupColumn, { page: 'current' })
                        .data()
                        .each(function (group, i) {
                            // console.log(group);
                            // let partes = group.split(",")
                            let partes = group.split('|');
                            // console.log(partes);
                            // console.log(partes[0]);
                            var idlab = partes[0];

                            if (last !== group) {
                                $(rows)
                                    .eq(i)
                                    .before(
                                        `<tr class="group" >
                                            <td colspan="9" class="table-secondary"  >${partes[1]}</td>
                                            <td colspan="3"  class="table-secondary">
                                                 
                                                <div role="group" style="display: inline-flex;">
                                                    @include('laboratorios.modal.editar')
                                                    

                                                    <div class="tooltip-container">
                                                        <button  class="comic-button-warning  editar-laboratorio" data-laboratorio-id="${partes[0]}">
                                                            <i class="fas fa-pen" style="color: white;"></i>
                                                        </button>
                                                            <span class="tooltipUI bg-warning">Editar</span>
                                                            <span class="text"></span>
                                                    </div>
                                                


                                                     @include('laboratorios.modal.conexion')

                                                        <div class="tooltip-container">
                                                                
                                                            <button class="comic-button-primary ver-laboratorio" data-laboratorio-id="${partes[0]}"  
                                                            data-nombre="${partes[1]}" >
                                                                <i class="fas fa-eye"></i>
                                                            </button> 

                                                                <span class="tooltipUI bg-primary">Ver datos del laboratorio</span>
                                                                <span class="text"></span>
                                                        </div>

                                                    @include('laboratorios.modal.config')
                                                        
                                                        <div class="tooltip-container">
                                                                
                                                            <button class="comic-button-success configurar-conexion" data-laboratorio-id="${partes[0]}"  
                                                            data-nombre="${partes[1]}" >
                                                                <i class="fas fa-cog"></i>
                                                            </button> 

                                                                <span class="tooltipUI bg-success">Configurar conexion de base de datos</span>
                                                                <span class="text"></span>
                                                        </div>

                                                        <div class="tooltip-container">

                                                                <button class="comic-button-danger"  onclick="confirmarEliminar('deleteFormLaboratorio${partes[0]}')"
                                                                
                                                                data-usuario="{{ Auth::user()->name }}">
                                                                    <form id="deleteFormLaboratorio${partes[0]}" action="{{ route('laboratorio.destroy', '') }}/${partes[0]}"
                                                                                method="post" data-usuario="{{ Auth::user()->name }}">
                                                                            <input type=hidden id="usuario" name="usuario" value="{{ Auth::user()->name }}">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                    </form>
                                                                    
                                                                    <i class="fas fa-trash" style="color: white"></i>
                                                                </button>
                                                                    <span class="tooltipUI bg-danger">Eliminar Laboratorio y conexion</span>
                                                                    <span class="text"></span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>`
                                    );

                                last = group;
                            }
                        });
                },
            });

                    // Mostrar el loader antes de enviar la solicitud AJAX
                    table.on('preXhr.dt', function() {
                        $('#wave-menu').show();
                    });

                    // Ocultar el loader después de recibir la respuesta AJAX
                    table.on('xhr.dt', function() {
                        $('#wave-menu').hide();
                    });

                        });



                       


    </script> --}}


<script>
     $(document).on('click', '.editar-laboratorio', function() {
                        // e.preventDefault();

                            var Idlaboratorio = $(this).data('laboratorio-id');
                            // var routeurlx = "/consultar_paciente') }}/" + pacienteID; // Ruta dinámica basada en el ID del paciente
                                // console.log(Idlaboratorio);
                        $.ajax({
                            url: 'laboratorio_edit/' + Idlaboratorio,
                            method:'GET',
                            // data: {id: $(this).attr(data-laboratorio-id)}
                            success: function(response) {
                                if(response.success)
                            {
                    
                                $("#idlaboratorio").val(response.idlaboratorio);
                                $("#editNombre").val(response.nombre);
                                $("#editNotas").val(response.notas);
                                $('#editModal').modal('show');
                            }
                            else
                            {
                                $.notify(response.mensaje,"error");
                            }

                            }
                        })
                    });

                    $(document).on('click', '.configurar-conexion', function() {
                        // e.preventDefault();
                            var Idlaboratorio = $(this).data('laboratorio-id');
                            var nombreLab = $(this).data('nombre');
                            
                            // var routeurlx = "/consultar_paciente') }}/" + pacienteID; // Ruta dinámica basada en el ID del paciente
                                // console.log(Idlaboratorio);
                        $.ajax({
                            url: 'laboratorio_editConexion/' + Idlaboratorio,
                            method:'GET',
                            // data: {id: $(this).attr(data-laboratorio-id)}
                            success: function(response) {
                                if(response.success)
                            {

                                $("#nombreLab").val(nombreLab);
                                $("#laboratorio_id").val(Idlaboratorio);
                                $("#Editbase_de_datos").val(response.BDs);
                                $("#Editservidor_sql").val(response.servidor);
                                $("#Editusuario_sql").val(response.usuario_sql);
                                $("#Editcontrasena_sql").val(response.contrasena);
                                $('#configurarModal').modal('show');
                            }
                            else
                            {
                                $.notify(response.mensaje,"error");
                            }

                            }
                        })
                    });


                    $(document).on('click', '.editar-cfdi', function() {
                        // e.preventDefault();

                        //ENVIAR ESTE TAMBIEN
                            var CFDIid = $(this).data('cfdi-id');


                            var Idlaboratorio = $(this).data('laboratorio-id');
                            // var routeurlx = "/consultar_paciente') }}/" + pacienteID; // Ruta dinámica basada en el ID del paciente
                                // console.log(CFDIid);
                        $.ajax({
                            url: 'laboratorio_editCFDI/' + Idlaboratorio,
                            method:'GET',
                            data: {
                                    CFDIid: CFDIid // Aquí envías el CFDIid como parte de los datos
                                },

                            // data: {id: $(this).attr(data-laboratorio-id)}
                            success: function(response) {
                                if(response.success)
                            {
                    
                                $("#idLab").val(Idlaboratorio);
                                $("#idSucursal").val(response.idSucursal);
                                $("#CFDIid").val(response.CFDIid);
                                $("#editSucursal").val(response.sucursal);
                                $("#editVersion").val(response.version);
                                $("#editTimesession").val(response.timesession);
                                $("#editMesMax").val(response.mesmax);
                                $("#editsesioneslimite").val(response.sesionlimite);
                                $('#editarModalCFDI').modal('show');

                            }
                            else
                            {
                                $.notify(response.mensaje,"error");
                            }

                            }
                        })
                    });


                    $(document).on('click', '.ver-cfdi', function() {
                        // e.preventDefault();

                        //ENVIAR ESTE TAMBIEN
                            var CFDIid = $(this).data('cfdi-id');


                            var Idlaboratorio = $(this).data('laboratorio-id');
                            // var routeurlx = "/consultar_paciente') }}/" + pacienteID; // Ruta dinámica basada en el ID del paciente
                                // console.log(CFDIid);
                        $.ajax({
                            url: 'laboratorio_mostrarCFDI/' + Idlaboratorio,
                            method:'GET',
                            data: {
                                    CFDIid: CFDIid // Aquí envías el CFDIid como parte de los datos
                                },

                            // data: {id: $(this).attr(data-laboratorio-id)}
                            success: function(response) {
                                if(response.success)
                            {
                                
                                
                            
                                $("#VerSucursal").val(response.sucursal);
                                $("#VerVersion").val(response.version);
                                $("#VerTimesession").val(response.timesession);
                                $("#VerMesMax").val(response.mesmax);
                                $("#Versesioneslimite").val(response.sesionlimite);
                                $('#mostrarModalCFDI').modal('show');

                            }
                            else
                            {
                                $.notify(response.mensaje,"error");
                            }

                            }
                        })
                    });


</script>



            <script>
                        $(document).on('click', '.ver-laboratorio', function() {
                            // e.preventDefault();

                            var Idlaboratorio = $(this).data('laboratorio-id');
                            // var routeurlx = "/consultar_paciente') }}/" + pacienteID; // Ruta dinámica basada en el ID del paciente
                                // console.log(Idlaboratorio);
                        $.ajax({
                            url: 'laboratorio_ver/' + Idlaboratorio,
                            method:'GET',
                            // data: {id: $(this).attr(data-laboratorio-id)}
                            success: function(response) {
                                if(response.success)
                            {
                    
                                
                                $("#VerNombre").val(response.VerNombre);
                                $("#VerNotas").val(response.VerNotas);
                                $('#VerModal').modal('show');
                            }
                            else
                            {
                                $.notify(response.mensaje,"error");
                            }

                            }
                        })
                    });
            </script>


    <script>
        function actualizarLaConexion() {
            var idLaboratorio = $('#laboratorio_id').val();
            var nombre = $('#nombreLab').val();
            var password_sql = $('#Editcontrasena_sql').val();
            var base_de_datos = $('#Editbase_de_datos').val();
            var servidor_sql = $('#Editservidor_sql').val();
            var usuario_sql = $('#Editusuario_sql').val();
            var usuario = $('#usuario').val(); 


            var formData = {
                '_method': 'PUT',
                'nombre': nombre,
                'usuario': usuario,
                'usuario_sql': usuario_sql,
                'servidor_sql': servidor_sql,
                'base_de_datos': base_de_datos,
                'password_sql': password_sql,
            };

            Swal.fire({
                title: "¿Estás seguro de enviar este formulario?",
                text: "Se actualizará la información del laboratorio.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, enviar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'PUT',
                        url: 'laboratorio/configsave/' + idLaboratorio,
                        contentType: 'application/json',
                        data: JSON.stringify(formData),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            try {
                                if (response.success) {
                                    Swal.fire(
                                        '¡Actualizado!',
                                        'El laboratorio ha sido actualizado correctamente.',
                                        'success'
                                    );
                                    $('#laboratorios-table').hide();

                                    $('#configurarModal').modal('hide');
                                    
                                    $('#laboratorios-table').DataTable().ajax.reload(() => {
                                        // Mostrar la tabla después de recargar
                                        $('#laboratorios-table').fadeIn(); // Puedes usar 'show()' si no deseas el efecto de desvanecimiento
                                    });

                                } else {
                                    Swal.fire(
                                        'Error',
                                        'Hubo un problema al actualizar el laboratorio.',
                                        'error'
                                    );
                                }
                            } catch (e) {
                                console.error('Error al procesar la respuesta JSON:', e);
                                Swal.fire(
                                    'Error',
                                    'Hubo un error al intentar actualizar el laboratorio.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error en la solicitud Ajax:', error);
                            Swal.fire(
                                'Error',
                                'Hubo un error al intentar actualizar el laboratorio.',
                                'error'
                            );
                        }
                    });
                } 
            });
        }
    </script>




    <script>
        function actualizarLaboratorio() {
            var idLaboratorio = $('#idlaboratorio').val(); // Obtener el ID del laboratorio del campo oculto
    
            var nombre = $('#editNombre').val();
            var notas = $('#editNotas').val();
            var usuario = $('#usuario').val();
            
    
            // Objeto con los datos a enviar al servidor
            var formData = {
                '_method': 'PUT', // Método HTTP simulado para Laravel
                'editNombre': nombre,
                'editNotas': notas,
                'usuario': usuario, // Ajusta esto para obtener el usuario de tu aplicación
            };
    
            // Mostrar SweetAlert para confirmar la acción
            Swal.fire({
                title: "¿Estás seguro de enviar este formulario?",
                text: "Se actualizará la información del laboratorio.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, enviar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Realizar la petición Ajax
                    $.ajax({
                        type: 'PUT', // Utilizamos POST en lugar de PUT (se especificará como PUT en Laravel)
                        url: 'laboratorio_update/' + idLaboratorio, // URL con el ID del laboratorio en la ruta
                        data: formData,
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Token CSRF para Laravel
                        },
                        success: function(response) {
                            // Manejar la respuesta del servidor
                            if (response.success) {
                                Swal.fire(
                                    '¡Actualizado!',
                                    'El laboratorio ha sido actualizado correctamente.',
                                    'success'
                                );
                               
                                $('#laboratorios-table').hide();

                                $('#editModal').modal('hide'); 

                                $('#laboratorios-table').DataTable().ajax.reload(() => {
                                    // Mostrar la tabla después de recargar
                                    $('#laboratorios-table').fadeIn(); // Puedes usar 'show()' si no deseas el efecto de desvanecimiento
                                });

                                // Cerrar el modal después de la actualización
                            } else {
                                Swal.fire(
                                    'Error',
                                    'Hubo un problema al actualizar el laboratorio.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error en la solicitud Ajax:', error);
                            Swal.fire(
                                'Error',
                                'Hubo un error al intentar actualizar el laboratorio.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>



<script>
    function ConfirmarActualizarCFDI() {
        var idLaboratorio = $('#idLab').val(); // Obtener el ID del laboratorio del campo oculto
        
        var CFDIid = $('#CFDIid').val();
        
        var sucursal = $('#editSucursal').val();
        var version = $('#editVersion').val();
        var timesession = $('#editTimesession').val();
        var mesmax = $('#editMesMax').val();
        var sesioneslimite = $('#editsesioneslimite').val();
        var idSucursal = $('#idSucursal').val();
        var usuario = $('#usuario').val();
        
        




        // Objeto con los datos a enviar al servidor
        var formData = {
            '_method': 'PUT', // Método HTTP simulado para Laravel
            'idSucursal': idSucursal,
            'CFDIid': CFDIid,
            'editSucursal': sucursal,
            'editVersion': version,
            'editTimesession': timesession,
            'editMesMax': mesmax,
            'editsesioneslimite': sesioneslimite,
            'usuario': usuario, 


            // 'idlaboratorio': idLaboratorio,
            // 'editNombre': nombre,
            // 'editNotas': notas,
            
        };
        
        // console.log(formData);

        // Mostrar SweetAlert para confirmar la acción
        Swal.fire({
            title: "¿Estás seguro de enviar este formulario?",
            text: "Se actualizará la información.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, enviar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                // Realizar la petición Ajax
                $.ajax({
                    type: 'PUT', // Utilizamos POST en lugar de PUT (se especificará como PUT en Laravel)
                    url: 'laboratorio_actualizarCFDI/' + idLaboratorio, 
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Token CSRF para Laravel
                    },
                    success: function(response) {
                        // Manejar la respuesta del servidor
                        if (response.success) {
                            Swal.fire(
                                '¡Actualizado!',
                                'La información ha sido actualizada correctamente.',
                                'success'
                            );
                           
                            
                            $('#laboratorios-table').hide();

                            $('#editarModalCFDI').modal('hide'); 

                            $('#laboratorios-table').DataTable().ajax.reload(() => {
                                    // Mostrar la tabla después de recargar
                                    $('#laboratorios-table').fadeIn(); // Puedes usar 'show()' si no deseas el efecto de desvanecimiento
                                });

                            // Cerrar el modal después de la actualización
                        } else {
                            Swal.fire(
                                'Error',
                                'Hubo un problema al actualizar.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la solicitud Ajax:', error);
                        Swal.fire(
                            'Error',
                            'Hubo un error al intentar actualizar.',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>



    <script>
            function confirmarCrear(formId) {
               // console.log("Función confirmarCrear llamada con el ID del formulario: " + formId);
                var form = document.getElementById(formId);
                if (!form) {
                  //  console.error("El formulario con el ID " + formId + " no fue encontrado.");
                    return;
                }

                Swal.fire({
                    title: "¿Quieres crear un nuevo registro?",
                    text: "Se Registrará la información",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, enviar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                 //   console.log("Resultado de la confirmación: ", result);
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
    </script>



    <script>
        function confirmarEliminar(formId) {
            var form = document.getElementById(formId);
            if (!form) {
                return;
            }

            Swal.fire({
                title: "¿Estás seguro de eliminar el laboratorio?",
                text: "Esta acción no se puede revertir",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.action,
                        method: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.success) {
                                // Recargar el DataTable


                                $('#laboratorios-table').hide();

                                $('#laboratorios-table').DataTable().ajax.reload(() => {
                                    // Mostrar la tabla después de recargar
                                        $('#laboratorios-table').fadeIn(); // Puedes usar 'show()' si no deseas el efecto de desvanecimiento
                                    });

                                Swal.fire(
                                    'Eliminado!',
                                    'El laboratorio ha sido eliminado.',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Hubo un problema al eliminar el laboratorio.',
                                    'error'
                                );
                            }
                        }
                    });
                }
            });
        }
    </script>



    <script>
                $(document).ready(function() {
                    $('#laboratorios-table').on('click', '.eliminar-cfdi', function() 
            {
                        var cfdi_id = $(this).data('cfdi-id');
                        var laboratorio_id = $(this).data('laboratorio-id');
                        var sucursal_id = $(this).data('sucursal-id');
                        var sucursal = $(this).data('sucursal');
                        var usuario = $(this).data('usuario');
                        var laboratorio = $(this).data('laboratorio-nombre');


                        // console.log("cfdi_id " ,cfdi_id);
                        // console.log("sucursal_id " ,sucursal_id);
                        // console.log("sucursal " ,sucursal);
                        // console.log("usuario " ,usuario);
                        // console.log("laboratorio " ,laboratorio);
                        // console.log("laboratorio_id " ,laboratorio_id);


                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "No podrás revertir esto!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, eliminarlo!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: 'laboratorio/borrarCFDI/' + cfdi_id,
                                    type: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        laboratorio_id: laboratorio_id,
                                        sucursal: sucursal,
                                        usuario: usuario,
                                        laboratorio_nombre: laboratorio,

                                    },
                                    success: function(response) {
                                        Swal.fire(
                                            'Eliminado!',
                                            response.message,
                                            'success'
                                        ).then(() => {

                                            $('#laboratorios-table').hide();

                                    $('#laboratorios-table').DataTable().ajax.reload(() => {
                                    // Mostrar la tabla después de recargar
                                    $('#laboratorios-table').fadeIn(); // Puedes usar 'show()' si no deseas el efecto de desvanecimiento
                                });

                                        });
                                    },
                                    error: function(xhr) {
                                        Swal.fire(
                                            'Error!',
                                            'Hubo un problema al intentar eliminar el elemento.',
                                            'error'
                                        );
                                        console.error(xhr.responseText);
                                    }
                                });
                            }
                        });
                    });
                });
    </script>


        <script>
            function revisarYActualizar(laboratorioId) {
                var formData = $('#formularioConfigurar' + laboratorioId).serialize();
                Swal.fire({
                    title: "¿Estás seguro de guardar la conexión?",
                    text: "Se probará la conexión antes de guardarla",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, guardar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'PUT',
                            url: '{{ route("laboratorio.revisaryguardar", ["id" => ":id"]) }}'.replace(':id', laboratorioId),
                            data: formData,

                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Conexión exitosa',
                                        text: response.message
                                        });
                                    formData.submit();

                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error al guardar la conexión',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                alert('Error al guardar la conexión: ' + xhr.responseText);
                            }
                        });
                    } 
                });
            }


            function probarConexionupdate(laboratorioId) {
                var formData = $('#formularioConfigurar' + laboratorioId).serialize();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("laboratorio.testconexion", ["id" => ":id"]) }}'.replace(':id', laboratorioId),
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Conexión exitosa',
                                text: response.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de conexión',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error al probar la conexión: ' + xhr.responseText);
                    }
                });
            }
        </script>



<script>
    function guardarDatos() {
        // Obtener valores de los campos del formulario
        var nombreLaboratorio = $('#nombre').val().trim();
        var servidor_sql = $('#servidor_sql').val().trim();
        var base_de_datos = $('#base_de_datos').val().trim();
        var usuario_sql = $('#usuario_sql').val().trim();
        var password_sql = $('#password_sql').val().trim();

        // Verificar si todos los campos están vacíos
        if (nombreLaboratorio === '' && servidor_sql === '' && base_de_datos === '' && usuario_sql === '' && password_sql === '') {
            // Mostrar alerta con SweetAlert indicando que se deben ingresar datos
            Swal.fire({
                icon: 'error',
                title: 'Ingresa los datos por favor',
                text: 'Por favor ingresa los datos antes de guardar!',
            });
            return; // Detener la función
        }

        // Mostrar confirmación con SweetAlert
        Swal.fire({
            title: '¿Estás seguro de guardar los datos?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, realizar la petición AJAX
                var formLaboratorio = $('#formularioCrear').serializeArray();
                var formConexion = $('#formularioCrearConexion').serializeArray();

                // Convertir los arrays serializados en objetos
                var datosCompletos = {};
                $.each(formLaboratorio, function(index, field) {
                    datosCompletos[field.name] = field.value;
                });
                $.each(formConexion, function(index, field) {
                    datosCompletos[field.name] = field.value;
                });

                // Realizar la petición AJAX
                $.ajax({
                    type: 'POST',
                    url: '{{ route("laboratorio.store") }}',
                    data: datosCompletos,
                    success: function(response) {
                        // Recargar la tabla


                        $('#laboratorios-table').hide();
                        
                        $('#laboratorios-table').DataTable().ajax.reload(() => {
                                    // Mostrar la tabla después de recargar
                                    $('#laboratorios-table').fadeIn(); // Puedes usar 'show()' si no deseas el efecto de desvanecimiento
                                });

                        // Mostrar mensaje de éxito con SweetAlert
                        Swal.fire(
                            '¡Guardado!',
                            'Se han guardado los datos correctamente.',
                            'success'
                        ).then(() => {
                            // Reiniciar los formularios
                            
                            // Reiniciar otros elementos del modal si es necesario
                            $('#modalTabs .nav-tabs .nav-link:first').tab('show'); // Reiniciar la pestaña activa

                            $('#formularioCrear')[0].reset();
                            $('#formularioCrearConexion')[0].reset();


                            // Ocultar el modal
                            $('#modalTabs').modal('hide');
                        });
                    },
                    error: function(error) {
                        console.error('Error al guardar datos: ' + JSON.stringify(error));
                    }
                });
            }
        });
    }
</script>



<script>
    // function probarConexion() {
        $(document).on('click', '.probarConexion', function() {
        var formData = $('#formularioCrearConexion').serialize();
        // console.log(formData); 

        var $button = $(this);
    
        $button.addClass('btn-disabled'); 

        $.ajax({
            type: 'POST',
            url: '{{ route("laboratorio.testnewconexion") }}',
            data: formData,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Conexión exitosa',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: response.message
                    });
                }
                $button.removeClass('btn-disabled');
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al probar la conexión',
                    text: xhr.responseText
                });
                $button.removeClass('btn-disabled');
                console.error(xhr.responseText);
            }
        });
    });
</script>



<script>
    $(document).on('click', '.testearConexion', function() {
    
        var formData = $('#ConfigForm').serializeArray();
        
        var $button = $(this);
    
        $button.addClass('btn-disabled'); 

        // Iterar sobre cada campo de formData y mostrarlo en la consola
        // console.log("Datos de formData:");
        // formData.forEach(function(item) {
        //     console.log(item.name + ": " + item.value);
        // });

        $.ajax({
            type: 'POST',
            url: '{{ route("laboratorio.testconexion") }}',
            data: $('#ConfigForm').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Conexión exitosa',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: response.message
                    });
                }
                $button.removeClass('btn-disabled');
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al probar la conexión',
                    text: xhr.responseText
                });
                $button.removeClass('btn-disabled');
                console.error(xhr.responseText);
            }
        });
    });
</script>




<script>
    $(document).ready(function() {
    $('#laboratorios-table').on('change', '.switch-veterinaria', function() {
        var $switch = $(this); // Guarda una referencia al interruptor
        $switch.addClass('switch-disabled'); // Añade la clase para deshabilitar visualmente el interruptor
        $switch.prop('disabled', true); // Desactiva el interruptor
        
        var laboratorioId = $switch.data('laboratorio-id');
        var cfdiId = $switch.data('cfdi-id');
        var usuario = $switch.data('usuario');
        var sucursal = $switch.data('sucursal');
        var nombre = $switch.data('laboratorio-nombre');

        var activado = $switch.prop('checked') ? '1' : '0';

        $.ajax({
            url: "{{ route('laboratorio.actualizarVeterinaria') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                laboratorio_id: laboratorioId,
                cfdi_id: cfdiId,
                usuario: usuario,
                sucursal: sucursal,
                VETERINARIA: activado,
                nombre: nombre,
            },
            success: function(response) {
                if (response.success) {
                    // La lógica para manejar una respuesta exitosa
                } else {
                    console.error('Error en la respuesta del servidor:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            },
            complete: function() {
                // Vuelve a habilitar el interruptor después de que la solicitud AJAX se complete
                $switch.removeClass('switch-disabled');
                $switch.prop('disabled', false);
            }
        });
    });
});

    
</script>


<script>

$(document).ready(function() {
    $('#laboratorios-table').on('change', '.switch-suscripcion', function() {
        var $switch = $(this); // Guarda una referencia al interruptor
        $switch.addClass('switch-disabled'); // Añade la clase para deshabilitar visualmente
        $switch.prop('disabled', true); // Desactiva el interruptor
        
        // Obtener los datos del switch que cambió
        var isChecked = $switch.prop('checked');
        var laboratorioId = $switch.data('laboratorio-id');
        var usuario = $switch.data('usuario');
        var sucursalId = $switch.data('sucursal-id');
        var laboratorioNombre = $switch.data('laboratorio-nombre');
        var cfdiId = $switch.data('cfdi-id');
        var sucursal = $switch.data('sucursal');
        var valor = isChecked ? 'ACTIVADO' : 'DESACTIVADO'; // Valor actual del switch

        // Objeto con los datos a enviar
        var data = {
            SUSCRIPCION: valor,
            nombre: laboratorioNombre,
            laboratorio_id: laboratorioId,
            cfdi_id: cfdiId,
            sucursal: sucursal,
            usuario: usuario
        };

        // Enviar la solicitud AJAX
        $.ajax({
            type: 'POST',
            url: '{{ route("actualizar.suscripcion") }}', 
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            success: function(response) {
                // Manejar respuesta exitosa si es necesario
                // console.log('Respuesta exitosa:', response);
            },
            error: function(xhr, status, error) {
                // Manejar errores de la solicitud AJAX
                console.error('Error al enviar la solicitud AJAX:', error);
            },
            complete: function() {
                // Vuelve a habilitar el interruptor después de la solicitud AJAX
                $switch.removeClass('switch-disabled'); // Elimina la clase de deshabilitación visual
                $switch.prop('disabled', false); // Vuelve a habilitar el interruptor
            }
        });
    });
});



</script>



    <script>


        $(document).on('click', '.consumosX', function() {
            // Obtén los IDs desde el botón
            var sucursalId = $(this).data('sucursal-id');
            var Idlaboratorio = $(this).data('laboratorio-id');
            var laboratorioNombre = $(this).data('nombre-laboratorio');
            var sucursalNombre = $(this).data('sucursal');

            $.ajax({
                            url: 'fecha/',
                            method:'GET',
                            // data: {id: $(this).attr(data-laboratorio-id)}
                            success: function(response) {
                                if(response.success)
                            {
                    
                            $("#LaboratorioNombre").val(laboratorioNombre);
                            $("#SucursalNombre").val(sucursalNombre);

                            $("#idlaboratorio").val(Idlaboratorio);
                            $("#idSucursal").val(sucursalId);
                               
                            $("#fechaInicio").val(response.fechaInicio);
                            $("#fechaFin").val(response.fechaFin);

                            $('#consumos').modal('show');
                            }
                            else
                            {
                                $.notify(response.mensaje,"error");
                            }

                            }
                        })



        });




        $(document).on('click', '.consultarOrdenes', function() {
    var $button = $(this);
    
    $button.addClass('btn-disabled'); 
 
    
    var token = $('meta[name="csrf-token"]').attr('content');
    var fechaInicio = $('#fechaInicio').val();
    var fechaFin = $('#fechaFin').val();
    var sucursalId = $("#idSucursal").val();
    var Idlaboratorio = $("#idlaboratorio").val();

    $("#ConsumosTotales").val('Calculando...');

    $.ajax({
        url: 'ConsumosXPeriodo/' + Idlaboratorio, // Asegúrate de que la URL sea correcta
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token // Incluye el token CSRF en los encabezados
        },
        data: {
            idSucursal: sucursalId,
            fechaInicio: fechaInicio,
            fechaFin: fechaFin
        },
        success: function(response) {
            
            if (response.success) {
                // Actualiza el campo con los datos recibidos
                $("#ConsumosTotales").val(response.resultados);
            } else {
                // Muestra un mensaje de error si la respuesta no es exitosa
                $("#ConsumosTotales").val('No disponible');
                
            }
            // Elimina la clase que desactiva el botón después de recibir la respuesta
            $button.removeClass('btn-disabled');
            
        },
        error: function(xhr, status, error) {
 
            // Elimina la clase que desactiva el botón también en caso de error
            $button.removeClass('btn-disabled');
            
        }
    });
});



    </script>


