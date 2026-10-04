<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de {{ $area }} - Hotel</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; background: #f3f4f6; color: #1f2937; }
        main { max-width: 1100px; margin: auto; }
        .panel { padding: 24px; margin-bottom: 20px; border-radius: 12px; background: white; box-shadow: 0 2px 8px #ddd; }
        h1 { margin-top: 0; color: #0f766e; }
        .ayuda { color: #64748b; }
        .mensaje { padding: 12px; margin-bottom: 15px; border-radius: 7px; background: #dcfce7; color: #166534; }
        .tabla { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
        th { background: #0f766e; color: white; }
        .estado { display: inline-block; padding: 5px 9px; border-radius: 20px; background: #fef3c7; color: #92400e; font-weight: bold; }
        .estado-completado { background: #dcfce7; color: #166534; }
        .acciones { display: flex; flex-wrap: wrap; gap: 7px; }
        button { padding: 8px 10px; border: 0; border-radius: 6px; background: #0f766e; color: white; cursor: pointer; }
        .completar { background: #166534; }
        .salir { display: inline-block; margin-top: 4px; padding: 10px 15px; border-radius: 6px; background: #dc2626; color: white; text-decoration: none; }
        @media (max-width: 700px) { body { padding: 14px; } }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <section class="panel">
        <h1>🛎️ Solicitudes del área de {{ $area }}</h1>
        <p class="ayuda">Aquí solo aparecen los servicios asignados a tu área. Se muestra el número de habitación y los detalles necesarios para atenderlos.</p>
        @if (session('success'))
            <div class="mensaje">{{ session('success') }}</div>
        @endif
    </section>

    <section class="panel">
        <div class="tabla">
            <table>
                <thead>
                    <tr>
                        <th>Solicitada</th>
                        <th>Servicio</th>
                        <th>Habitación</th>
                        <th>Detalles</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($solicitudes as $solicitud)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($solicitud->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $solicitud->servicio_nombre }}</td>
                            <td>{{ $solicitud->habitacion_nombre }} · N.º {{ $solicitud->habitacion_numero }}</td>
                            <td>{{ $solicitud->observaciones ?: 'Sin detalles adicionales' }}</td>
                            <td>
                                <span class="estado {{ $solicitud->estado === 'completado' ? 'estado-completado' : '' }}">
                                    {{ $solicitud->estado === 'en_proceso' ? 'En proceso' : ucfirst($solicitud->estado) }}
                                </span>
                                @if ($solicitud->completed_at)
                                    <br><small>Completada: {{ \Illuminate\Support\Carbon::parse($solicitud->completed_at)->format('d/m/Y H:i') }}</small>
                                @endif
                            </td>
                            <td>
                                @if ($solicitud->estado !== 'completado')
                                    <div class="acciones">
                                        @if ($solicitud->estado === 'solicitado')
                                            <form method="POST" action="{{ route('operacion.servicios.update', $solicitud->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="estado" value="en_proceso">
                                                <button type="submit">Iniciar atención</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('operacion.servicios.update', $solicitud->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="estado" value="completado">
                                            <button class="completar" type="submit">Marcar completado</button>
                                        </form>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Todavía no hay solicitudes para esta área.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <a class="salir" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar sesión</a>
    <form id="logout-form" method="POST" action="{{ route('logout') }}" hidden>@csrf</form>
</main>
</body>
</html>
