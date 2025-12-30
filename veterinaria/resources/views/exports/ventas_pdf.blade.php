@php
    $totalVentas = 0;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f2f2f2; text-align: left; padding: 8px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { margin-top: 30px; font-size: 8pt; text-align: center; color: #666; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reporte de Ventas</h2>
        @if($fechaInicio || $fechaFin)
            <p>
                @if($fechaInicio && $fechaFin)
                    Del {{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}
                @elseif($fechaInicio)
                    Desde el {{ $fechaInicio->format('d/m/Y') }}
                @else
                    Hasta el {{ $fechaFin->format('d/m/Y') }}
                @endif
            </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Fecha</th>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">P. Unitario</th>
                <th class="text-right">Subtotal</th>
                <th class="text-center">Tipo Pago</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                @php $totalVentas += $venta->venta->total; @endphp
                <tr>
                    <td>{{ $venta->venta->folio }}</td>
                    <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $venta->producto->nombre }}</td>
                    <td class="text-center">{{ $venta->cantidad }}</td>
                    <td class="text-right">${{ number_format($venta->precio_unitario, 2) }}</td>
                    <td class="text-right">${{ number_format($venta->subtotal, 2) }}</td>
                  <td class="text-center">
                        @if ($venta->venta?->tipo_pago == 1)
                            Efectivo
                        @elseif ($venta->venta?->tipo_pago == 2)
                            Tarjeta
                        @elseif ($venta->venta?->tipo_pago == 3)
                            Transferencia
                        @else
                            ---
                        @endif
                    </td>
                    <td class="text-right">${{ number_format($venta->venta->total, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7" class="text-right"><strong>Total General:</strong></td>
                <td class="text-right">${{ number_format($totalVentas, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} |
        Página {PAGENO} de {nbpg}
    </div>
</body>
</html>
