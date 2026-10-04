<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservaciones - Hotel</title>

    <style>
        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
        }

        .contenedor {
            max-width: 1200px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ddd;
            overflow-x: auto;
        }

        h1 {
            color: #0f766e;
        }

        .mensaje {
            padding: 12px;
            margin-bottom: 20px;
            color: #166534;
            background: #dcfce7;
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            color: white;
            background: #0f766e;
        }

        .volver {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            color: white;
            background: #dc2626;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>

<body>
    <div class="contenedor">
        <h1>📅 Reservaciones</h1>

        @if (session('success'))
            <div class="mensaje">{{ session('success') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>N.º</th>
                    <th>Huésped</th>
                    <th>Habitación asignada</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservaciones as $reservacion)
                    <tr>
                        <td>{{ $reservacion->id }}</td>
                        <td>
                            {{ $reservacion->huesped_nombre }}
                            {{ $reservacion->apellido_paterno }}
                            {{ $reservacion->apellido_materno }}
                        </td>
                        <td>{{ $reservacion->habitacion_nombre }} · N.º {{ $reservacion->habitacion_numero }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($reservacion->fecha_entrada)->format('d/m/Y') }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($reservacion->fecha_salida)->format('d/m/Y') }}</td>
                        <td>${{ number_format($reservacion->total, 2) }}</td>
                        <td>{{ ucfirst($reservacion->estado) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Todavía no hay reservaciones registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <a href="/recepcion" class="volver">← Volver al panel</a>
    </div>
</body>
</html>
