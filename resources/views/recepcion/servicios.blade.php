<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios para huéspedes - Hotel</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; background: #f3f4f6; color: #1f2937; }
        main { max-width: 1250px; margin: auto; }
        .encabezado, .panel { padding: 24px; margin-bottom: 22px; border-radius: 12px; background: white; box-shadow: 0 2px 8px #ddd; }
        h1, h2 { margin-top: 0; color: #0f766e; }
        .encabezado p, .ayuda { color: #64748b; }
        form { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        label { display: block; margin-bottom: 7px; font-weight: bold; }
        select, textarea { width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px; font: inherit; }
        textarea { min-height: 95px; resize: vertical; }
        .ancho { grid-column: 1 / -1; }
        .boton, .volver { display: inline-block; padding: 11px 16px; border: 0; border-radius: 6px; background: #0f766e; color: white; text-decoration: none; font: inherit; cursor: pointer; }
        .volver { background: #dc2626; }
        .mensaje, .error { padding: 11px; margin-bottom: 15px; border-radius: 6px; }
        .mensaje { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
        .error-campo { display: block; margin-top: 5px; color: #b91c1c; font-size: 14px; }
        .tabla { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
        th { background: #0f766e; color: white; }
        .etiqueta { display: inline-block; padding: 5px 9px; border-radius: 20px; background: #fef3c7; color: #92400e; font-weight: bold; font-size: 13px; }
        .boton-completar { padding: 8px 10px; border: 0; border-radius: 6px; background: #0f766e; color: white; cursor: pointer; }
        .texto-auto { display: block; margin-top: 7px; color: #64748b; font-size: 12px; }
        @media (max-width: 700px) { body { padding: 14px; } form { grid-template-columns: 1fr; } .ancho { grid-column: auto; } }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <section class="encabezado">
        <h1>🛎️ Servicios para huéspedes</h1>
        <p>Registra solicitudes ligadas al huésped y su habitación. Puedes completarlas manualmente; si quedan pendientes, se completan automáticamente al cumplir un minuto.</p>
    </section>

    <section class="panel">
        <h2>Nueva solicitud</h2>

        @if (session('success'))
            <div class="mensaje">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">Revisa los datos marcados e inténtalo de nuevo.</div>
        @endif

        @if ($huespedesAlojados->isEmpty())
            <p class="ayuda">No hay huéspedes alojados hoy. Las solicitudes se pueden asociar cuando la fecha de entrada de su reservación haya llegado.</p>
        @elseif ($tiposServicios->isEmpty())
            <p class="ayuda">No hay servicios activos para seleccionar.</p>
        @else
            <form method="POST" action="{{ route('recepcion.servicios.store') }}">
                @csrf
                <div>
                    <label for="reservacion_id">Huésped y habitación</label>
                    <select id="reservacion_id" name="reservacion_id" required>
                        <option value="">Selecciona una habitación ocupada</option>
                        @foreach ($huespedesAlojados as $alojamiento)
                            <option value="{{ $alojamiento->reservacion_id }}" @selected(old('reservacion_id') == $alojamiento->reservacion_id)>
                                Hab. {{ $alojamiento->habitacion_numero }} — {{ $alojamiento->huesped_nombre }} {{ $alojamiento->apellido_paterno }} {{ $alojamiento->apellido_materno }}
                            </option>
                        @endforeach
                    </select>
                    @error('reservacion_id') <span class="error-campo">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="tipo_servicio_id">Tipo de servicio</label>
                    <select id="tipo_servicio_id" name="tipo_servicio_id" required>
                        <option value="">Selecciona un servicio</option>
                        @foreach ($tiposServicios as $tipoServicio)
                            <option value="{{ $tipoServicio->id }}" @selected(old('tipo_servicio_id') == $tipoServicio->id)>{{ $tipoServicio->nombre }}</option>
                        @endforeach
                    </select>
                    @error('tipo_servicio_id') <span class="error-campo">{{ $message }}</span> @enderror
                </div>

                <div class="ancho">
                    <label for="observaciones">Detalles de la solicitud</label>
                    <textarea id="observaciones" name="observaciones" maxlength="1000" placeholder="Ejemplo: llevar toallas adicionales a la habitación.">{{ old('observaciones') }}</textarea>
                    @error('observaciones') <span class="error-campo">{{ $message }}</span> @enderror
                </div>

                <div class="ancho">
                    <label>
                        <input type="checkbox" name="auto_completar" value="1" @checked(old('auto_completar')) style="width: auto;">
                        Completar automáticamente si nadie atiende la solicitud durante 1 minuto
                    </label>
                </div>

                <div class="ancho">
                    <button class="boton" type="submit">Registrar solicitud</button>
                </div>
            </form>
        @endif
    </section>

    <section class="panel">
        <h2>Solicitudes registradas</h2>
        <div class="tabla">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Servicio</th>
                        <th>Habitación</th>
                        <th>Huésped</th>
                        <th>Detalles</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($solicitudes as $solicitud)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($solicitud->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $solicitud->servicio_nombre }}</td>
                            <td>{{ $solicitud->habitacion_nombre }} · N.º {{ $solicitud->habitacion_numero }}</td>
                            <td>{{ $solicitud->huesped_nombre }} {{ $solicitud->apellido_paterno }} {{ $solicitud->apellido_materno }}</td>
                            <td>{{ $solicitud->observaciones ?: 'Sin detalles adicionales' }}</td>
                            <td><span class="etiqueta">{{ ucfirst($solicitud->estado) }}</span></td>
                            <td>
                                @if ($solicitud->estado === 'solicitado')
                                    <form method="POST" action="{{ route('recepcion.servicios.complete', $solicitud->id) }}" @if ($solicitud->auto_completar) data-auto-complete="{{ \Illuminate\Support\Carbon::parse($solicitud->created_at)->addMinute()->toIso8601String() }}" @endif>
                                        @csrf
                                        @method('PUT')
                                        <button class="boton-completar" type="submit">Marcar completado</button>
                                    </form>
                                    <span class="texto-auto">{{ $solicitud->auto_completar ? 'Se completará automáticamente al cumplirse 1 minuto.' : 'Puedes marcarla completada manualmente.' }}</span>
                                @else
                                    {{ $solicitud->completed_at ? \Illuminate\Support\Carbon::parse($solicitud->completed_at)->format('d/m/Y H:i') : 'Completada' }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">Aún no hay solicitudes registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <a href="{{ auth()->user()->role === 'administrador' ? route('admin') : '/recepcion' }}" class="volver">← Volver al panel</a>
</main>
<script>
    document.querySelectorAll('form[data-auto-complete]').forEach((form) => {
        const completeAt = new Date(form.dataset.autoComplete).getTime();
        const delay = Math.max(0, completeAt - Date.now());

        window.setTimeout(() => form.requestSubmit(), delay);
    });
</script>
</body>
</html>
