<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanzas - Hotel</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; background: #f4f6f9; color: #1f2937; }
        main { max-width: 1100px; margin: 15px auto; }
        h1, h2 { color: #172554; }
        .resumen, .panel { background: white; padding: 24px; border-radius: 10px; box-shadow: 0 2px 8px #ddd; }
        .resumen { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin: 20px 0; }
        .dato { font-size: 28px; color: #0f766e; font-weight: bold; margin-top: 8px; }
        .fila { display: grid; grid-template-columns: minmax(140px, 220px) 1fr 180px; align-items: center; gap: 14px; margin: 18px 0; }
        .pista { height: 22px; overflow: hidden; background: #e5e7eb; border-radius: 20px; }
        .barra { height: 100%; background: #0f766e; border-radius: inherit; }
        .total { text-align: right; font-weight: bold; }
        .nota { color: #64748b; }
        .volver { display: inline-block; margin-top: 20px; padding: 10px 15px; border-radius: 6px; background: #dc2626; color: white; text-decoration: none; }
        @media (max-width: 650px) { .fila { grid-template-columns: 1fr; gap: 6px; } .total { text-align: left; } }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <h1>Resumen financiero</h1>
    <section class="resumen">
        <article class="panel">
            <strong>Ingresos registrados</strong>
            <div class="dato">${{ number_format($ingresosPagados, 2) }}</div>
        </article>
        <article class="panel">
            <strong>Pagos completados</strong>
            <div class="dato">{{ $cantidadPagos }}</div>
        </article>
        <article class="panel">
            <strong>Reservaciones por cobrar</strong>
            <div class="dato">${{ number_format($porCobrar, 2) }}</div>
        </article>
    </section>

    <section class="panel">
        <h2>Ingresos por método de pago</h2>
        <p class="nota">Solo se consideran pagos con estado “pagado”.</p>

        @forelse ($pagosPorMetodo as $metodo)
            @php
                $porcentaje = ((float) $metodo->total / $maxIngresoMetodo) * 100;
            @endphp
            <div class="fila">
                <span>{{ ucfirst($metodo->metodo_pago) }} ({{ $metodo->cantidad }} pagos)</span>
                <div class="pista" role="img" aria-label="Ingresos de {{ $metodo->metodo_pago }}">
                    <div class="barra" style="width: {{ $porcentaje }}%"></div>
                </div>
                <span class="total">${{ number_format($metodo->total, 2) }}</span>
            </div>
        @empty
            <p>No hay pagos completados para mostrar.</p>
        @endforelse
    </section>

    <a class="volver" href="{{ route('admin') }}">← Volver al panel</a>
</main>
</body>
</html>
