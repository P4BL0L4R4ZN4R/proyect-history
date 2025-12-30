<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Unidades Registradas - PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #454545;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-table td {            
            vertical-align: top;
            border: none !important;
        }

        .logo {
            height: 60px;
        }

        .header-info {
            text-align: right;
            font-size: 12px;
        }

        h2 {
            color: #FF6000;
            margin-bottom: 10px;
            text-align: center;
            border-bottom: 2px solid #FFA559;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #FF6000;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #FFE6C7;
        }

    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <img src="{{ public_path('asset/LogoRutvans.png') }}" class="logo" alt="RutVans Logo">
            </td>
            <td class="header-info">
                <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                <p><strong>Generado por:</strong> {{ auth()->user()->name }}</p>
            </td>
        </tr>
    </table>

    <h2>Reporte de Unidades Registrados</h2>

    <table>
        <thead>
            <tr>
                <th>Conductor</th>
                <th>Placa</th>
                <th>Capacidad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($unidades as $unit)
                @foreach ($unit->drivers as $driver)
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
