<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - Super Gerencia</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; background: #f4f6f9; color: #1f2937; }
        main { max-width: 1300px; margin: auto; }
        .panel { padding: 24px; margin-bottom: 22px; border-radius: 12px; background: white; box-shadow: 0 2px 8px #ddd; }
        h1, h2 { margin-top: 0; color: #172554; }
        .catalogo { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; }
        .servicio { padding: 14px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .servicio strong { display: block; margin-bottom: 6px; color: #0f766e; }
        .tabla { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
        th { background: #172554; color: white; }
        .estado { display: inline-block; padding: 5px 9px; border-radius: 20px; background: #dcfce7; color: #166534; font-weight: bold; }
        .volver { display: inline-block; margin-top: 4px; padding: 10px 15px; border-radius: 6px; background: #dc2626; color: white; text-decoration: none; }
        .nota { color: #64748b; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <section class="panel">
        <h1>🛎️ Control de servicios</h1>
        <p class="nota">Consulta el catálogo y el historial de solicitudes realizadas por huéspedes.</p>
    </section>

    <section class="panel">
        <h2>Catálogo de servicios</h2>
        <div class="catalogo">
            @forelse ($tiposServicios as $tipoServicio)
                <article class="servicio">
                    <strong>{{ $tipoServicio->nombre }}</strong>
                    <span>{{ $tipoServicio->descripcion ?: 'Sin descripción' }}</span>
                    <p>Área: {{ ucfirst(str_replace('_', ' ', $tipoServicio->categoria)) }}</p>
                    <p>{{ $tipoServicio->activo ? 'Activo' : 'Inactivo' }}</p>
                </article>
            @empty
                <p>No hay servicios registrados.</p>
            @endforelse
        </div>
    </section>

    <section class="panel">
        <h2>Solicitudes de servicio</h2>
        <div class="tabla">
            <table>
                <thead>
                    <tr>
                        <th>Fecha solicitada</th>
                        <th>Servicio</th>
                        <th>Área</th>
                        <th>Habitación</th>
                        <th>Huésped</th>
                        <th>Observaciones</th>
                        <th>Estado</th>
                        <th>Completada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($solicitudes as $solicitud)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($solicitud->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $solicitud->servicio_nombre }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $solicitud->categoria)) }}</td>
                            <td>{{ $solicitud->habitacion_nombre }} · N.º {{ $solicitud->habitacion_numero }}</td>
                            <td>{{ $solicitud->huesped_nombre }} {{ $solicitud->apellido_paterno }} {{ $solicitud->apellido_materno }}</td>
                            <td>{{ $solicitud->observaciones ?: '—' }}</td>
                            <td><span class="estado">{{ ucfirst($solicitud->estado) }}</span></td>
                            <td>{{ $solicitud->completed_at ? \Illuminate\Support\Carbon::parse($solicitud->completed_at)->format('d/m/Y H:i') : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8">Todavía no hay solicitudes registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <a class="volver" href="{{ route('admin') }}">← Volver al panel</a>
</main>
</body>
</html>
