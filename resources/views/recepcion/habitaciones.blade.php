<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de habitaciones - Hotel</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f4f6; color: #1f2937; }
        header { padding: 22px 30px; background: #0f766e; color: white; font-size: 25px; font-weight: bold; }
        main { max-width: 1350px; margin: auto; padding: 28px; }
        .intro, .grupo { padding: 22px; margin-bottom: 22px; border-radius: 12px; background: white; box-shadow: 0 2px 8px #ddd; }
        h1, h2 { margin-top: 0; color: #0f766e; }
        .resumen { color: #64748b; }
        .habitaciones { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 14px; }
        .habitacion { padding: 17px; border: 1px solid #e2e8f0; border-radius: 10px; }
        .numero { display: block; margin-bottom: 9px; font-size: 25px; font-weight: bold; color: #0f766e; }
        .estado { display: inline-block; margin: 6px 0 10px; padding: 6px 9px; border-radius: 20px; font-size: 13px; font-weight: bold; }
        .libre { background: #dcfce7; color: #166534; }
        .ocupada { background: #fee2e2; color: #991b1b; }
        .reservada { background: #fef3c7; color: #92400e; }
        .inactiva { background: #e2e8f0; color: #475569; }
        .detalle { min-height: 45px; margin: 4px 0; color: #475569; font-size: 14px; }
        .boton, .volver { display: inline-block; margin-top: 10px; padding: 10px 14px; border-radius: 6px; background: #0f766e; color: white; text-decoration: none; }
        .volver { background: #dc2626; }
        .mensaje { padding: 12px; margin-bottom: 18px; border-radius: 7px; background: #dcfce7; color: #166534; }
        @media (max-width: 600px) { main { padding: 16px; } header { padding: 18px; } }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
    <header>🏨 Inventario de habitaciones</header>
    <main>
        <section class="intro">
            <h1>Habitaciones por tipo y número</h1>
            <p class="resumen">Cada habitación tiene un número propio. El estado muestra la ocupación de hoy y quién tiene asignado el cuarto.</p>
            @if (session('success'))
                <div class="mensaje">{{ session('success') }}</div>
            @endif
        </section>

        @foreach ($habitaciones->groupBy('nombre') as $nombre => $grupoHabitaciones)
            <section class="grupo">
                <h2>{{ $nombre }} <span class="resumen">({{ $grupoHabitaciones->count() }} habitaciones)</span></h2>
                <div class="habitaciones">
                    @foreach ($grupoHabitaciones as $habitacion)
                        <article class="habitacion">
                            <span class="numero">N.º {{ $habitacion->numero }}</span>
                            @if (! $habitacion->disponible)
                                <span class="estado inactiva">Fuera de servicio</span>
                                <p class="detalle">No se puede asignar una reservación en este momento.</p>
                            @elseif ($habitacion->reservacion_actual)
                                <span class="estado ocupada">Ocupada hoy</span>
                                <p class="detalle">
                                    {{ $habitacion->reservacion_actual->huesped_nombre }}
                                    {{ $habitacion->reservacion_actual->apellido_paterno }}
                                    {{ $habitacion->reservacion_actual->apellido_materno }}
                                </p>
                            @elseif ($habitacion->proxima_reservacion)
                                <span class="estado reservada">Próxima reservación</span>
                                <p class="detalle">
                                    {{ $habitacion->proxima_reservacion->huesped_nombre }}
                                    {{ $habitacion->proxima_reservacion->apellido_paterno }}
                                    · Entrada {{ \Illuminate\Support\Carbon::parse($habitacion->proxima_reservacion->fecha_entrada)->format('d/m/Y') }}
                                </p>
                            @else
                                <span class="estado libre">Sin huésped alojado hoy</span>
                                <p class="detalle">No hay una reservación activa para hoy.</p>
                            @endif
                            <p><strong>${{ number_format($habitacion->precio, 2) }}</strong> por noche</p>
                            @if ($habitacion->disponible)
                                <a href="/reservaciones/crear/{{ $habitacion->id }}" class="boton">Asignar reservación</a>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        <a href="/recepcion" class="volver">← Volver al panel</a>
    </main>
</body>
</html>
