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

<div class="row">
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
            <h3>{{ $laboratorioscontar ?? '' }}</h3>

          <p>Laboratorios registrados</p>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
        <a href=" {{ route('laboratorio.index') }} " class="small-box-footer">Verificar <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-success">
        <div class="inner">
          <h3>53<sup style="font-size: 20px">%</sup></h3>

          <p>Bounce Rate</p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-warning">
        <div class="inner">
          <h3>{{ $totalActiveSessions ?? '' }} </h3>
            
          <p>Sesiones abiertas</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
        {{-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-primary">
        <div class="inner">
          <h3>{{ $usuariosactivos ?? ''}}</h3>

          <p>Usuarios Activos en PanelLab</p>
        </div>
        <div class="icon">
          <i class="ion ion-pie-graph"></i>
        </div>
        <a href="{{ route('usuario.index') }}" class="small-box-footer">Verificar <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
  </div>






<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <canvas id="donutChart1" style="height: 200px;"></canvas>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col-md-6 -->

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <canvas id="donutChart2" style="height: 200px;"></canvas>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col-md-6 -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-md-6">
        <h4>Estados de sucursales actualmente</h4>
        <p>Sucursales en uso actualmente: {{ $activadosCount }}</p>
        <p>Sucursales sin uso actualmente: {{ $desactivadosCount }}</p>
    </div>
    <div class="col-md-6">
        <h4>Estados de Suscripciones</h4>
        <p>Suscripciones Activas: {{ $activadosCount }}</p>
        <p>Suscripciones Desactivadas: {{ $desactivadosCount }}</p>
    </div>
</div>

<script>
    $(function () {
        var totalSucursales = {{ $totalSucursales }};
        var cfdisValidosCount = {{ $cfdisValidosCount }};

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
            type: 'doughnut',
            data: data1,
            options: options1
        });
    });

    $(function () {
        var suscripcionesActivas = {{ $activadosCount }};
        var suscripcionesDesactivadas = {{ $desactivadosCount }};

        var data2 = {
            labels: ['Suscripciones Activas', 'Suscripciones Desactivadas'],
            datasets: [{
                label: 'Conteo de Suscripciones',
                data: [suscripcionesActivas, suscripcionesDesactivadas],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(255, 99, 132, 0.6)'
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

<div style="width: 800px; margin: auto;">
    <canvas id="myChart"></canvas>
</div>

<script>
    // Colores de Chart.js
    const coloresChartJS = [
        'rgb(255, 99, 132)',
        'rgb(54, 162, 235)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(153, 102, 255)',
        'rgb(255, 159, 64)',
        'rgb(231, 233, 237)',
        'rgb(245, 74, 85)',
        'rgb(0, 123, 255)',
        'rgb(68, 189, 50)',
        'rgb(255, 193, 7)',
        'rgb(238, 66, 102)',
        'rgb(25, 42, 86)',
        'rgb(50, 212, 127)',
        'rgb(32, 191, 107)',
        'rgb(247, 144, 30)',
        'rgb(198, 106, 222)',
        'rgb(237, 22, 73)',
        'rgb(85, 37, 128)',
        'rgb(52, 231, 123)'
    ];

    // Datos para el gráfico
    const data = {
        labels: coloresChartJS.map((color, index) => `Color ${index + 1}`),
        datasets: [{
            label: 'Colores de Chart.js',
            data: new Array(coloresChartJS.length).fill(1), // Valor dummy para cada barra
            backgroundColor: coloresChartJS,
            borderWidth: 1
        }]
    };

    // Opciones del gráfico
    const options = {
        indexAxis: 'y', // Mostrar barras horizontalmente
        plugins: {
            legend: {
                display: true,
                position: 'right'
            }
        }
    };

    // Configuración del gráfico
    const config = {
        type: 'bar',
        data: data,
        options: options
    };

    // Esperar a que se cargue el DOM antes de crear el gráfico
    document.addEventListener('DOMContentLoaded', () => {
        var myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
    });
</script>

@endsection