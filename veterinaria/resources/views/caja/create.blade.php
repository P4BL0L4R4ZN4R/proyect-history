@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Abrir Caja</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('caja.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="saldo_inicial" class="form-label">Saldo Inicial</label>
                            <input type="number" name="saldo_inicial" id="saldo_inicial" class="form-control" min="0" required placeholder="Ingresa el saldo inicial">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Abrir Caja</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
