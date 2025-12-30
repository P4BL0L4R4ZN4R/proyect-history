<!DOCTYPE html>
<html>

<head>
    <title>Exportación de Localidades</title>
    <style>
        body {
            font-family: "Georgia", serif;
            /* Letra formal y profesional */
            margin: 20px;
            color: #333;
            background-color: #fff;
            /* Fondo blanco */
        }

        h1 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
            color: #ff6600;
            /* Naranja vibrante */
        }

        .info-box {
            display: flex;
            justify-content: space-between;
            border: 1px solid #000;
            /* Bordes negros */
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff;
            /* Fondo blanco */
        }

        .info-column {
            width: 48%;
        }

        .info-column h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #ff6600;
            /* Naranja vibrante */
            text-transform: uppercase;
        }

        .info-column p {
            margin: 4px 0;
            font-size: 14px;
            color: #000;
            /* Texto negro */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #fff;
            /* Fondo blanco */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Sombra ligera */
        }

        th,
        td {
            border: 1px solid #000;
            /* Bordes negros */
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #ff6600;
            /* Fondo naranja */
            color: #fff;
            /* Texto blanco */
            font-weight: bold;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
            /* Filas alternas de gris claro */
        }

        tr:hover {
            background-color: #ffe6cc;
            /* Naranja suave para hover */
        }

        .total {
            font-weight: bold;
            text-align: center;
            background-color: #ffe6cc;
            /* Fondo naranja suave */
            color: #000;
            /* Texto negro */
        }
    </style>
</head>

<body>
    <h1>Exportación de Localidades</h1>

    <!-- Información General -->
    <div class="info-box">
        <div class="info-column">
            <h3>Información General</h3>
            <p><strong>Fecha de Consulta:</strong> {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
            <p><strong>Persona que Consulta:</strong> {{ Auth::user()->name ?? 'N/A' }}</p>
        </div>
        <div class="info-column">
            <h3>Detalles del Filtro</h3>
            <p><strong>Fecha Consultada:</strong> {{ $fechaConsultada ?? 'No especificada' }}</p>
            <p><strong>Filtros:</strong> {{ $filtroBuscado ?? 'Ninguno' }}</p>
            <p><strong>Total de Datos:</strong> {{ $totalDatos }}</p>
            <p><strong>Datos Mostrados:</strong> {{ $datosMostrados }}</p>
        </div>
    </div>

    <!-- Tabla de Localidades -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Longitud</th>
                <th>Latitud</th>
                <th>Localidad</th>
                <th>Calle</th>
                <th>Código Postal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($localidades as $localidad)
                <tr>
                    <td>{{ $localidad->id }}</td>
                    <td>{{ $localidad->longitude }}</td>
                    <td>{{ $localidad->latitude }}</td>
                    <td>{{ $localidad->locality }}</td>
                    <td>{{ $localidad->street }}</td>
                    <td>{{ $localidad->postal_code }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="total">No se encontraron localidades con los filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
