<table style="width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <thead>
        <tr style="background-color: #ff6600; color: #fff; text-align: center;">
            <th style="border: 1px solid #000; padding: 10px;">ID</th>
            <th style="border: 1px solid #000; padding: 10px;">Longitud</th>
            <th style="border: 1px solid #000; padding: 10px;">Latitud</th>
            <th style="border: 1px solid #000; padding: 10px;">Localidad</th>
            <th style="border: 1px solid #000; padding: 10px;">Calle</th>
            <th style="border: 1px solid #000; padding: 10px;">Código Postal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($localidades as $localidad)
            <tr style="background-color: {{ $loop->index % 2 == 0 ? '#f2f2f2' : '#fff' }};">
                <td style="border: 1px solid #000; padding: 10px; text-align: center;">{{ $localidad->id }}</td>
                <td style="border: 1px solid #000; padding: 10px;">{{ $localidad->longitude }}</td>
                <td style="border: 1px solid #000; padding: 10px;">{{ $localidad->latitude }}</td>
                <td style="border: 1px solid #000; padding: 10px;">{{ $localidad->locality }}</td>
                <td style="border: 1px solid #000; padding: 10px;">{{ $localidad->street }}</td>
                <td style="border: 1px solid #000; padding: 10px;">{{ $localidad->postal_code }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
