@include('layouts.plantilla')

@extends('adminlte::page')

@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)
@section('plugins.Chartjs', true)

@section('title', 'PanelLab | Administrador de laboratorios')

@section('content_header')
@endsection

@section('content')







            <h1>Estadísticas</h1>

            <div class="container-fluid">
                <div class="row">
                    <!-- Laboratorios registrados -->
                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $laboratorioscontar ?? '' }}</h3>
                                <p>Laboratorios registrados</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ route('laboratorio.index') }}" class="small-box-footer">Verificar <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
            
                    <!-- Veterinarias registradas -->
                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $VETERINARIASI ?? '' }}</h3>
                                <p>Veterinarias registradas</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="{{ route('laboratorio.Cfdiveterinaria') }}" class="small-box-footer">Verificar <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
            
                    <!-- Sucursales registradas -->
                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $totalsuc ?? '' }}</h3>
                                <p>Sucursales registradas</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a class="small-box-footer"><i class="fas fa-exde"></i></a>
                        </div>
                    </div>
                </div>
            
                <div class="row">
                    <!-- Tarjeta Estados de sucursales actualmente -->
                    <div class="col-md-4 mb-3">
                        <div class="card text-light" style="background-color: #fbe3a0">
                            <div class="card-body d-flex flex-column align-items-center">
                                <div class="mb-3">
                                    <a type="button" href="{{ route('laboratorio.Labsinuso') }}" class="container-fluid comic-button-light-md d-flex align-items-center">
                                        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                                            <i class="far fa-frown p-2" ></i> 
                                        Sucursales <br> actualmente
                                    </a>
                                </div>
                                <div class="w-100 d-flex justify-content-center container-fluid">
                                    <div class="card p-2 container-fluid">
                                        <div class="card-body p-2 container-fluid">
                                            <canvas id="donutChart1" style="width: 80%; height: 200px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <!-- Tarjeta Estados de Suscripciones -->
                    <div class="col-md-4 ">
                        <div class="card text-dark" style="background-color: #00bf7d">
                            <div class="card-body d-flex flex-column align-items-center">
                                <div class="mb-3">
                                    <a type="button" href="{{ route('laboratorio.Cfdisuscripcion') }}" class="container-fluid comic-button-light-md d-flex align-items-center">
                                        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}"> 
                                        <i class="fas fa-power-off p-2"> </i>
                                            Estados de <br> Suscripciones
                                        
                                    
                                    </a>
                                </div>

                                <div class="w-100 d-flex justify-content-center container-fluid">
                                    <div class="card p-2 container-fluid">
                                        <div class="card-body p-2 container-fluid">
                                            <canvas id="donutChart3" style="width: 80%; height: 200px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <!-- Tarjeta Reporte de consumos mensuales -->
                    <div class="col-md-4 mb-3">
                        <div class="card text-dark" style="background-color: #8babf1">
                            <div class="card-body d-flex flex-column align-items-center">
                                <div class="mb-3">
                                    <a type="button" href="{{ route('laboratorio.filterCFDI') }}" class="container-fluid comic-button-light-md d-flex align-items-center">
                                        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                                        <i class="fas fa-file-alt p-2"></i> 
                                        Reporte de <br> consumos mensuales
                                    </a>
                                </div>
                                <div class="w-100 d-flex justify-content-center container-fluid">
                                    <div class="card p-2 container-fluid">
                                        <div class="card-body p-2 container-fluid">
                                            <canvas id="donutChart2" style="width: 80%; height: 200px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- <canvas id="myChart"></canvas> --}}

                    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <style>
                        canvas {
                            width: 100%;
                            height: 400px;
                        }
                    </style> --}}

                </div>
            </div>
            
            
                <!-- Scripts -->
                <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
                <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>




                

                    <script>
                        $(function () {
                            var totalSucursales = {{ $totalSucursales ?? ''}};
                            var cfdisValidosCount = {{ $cfdisValidosCount ?? ''}};

                            var data1 = {
                                labels: ['Sucursales en uso', 'Sucursales sin uso actualmente'],
                                datasets: [{
                                    label: 'Datos Dinámicos',
                                    data: [totalSucursales, cfdisValidosCount],
                                    backgroundColor: [
                                        'rgba(75, 192, 192, 0.6)',
                                        'rgba(255, 99, 132, 0.6)'

                                    ],
                                    hoverOffset: 4
                                }]
                            };

                            var options1 = {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        // onHover: handleHover,
                                        // onLeave: handleLeave,
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2);
                                            }
                                        }
                                    }
                                }
                            };

                            var ctx1 = document.getElementById('donutChart1').getContext('2d');
                            var myChart1 = new Chart(ctx1, {
                                type: 'pie',
                                data: data1,
                                options: options1
                            });
                        });

                        $(function () {
                            var suscripcionesActivas = {{ $activadosCount ?? ''}};
                            var suscripcionesDesactivadas = {{ $desactivadosCount ?? ''}};

                            var data3 = {
                                labels: ['Suscripciones Activas', 'Suscripciones Desactivadas'],
                                datasets: [{
                                    label: 'Conteo de Suscripciones',
                                    data: [suscripcionesActivas, suscripcionesDesactivadas],
                                    backgroundColor: [
                                        '#9b8bf4',
                                        '#faaf90',
                                    ],
                                    hoverOffset: 4
                                }]
                            };

                            var options3 = {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2);
                                            }
                                        }
                                    }
                                }
                            };

                            var ctx3 = document.getElementById('donutChart3').getContext('2d');
                            var myChart3 = new Chart(ctx3, {
                                type: 'doughnut',
                                data: data3,
                                options: options3
                            });
                        });


                        $(function () {
                            var total = {{ $sucursalsinexceso ?? ''}};
                            var excedidos = {{ $conteoMesAnterior ?? ''}};

                            var data2 = {
                                labels: ['Sin exceso de ordenes', 'Con exceso de ordenes'],
                                datasets: [{
                                    label: 'Conteo de Suscripciones',
                                    data: [total, excedidos],
                                    backgroundColor: [
                                        '#90d8b2',
                                        'rgb(54, 162, 235)'
                                    ],
                                    hoverOffset: 4
                                }]
                            };

                            var options2 = {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2);
                                            }
                                        }
                                    }
                                }
                            };

                            var ctx2 = document.getElementById('donutChart2').getContext('2d');
                            var myChart2 = new Chart(ctx2, {
                                type: 'doughnut',
                                data: data2,
                                options: options2
                            });
                        });
                    </script>


    {{-- <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    'Color 1', 'Color 2', 'Color 3', 'Color 4', 'Color 5', 
                    'Color 6', 'Color 7', 'Color 8', 'Color 9', 'Color 10', 
                    'Color 11', 'Color 12', 'Color 13', 'Color 14', 'Color 15', 
                    'Color 16', 'Color 17', 'Color 18', 'Color 19', 'Color 20'
                ],
                datasets: [{
                    label: 'Colores',
                    data: [10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10],
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCD56', '#4BC0C0', '#9966FF', 
                        '#FF9F40', '#E7E9ED', '#F54A55', '#007BFF', '#44BD32', 
                        '#FFC107', '#EE4266', '#192A56', '#32D47F', '#20BF6B', 
                        '#F7901E', '#C66ADE', '#ED1649', '#552580', '#34E77B'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script> --}}

@endsection