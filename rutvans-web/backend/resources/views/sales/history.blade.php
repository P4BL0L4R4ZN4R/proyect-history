@extends('adminlte::page')

@section('title', 'Historial de Ventas')

@section('content_header')
    <div class="d-flex justify-content-center">
        <form id="filter-form" method="GET" action="{{ route('sales.history') }}" class="mb-4 w-75">
            <div class="form-row align-items-end justify-content-center">
                <div class="col-md-5">
                    <label for="date">Seleccionar Fecha</label>
                    <input 
                        type="date" 
                        name="date" 
                        id="date" 
                        class="form-control"
                        value="{{ request('date', \Carbon\Carbon::now()->toDateString()) }}"
                    >
                </div>

                <div class="col-md-3 mt-2 mt-md-0">
                    <label>&nbsp;</label>
                    <div>
                        <a href="{{ route('sales.history') }}" class="btn btn-secondary w-100">Limpiar filtros</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@section('content')
    @if ($salesGrouped->isEmpty())
        <div class="alert alert-warning text-center w-100">
            No hay ventas registradas para esta fecha.
        </div>
    @endif

    <div class="row justify-content-center">
        @foreach ($salesGrouped as $date => $sales)
            <div class="col-md-10 mb-4">
                <div class="card shadow border-left-orange">
                    <div class="card-header bg-gradient-orange text-white">
                        <h5 class="mb-0">
                            Ventas del {{ \Carbon\Carbon::parse($date)->locale('es')->isoFormat('D [de] MMMM YYYY') }}
                        </h5>
                    </div>
                    <div class="card-body bg-white text-secondary">
                        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <i class="fas fa-file-invoice-dollar mr-2"></i>
                                {{ count($sales) }} venta{{ count($sales) > 1 ? 's' : '' }}
                            </div>
                            <div>
                                <i class="fas fa-dollar-sign mr-2 text-success"></i>
                                <strong>${{ number_format($sales->sum('amount'), 2) }}</strong>
                            </div>
                        </div>

                        @foreach ($sales as $sale)
                            <div class="sale-item d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 flex-wrap">
                                <div class="flex-grow-1">
                                    <div><strong>Folio:</strong> {{ $sale->folio }}</div>
                                    <div>
                                        <strong>Status:</strong>
                                        @if($sale->status === 'Completado')
                                            <span class="badge badge-success">{{ $sale->status }}</span>
                                        @elseif($sale->status === 'Cancelado')
                                            <span class="badge badge-danger">{{ $sale->status }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ $sale->status }}</span>
                                        @endif
                                    </div>
                                    <div><strong>Monto:</strong> ${{ number_format($sale->amount, 2) }}</div>
                                </div>
                                <div class="mt-2 mt-md-0 d-flex flex-column flex-md-row">
                                    <a href="#" class="btn btn-sm btn-light border-orange mr-md-2 mb-1 mb-md-0">
                                        Exportar Excel
                                    </a>
                                    <a href="#" class="btn btn-sm btn-light border-orange">
                                        Exportar PDF
                                    </a>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/history.css') }}">
<style>
    .border-left-orange {
        border-left: 5px solid #fd7e14;
    }
    .bg-gradient-orange {
        background: linear-gradient(to right, #fd7e14, #ffa94d);
    }
</style>
@endpush

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('date');
        if (dateInput) {
            dateInput.addEventListener('change', function () {
                document.getElementById('filter-form').submit();
            });
        }
    });
</script>
@endsection
