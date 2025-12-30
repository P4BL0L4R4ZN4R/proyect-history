<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Unidades Registradas</title>
</head>
<body>

    <div class="header">
        <div class="logo">
            <img src="{{ public_path('asset/LogoRutvans.png') }}" alt="RutVans Logo" width="130">
        </div>
        <div class="info">
            <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>
            <p><strong>Generado por:</strong> {{ auth()->user()->name }}</p>
        </div>
    </div>

    <h3>Reporte de Unidades Registradas</h3>

    <table>
        <thead>
            <tr>
                <th>Conductor</th>
                <th>Placa</th>
                <th>Capacidad</th>
                <th>Estatus</th>
            </tr>
        </thead>
        <tbody>
            @foreach($units as $unit)
                @foreach($unit->drivers as $driver)
                    <tr>
                        <td>{{ $driver->user->name }}</td>
                        <td>{{ $unit->plate }}</td>
                        <td>{{ $unit->capacity }}</td>
                        <td>{{ $driver->pivot->status }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

</body>
</html>
