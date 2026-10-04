<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de pagos - Hotel</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; background: #f4f6f9; color: #1f2937; }
        main { max-width: 1300px; margin: 15px auto; padding: 25px; background: white; border-radius: 10px; box-shadow: 0 2px 8px #ddd; overflow-x: auto; }
        h1 { color: #172554; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 11px; border: 1px solid #ddd; text-align: left; }
        th { background: #172554; color: white; }
        .aviso { padding: 12px; background: #eff6ff; color: #1e3a8a; border-radius: 6px; }
        .volver { display: inline-block; margin-top: 20px; padding: 10px 15px; border-radius: 6px; background: #dc2626; color: white; text-decoration: none; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <h1>Historial de pagos</h1>
    <p>Consulta los pagos junto con el huésped, la habitación y el método utilizado.</p>
    <p class="aviso">Por seguridad, las tarjetas se identifican únicamente por sus últimos cuatro dígitos. No se almacena ni muestra el número completo ni el código de seguridad.</p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Reservación</th>
                <th>Huésped</th>
                <th>Habitación</th>
                <th>Método</th>
                <th>Datos de tarjeta</th>
                <th>Monto</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pagos as $pago)
                <tr>
                    <td>{{ $pago->fecha_pago }}</td>
                    <td>#{{ $pago->reservacion_id }}</td>
                    <td>{{ $pago->huesped_nombre }} {{ $pago->apellido_paterno }} {{ $pago->apellido_materno }}</td>
                    <td>{{ $pago->habitacion_nombre }}</td>
                    <td>{{ ucfirst($pago->metodo_pago) }}</td>
                    <td>
                        @if ($pago->metodo_pago === 'tarjeta')
                            Titular: {{ $pago->titular_tarjeta ?: 'No registrado' }}<br>
                            Tarjeta: **** {{ $pago->tarjeta_ultimos4 ?: '----' }}<br>
                            Vence: {{ $pago->vencimiento_tarjeta ?: 'No registrado' }}
                        @else
                            No aplica
                        @endif
                    </td>
                    <td>${{ number_format($pago->monto, 2) }}</td>
                    <td>{{ ucfirst($pago->estado) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No hay pagos registrados todavía.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a class="volver" href="{{ route('admin') }}">← Volver al panel</a>
</main>
</body>
</html>
