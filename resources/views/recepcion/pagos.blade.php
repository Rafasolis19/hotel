<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos - Hotel</title>

    <style>
        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
        }

        main {
            max-width: 1200px;
            margin: 20px auto;
        }

        .cuadro {
            margin-bottom: 25px;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ddd;
            overflow-x: auto;
        }

        h1, h2 {
            color: #0f766e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #0f766e;
            color: white;
        }

        .boton {
            display: inline-block;
            padding: 9px 13px;
            border-radius: 6px;
            background: #0f766e;
            color: white;
            text-decoration: none;
        }

        .volver {
            display: inline-block;
            margin-top: 5px;
            padding: 10px 15px;
            border-radius: 6px;
            background: #dc2626;
            color: white;
            text-decoration: none;
        }

        .mensaje {
            margin-bottom: 20px;
            padding: 12px;
            background: #dcfce7;
            color: #166534;
            border-radius: 6px;
        }

        .vacio {
            color: #555;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <h1>💳 Pagos del hotel</h1>

    @if (session('success'))
        <div class="mensaje">{{ session('success') }}</div>
    @endif

    <section class="cuadro">
        <h2>Reservaciones pendientes de pago</h2>
        <p>Selecciona una reservación para registrar un pago completo, un abono o dividirlo entre dos métodos.</p>

        <table>
            <thead>
                <tr>
                    <th>Reservación</th>
                    <th>Huésped</th>
                    <th>Habitación</th>
                    <th>Total</th>
                    <th>Abonado</th>
                    <th>Saldo pendiente</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendientes as $reservacion)
                    <tr>
                        <td>{{ $reservacion->id }}</td>
                        <td>
                            {{ $reservacion->huesped_nombre }}
                            {{ $reservacion->apellido_paterno }}
                            {{ $reservacion->apellido_materno }}
                        </td>
                        <td>{{ $reservacion->habitacion_nombre }}</td>
                        <td>${{ number_format($reservacion->total, 2) }}</td>
                        <td>${{ number_format($reservacion->monto_pagado, 2) }}</td>
                        <td>${{ number_format($reservacion->saldo_pendiente, 2) }}</td>
                        <td>
                            <a
                                class="boton"
                                href="/pagos/crear/{{ $reservacion->id }}"
                            >
                                Registrar pago o abono
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="vacio">
                            No hay reservaciones pendientes de pago.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="cuadro">
        <h2>Pagos registrados</h2>
        <p>Historial de pagos realizados.</p>

        <table>
            <thead>
                <tr>
                    <th>Reservación</th>
                    <th>Huésped</th>
                    <th>Habitación</th>
                    <th>Método</th>
                    <th>Tarjeta</th>
                    <th>Monto pagado</th>
                    <th>Fecha de pago</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pagos as $pago)
                    <tr>
                        <td>{{ $pago->reservacion_id }}</td>
                        <td>
                            {{ $pago->huesped_nombre }}
                            {{ $pago->apellido_paterno }}
                            {{ $pago->apellido_materno }}
                        </td>
                        <td>{{ $pago->habitacion_nombre }}</td>
                        <td>{{ ucfirst($pago->metodo_pago) }}</td>
                        <td>
                            @if ($pago->metodo_pago === 'tarjeta')
                                {{ $pago->titular_tarjeta }}
                                <br>
                                **** {{ $pago->tarjeta_ultimos4 }}
                            @else
                                No aplica
                            @endif
                        </td>
                        <td>${{ number_format($pago->monto, 2) }}</td>
                        <td>{{ $pago->fecha_pago }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="vacio">
                            Todavía no hay pagos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <a href="{{ auth()->user()->role === 'administrador' ? route('admin') : '/recepcion' }}" class="volver">← Volver al panel</a>
</main>
</body>
</html>
