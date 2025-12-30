@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <h1>Registrar nuevo producto</h1>
@stop

@section('content')

<div class="form-group">
    <label>Nombre *</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $producto->nombre ?? '') }}" required>
</div>

<div class="form-group">
    <label>Stock</label>
    <input type="number" name="stock" class="form-control" value="{{ old('stock', $producto->stock ?? 0) }}">
</div>

<div class="form-group">
    <label>Precio Compra</label>
    <input type="number" step="0.01" name="precio_compra" class="form-control" value="{{ old('precio_compra', $producto->precio_compra ?? 0) }}">
</div>

<div class="form-group">
    <label>Precio Unitario</label>
    <input type="number" step="0.01" name="precio_unitario" class="form-control" value="{{ old('precio_unitario', $producto->precio_unitario ?? 0) }}">
</div>

<div class="form-group">
    <label>Precio Venta</label>
    <input type="number" step="0.01" name="precio_venta" class="form-control" value="{{ old('precio_venta', $producto->precio_venta ?? 0) }}">
</div>

<div class="form-group">
    <label>Código</label>
    <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $producto->codigo ?? '') }}">
</div>

<div class="form-group">
    <label>Descripción</label>
    <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion', $producto->descripcion ?? '') }}">
</div>

<div class="form-group">
    <label>Categoría</label>
    <input type="number" name="categoria" class="form-control" value="{{ old('categoria', $producto->categoria ?? '') }}">
</div>

@stop


