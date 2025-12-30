<div class="mb-3">
    <label>Remitente</label>
    <input type="text" name="sender_name" value="{{ old('sender_name', $envio->sender_name ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label>Destinatario</label>
    <input type="text" name="receiver_name" value="{{ old('receiver_name', $envio->receiver_name ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label>Total</label>
    <input type="number" name="total" step="0.01" value="{{ old('total', $envio->total ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label>Ruta</label>

</div>

<div class="mb-3">
    <label>Horario</label>

</div>

<div class="mb-3">
    <label>Unidad</label>

</div>

<div class="mb-3">
    <label>Foto</label>
    <input type="file" name="photo" class="form-control">
</div>
