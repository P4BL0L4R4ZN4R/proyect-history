@extends('adminlte::page')

@section('title', 'Panel de Ventas')

@section('content_header')

@stop

@section('content')
        {{-- Primera fila: Cards de resumen --}}
        <div class="row g-4 mb-5">
            {{-- Ventas del Día (Dinero) --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-lg h-100 hover-lift bg-gradient-primary text-white">
                    <div class="card-body position-relative p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h3 class="h1 fw-bold mb-1" id="ventasHoy" >${{ number_format($ventasHoy ?? 0, 2) }}</h3>
                                <p class="mb-0 opacity-75">Ventas del Día</p>
                            </div>
                            <div class="icon-xl  bg-opacity-20 rounded-3">
                                <i class="fas fa-dollar-sign fs-3"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-4">
                            <span class="badge  bg-opacity-20 fs-6 px-3 py-2">
                                <i class="fas fa-arrow-up me-1"></i> Hoy
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ventas del Mes --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-lg h-100 hover-lift bg-gradient-warning text-white">
                    <div class="card-body position-relative p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h3 class="h1 fw-bold mb-1" id="totalVentasMes">${{ number_format($ventasMensuales ?? 0, 2) }}</h3>
                                <p class="mb-0 opacity-75">Ventas del Mes</p>
                            </div>
                            <div class="icon-xl  bg-opacity-20 rounded-3">
                                <i class="fas fa-chart-bar fs-3"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-4">
                            <span class="badge  bg-opacity-20 fs-6 px-3 py-2">
                                <i class="fas fa-calendar me-1"></i> Mes Actual: {{ $mesactual ?? 'NA' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Efectivo en Caja --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-lg h-100 hover-lift bg-gradient-success text-white">
                    <div class="card-body position-relative p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h3 class="h1 fw-bold mb-1">${{ number_format($efectivoCaja ?? 0, 2) }}</h3>
                                <p class="mb-0 opacity-75">Efectivo en Caja</p>
                            </div>
                            <div class="icon-xl  bg-opacity-20 rounded-3">
                                <i class="fas fa-wallet fs-3"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-4">
                            <span class="badge  bg-opacity-20 fs-6 px-3 py-2">
                                <i class="fas fa-money-bill-wave me-1"></i> Disponible
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ventas del Día (Tickets) --}}
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-lg h-100 hover-lift bg-gradient-info text-white">
                    <div class="card-body position-relative p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h3 class="h1 fw-bold mb-1" id="ticketsHoy">{{ $ticketsHoy ?? 0 }}</h3>
                                <p class="mb-0 opacity-75">Ventas Emitidas Hoy</p>
                            </div>
                            <div class="icon-xl  bg-opacity-20 rounded-3">
                                <i class="fas fa-receipt fs-3"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-4">
                            <span class="badge  bg-opacity-20 fs-6 px-3 py-2">
                                <i class="fas fa-file-invoice me-1"></i> Tickets
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Segunda fila: Gráfico de ventas y productos más vendidos --}}
        <div class="row g-4 mt-2">
            {{-- Gráfico de ventas --}}
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header  py-3 border-0 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i class="fas fa-chart-line me-2"></i>Ventas de la Semana
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="chart-container" style="height: 320px;">
                            <canvas id="graficoVentas"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Productos más vendidos --}}
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header  py-3 border-0 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-success">
                            <i class="fas fa-fire me-2"></i>Productos Más Vendidos
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $productosMasVendidos = $productosMasVendidos ?? collect();
                        @endphp
                        @if($productosMasVendidos->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($productosMasVendidos as $index => $producto)
                                    <div class="list-group-item border-0 py-3 px-4 {{ $index % 2 === 0 ? 'bg-light' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-semibold text-dark">{{ $producto->nombre }}</h6>
                                                <small class="text-muted">Código: {{ $producto->codigo ?? 'N/A' }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success bg-gradient fs-6 px-3 py-2">
                                                    {{ $producto->total_vendido }}
                                                </span>
                                                <small class="d-block text-muted mt-1">unidades</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="text-muted mb-3">
                                    <i class="fas fa-box-open fa-3x opacity-25"></i>
                                </div>
                                <p class="text-muted mb-0">No hay ventas registradas</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
@stop


    @section('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ventasSemana = {!! json_encode($ventasSemana ?? [0,0,0,0,0,0,0]) !!};
                    const ventasSemanaNums = ventasSemana.map(v => Number(v) || 0);

                    const canvas = document.getElementById('graficoVentas');
                    if (!canvas) return;

                    if (canvas._chartInstance) {
                        try { canvas._chartInstance.destroy(); } catch(e) {}
                        canvas._chartInstance = null;
                    }

                    const ctx = canvas.getContext('2d');
                    canvas._chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
                            datasets: [{
                                label: 'Ventas ($)',
                                data: ventasSemanaNums,
                                backgroundColor: [
                                    'rgba(13, 110, 253, 0.8)',
                                    'rgba(13, 110, 253, 0.8)',
                                    'rgba(13, 110, 253, 0.8)',
                                    'rgba(13, 110, 253, 0.8)',
                                    'rgba(25, 135, 84, 0.8)',
                                    'rgba(25, 135, 84, 0.8)',
                                    'rgba(220, 53, 69, 0.8)'
                                ],
                                borderWidth: 0,
                                borderRadius: 6,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    padding: 12,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(context) {
                                            return '$' + context.parsed.y.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>

            <script>
                // Opción B: Cargar datos individualmente (RECOMENDADO)
                function cargarDatosIndividuales() {
                    // Mostrar todos los loaders
                    $('.loading').show();

                    // Cargar en paralelo
                    cargarDatoIndividual('/dashboard/total-ventas-mes', '#totalVentasMes', true);
                    cargarDatoIndividual('/dashboard/ventas-hoy', '#ventasHoy', true);
                    cargarDatoIndividual('/dashboard/total-productos', '#totalProductos');
                    cargarDatoIndividual('/dashboard/productos-bajo-stock', '#productosBajoStock');
                    cargarDatoIndividual('/dashboard/productos-proximos-caducar', '#productosProximosCaducar');
                    cargarDatoIndividual('/dashboard/promedio-venta-diaria', '#promedioVentaDiaria', true);
                    cargarDatoIndividual('/dashboard/ventas-del-dia', '#ticketsHoy');
                    // cargarCorteActual();
                }

                function cargarDatoIndividual(url, elementoId, esMoneda = false) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            let valor = esMoneda ? '$' + response.dato : response.dato;
                            $(elementoId).text(valor);
                            $(elementoId).closest('.card').find('.loading').hide();
                        },
                        error: function() {
                            $(elementoId).text('Error');
                            $(elementoId).closest('.card').find('.loading').hide();
                        }
                    });
                }

                // function cargarCorteActual() {
                //     $.ajax({
                //         url: '/dashboard/corte-actual',
                //         type: 'GET',
                //         success: function(response) {
                //             if (response.dato) {
                //                 $('#corteActual').text('$' + response.dato.saldo_inicio);
                //             } else {
                //                 $('#corteActual').text('No hay corte abierto');
                //             }
                //             $('#corteActual').closest('.card').find('.loading').hide();
                //         },
                //         error: function() {
                //             $('#corteActual').text('Error');
                //             $('#corteActual').closest('.card').find('.loading').hide();
                //         }
                //     });
                // }

                // Al cargar la página
                $(document).ready(function() {
                    // Elegir una opción:
                    // cargarDashboardCompleto(); // Opción A
                    cargarDatosIndividuales(); // Opción B (Recomendada)
                });
            </script>



    @stop

    @push('styles')
        <style>
                .hover-lift {
                    transition: all 0.3s ease-in-out;
                }
                .hover-lift:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
                }
                .icon-xl {
                    width: 60px;
                    height: 60px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .bg-gradient-primary {
                    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
                }
                .bg-gradient-warning {
                    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
                }
                .bg-gradient-success {
                    background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
                }
                .bg-gradient-info {
                    background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
                }
                /* Mejor espaciado general */
                .card-body.p-4 {
                    padding: 1.5rem !important;
                }
                .mb-5 {
                    margin-bottom: 3rem !important;
                }
                .mt-2 {
                    margin-top: 1rem !important;
                }
        </style>
    @endpush
