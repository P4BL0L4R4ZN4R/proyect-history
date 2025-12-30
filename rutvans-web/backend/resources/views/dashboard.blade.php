@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Panel Administrativo</h1>
        <div class="px-3 py-2 bg-light rounded shadow-sm">
        <div><strong>Fecha actual:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
        <div id="reloj" class="text-muted small font-weight-bold" style="font-size: 1rem;"></div>
    </div>

    </div>
@stop

@section('content')
    <!-- Tarjetas resumen -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="small-box" style="background-color: rgba(40,167,69,0.1); color: #155724;">
                <div class="inner">
                    <h3>${{ number_format($totalSales, 2) }}</h3>
                    <p>Ingresos de hoy</p>
                </div>
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="small-box" style="background-color: rgba(0,123,255,0.1); color: #858100;">
                <div class="inner">
                    <h3>{{ $activeDrivers }}</h3>
                    <p>Choferes activos</p>
                </div>
                <div class="icon"><i class="fas fa-user-tie"></i></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="small-box" style="background-color: rgba(23,162,184,0.1); color: #0c5460;">
                <div class="inner">
                    <h3>{{ $activeUnits }}</h3>
                    <p>Unidades registradas</p>
                </div>
                <div class="icon"><i class="fas fa-shuttle-van"></i></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="small-box" style="background-color: rgba(255,193,7,0.1); color: #856404;">
                <div class="inner">
                    <h3>{{ $todayTrips }}</h3>
                    <p>Viajes de hoy</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>
    </div>

    <!-- Filtro + Gráficos como sección unificada -->
    <div class="card shadow-sm border rounded-lg mb-4" style="border: 1px solid #dee2e6;">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Filtrar gráficos por rango de fechas</strong>
            <form method="GET" class="form-inline m-0">
                <label class="mr-2 mb-0"><strong>De:</strong></label>
                <input type="date" name="start_date" class="form-control mr-3" value="{{ $startDate->toDateString() }}" style="width: 160px;">

                <label class="mr-2 mb-0"><strong>A:</strong></label>
                <input type="date" name="end_date" class="form-control mr-3" value="{{ $endDate->toDateString() }}" style="width: 160px;">

                <button class="btn btn-primary mr-2">Aplicar filtro</button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Borrar filtro</a>
            </form>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">
                <strong>Visualizando datos del:</strong>
                @if($startDate->equalTo($endDate))
                    {{ $startDate->format('d/m/Y') }}
                @else
                    {{ $startDate->format('d/m/Y') }} al {{ $endDate->format('d/m/Y') }}
                @endif
            </p>

            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">Ventas por día</div>
                        <div class="card-body p-0" style="height:250px; position: relative;">
                            <canvas id="barChart" width="100%" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-warning text-dark">Ventas por chofer</div>
                        <div class="card-body p-0" style="height:250px; position: relative;">
                            <canvas id="pieChart" width="100%" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dates   = {!! json_encode($salesData->pluck('date')) !!};
    const vals    = {!! json_encode($salesData->pluck('total')) !!};
    const dLabels = {!! json_encode($pieData->pluck('driver')) !!};
    const dVals   = {!! json_encode($pieData->pluck('total')) !!};

    new Chart(document.getElementById('barChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: dates,
            datasets: [{
                label: 'Ventas ($)',
                data: vals,
                backgroundColor: 'rgba(54,162,235,0.3)',
                borderColor:     'rgba(54,162,235,0.6)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } },
                y: { beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('pieChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: dLabels,
            datasets: [{
                data: dVals,
                backgroundColor: [
                    'rgba(255,99,132,0.3)',
                    'rgba(54,162,235,0.3)',
                    'rgba(255,206,86,0.3)',
                    'rgba(75,192,192,0.3)',
                    'rgba(153,102,255,0.3)'
                ],
                borderColor: [
                    'rgba(255,99,132,0.6)',
                    'rgba(54,162,235,0.6)',
                    'rgba(255,206,86,0.6)',
                    'rgba(75,192,192,0.6)',
                    'rgba(153,102,255,0.6)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
<script>
    function actualizarReloj() {
        const reloj = document.getElementById("reloj");
        const ahora = new Date();
        const horas = ahora.getHours().toString().padStart(2, '0');
        const minutos = ahora.getMinutes().toString().padStart(2, '0');
        const segundos = ahora.getSeconds().toString().padStart(2, '0');
        reloj.textContent = Hora actual: ${horas}:${minutos}:${segundos};
    }

    setInterval(actualizarReloj, 1000);
    actualizarReloj(); // ejecutar al cargar
</script>

@stop
