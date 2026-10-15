<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar pago - Hotel</title>

    <style>
        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
        }

        main {
            max-width: 650px;
            margin: 30px auto;
            padding: 28px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ddd;
        }

        h1 {
            color: #0f766e;
        }

        label {
            display: block;
            margin-top: 16px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            margin-top: 7px;
        }

        button, .boton {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 17px;
            border: 0;
            border-radius: 6px;
            background: #0f766e;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .cancelar {
            background: #dc2626;
        }

        .error {
            color: #b91c1c;
            margin-top: 5px;
        }

        .tarjeta {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #f9fafb;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .pago-metodo {
            margin: 20px 0;
            padding: 18px;
            border: 1px solid #d5ded9;
            border-radius: 8px;
        }

        .toggle-pago {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0 8px;
            cursor: pointer;
        }

        .toggle-pago input {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: -1px;
            padding: 0;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .toggle-control {
            position: relative;
            flex: 0 0 46px;
            width: 46px;
            height: 26px;
            border-radius: 999px;
            background: #cbd5d1;
            transition: background-color .18s ease;
        }

        .toggle-control::after {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            content: "";
            transition: transform .18s ease;
        }

        .toggle-pago input:checked + .toggle-control {
            background: #2f7771;
        }

        .toggle-pago input:checked + .toggle-control::after {
            transform: translateX(20px);
        }

        .toggle-pago input:focus-visible + .toggle-control {
            outline: 3px solid rgba(47, 119, 113, .25);
            outline-offset: 2px;
        }

        .toggle-label { font-weight: 700; }

        .error-list { color: #b91c1c; }

        .aviso {
            margin-top: 15px;
            color: #555;
            font-size: 14px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <h1>Registrar pago</h1>

    <p><strong>Reservación:</strong> {{ $reservacion->id }}</p>
    <p>
        <strong>Huésped:</strong>
        {{ $reservacion->huesped_nombre }}
        {{ $reservacion->apellido_paterno }}
        {{ $reservacion->apellido_materno }}
    </p>
    <p><strong>Habitación:</strong> {{ $reservacion->habitacion_nombre }}</p>
    <p><strong>Entrada:</strong> {{ $reservacion->fecha_entrada }}</p>
    <p><strong>Salida:</strong> {{ $reservacion->fecha_salida }}</p>
    <p><strong>Total de la reservación:</strong> ${{ number_format($reservacion->total, 2) }}</p>
    <p><strong>Abonado:</strong> ${{ number_format($reservacion->monto_pagado, 2) }}</p>
    <p><strong>Saldo pendiente:</strong> ${{ number_format($reservacion->saldo_pendiente, 2) }}</p>
    <p>Puedes registrar un abono o dividir el pago entre dos métodos. El importe no puede superar el saldo pendiente.</p>

    <form method="POST" action="/pagos/{{ $reservacion->id }}">
        @csrf

        @error('pagos')
            <div class="error-list">{{ $message }}</div>
        @enderror

        <section class="pago-metodo" data-payment-row>
            <h2>Primer método de pago</h2>
            <label for="metodo_pago_0">Método de pago</label>
            <select id="metodo_pago_0" name="pagos[0][metodo_pago]" required>
                <option value="">Selecciona un método</option>
                <option value="efectivo" @selected(old('pagos.0.metodo_pago') === 'efectivo')>Efectivo</option>
                <option value="tarjeta" @selected(old('pagos.0.metodo_pago') === 'tarjeta')>Tarjeta</option>
                <option value="transferencia" @selected(old('pagos.0.metodo_pago') === 'transferencia')>Transferencia</option>
            </select>
            @error('pagos.0.metodo_pago') <div class="error">{{ $message }}</div> @enderror

            <label for="monto_pago_0">Importe</label>
            <input id="monto_pago_0" class="importe-pago" type="number" name="pagos[0][monto]" min="0.01" max="{{ number_format($reservacion->saldo_pendiente, 2, '.', '') }}" step="0.01" value="{{ old('pagos.0.monto') }}" required>
            @error('pagos.0.monto') <div class="error">{{ $message }}</div> @enderror

            <div class="tarjeta" data-card-fields>
                <label for="titular_tarjeta_0">Titular de la tarjeta</label>
                <input id="titular_tarjeta_0" type="text" name="pagos[0][titular_tarjeta]" value="{{ old('pagos.0.titular_tarjeta') }}" autocomplete="off" disabled>
                @error('pagos.0.titular_tarjeta') <div class="error">{{ $message }}</div> @enderror

                <label for="numero_tarjeta_0">Número de tarjeta de prueba</label>
                <input id="numero_tarjeta_0" type="text" name="pagos[0][numero_tarjeta]" inputmode="numeric" autocomplete="off" placeholder="Solo usa un número ficticio" disabled>
                @error('pagos.0.numero_tarjeta') <div class="error">{{ $message }}</div> @enderror

                <label for="vencimiento_tarjeta_0">Vencimiento</label>
                <input id="vencimiento_tarjeta_0" type="month" name="pagos[0][vencimiento_tarjeta]" value="{{ old('pagos.0.vencimiento_tarjeta') }}" disabled>
                @error('pagos.0.vencimiento_tarjeta') <div class="error">{{ $message }}</div> @enderror

                <label for="codigo_seguridad_0">Código de seguridad de prueba</label>
                <input id="codigo_seguridad_0" type="password" name="pagos[0][codigo_seguridad]" inputmode="numeric" autocomplete="new-password" placeholder="3 o 4 dígitos" disabled>
                @error('pagos.0.codigo_seguridad') <div class="error">{{ $message }}</div> @enderror
            </div>
        </section>

        <label class="toggle-pago" for="agregar_segundo_pago">
            <input id="agregar_segundo_pago" type="checkbox" name="agregar_segundo_pago" value="1" @checked(old('agregar_segundo_pago'))>
            <span class="toggle-control" aria-hidden="true"></span>
            <span class="toggle-label">Agregar otro método de pago</span>
        </label>

        <fieldset id="pago-secundario" class="pago-metodo" data-payment-row @disabled(! old('agregar_segundo_pago')) style="display: {{ old('agregar_segundo_pago') ? 'block' : 'none' }}">
            <legend>Segundo método de pago</legend>
            <label for="metodo_pago_1">Método de pago</label>
            <select id="metodo_pago_1" name="pagos[1][metodo_pago]">
                <option value="">Selecciona un método</option>
                <option value="efectivo" @selected(old('pagos.1.metodo_pago') === 'efectivo')>Efectivo</option>
                <option value="tarjeta" @selected(old('pagos.1.metodo_pago') === 'tarjeta')>Tarjeta</option>
                <option value="transferencia" @selected(old('pagos.1.metodo_pago') === 'transferencia')>Transferencia</option>
            </select>
            @error('pagos.1.metodo_pago') <div class="error">{{ $message }}</div> @enderror

            <label for="monto_pago_1">Importe</label>
            <input id="monto_pago_1" class="importe-pago" type="number" name="pagos[1][monto]" min="0.01" max="{{ number_format($reservacion->saldo_pendiente, 2, '.', '') }}" step="0.01" value="{{ old('pagos.1.monto') }}">
            @error('pagos.1.monto') <div class="error">{{ $message }}</div> @enderror

            <div class="tarjeta" data-card-fields>
                <label for="titular_tarjeta_1">Titular de la tarjeta</label>
                <input id="titular_tarjeta_1" type="text" name="pagos[1][titular_tarjeta]" value="{{ old('pagos.1.titular_tarjeta') }}" autocomplete="off" disabled>
                @error('pagos.1.titular_tarjeta') <div class="error">{{ $message }}</div> @enderror

                <label for="numero_tarjeta_1">Número de tarjeta de prueba</label>
                <input id="numero_tarjeta_1" type="text" name="pagos[1][numero_tarjeta]" inputmode="numeric" autocomplete="off" placeholder="Solo usa un número ficticio" disabled>
                @error('pagos.1.numero_tarjeta') <div class="error">{{ $message }}</div> @enderror
                <label for="vencimiento_tarjeta_1">Vencimiento</label>
                <input id="vencimiento_tarjeta_1" type="month" name="pagos[1][vencimiento_tarjeta]" value="{{ old('pagos.1.vencimiento_tarjeta') }}" disabled>
                @error('pagos.1.vencimiento_tarjeta') <div class="error">{{ $message }}</div> @enderror

                <label for="codigo_seguridad_1">Código de seguridad de prueba</label>
                <input id="codigo_seguridad_1" type="password" name="pagos[1][codigo_seguridad]" inputmode="numeric" autocomplete="new-password" placeholder="3 o 4 dígitos" disabled>
                @error('pagos.1.codigo_seguridad') <div class="error">{{ $message }}</div> @enderror
            </div>
        </fieldset>

        <p id="resumen-importes" class="aviso" data-saldo="{{ number_format($reservacion->saldo_pendiente, 2, '.', '') }}">
            Saldo después de este pago: ${{ number_format($reservacion->saldo_pendiente, 2) }}
        </p>

        <p class="aviso">
            Usa datos ficticios para tarjeta. El sistema guarda solo el titular, el vencimiento y los últimos cuatro dígitos; no guarda el número completo ni el código.
        </p>

        <button type="submit">Registrar pago o abono</button>
        <a href="{{ auth()->user()->role === 'administrador' ? route('admin.pagos.index') : '/pagos' }}" class="boton cancelar">Cancelar</a>
    </form>
</main>

<script>
    const segundoPagoToggle = document.getElementById('agregar_segundo_pago');
    const segundoPago = document.getElementById('pago-secundario');
    const filasPago = document.querySelectorAll('[data-payment-row]');
    const importesPago = document.querySelectorAll('.importe-pago');
    const resumenImportes = document.getElementById('resumen-importes');

    function actualizarFilaPago(fila) {
        const esOpcional = fila === segundoPago;
        const habilitada = !esOpcional || segundoPagoToggle.checked;
        const metodo = fila.querySelector('select');
        const esTarjeta = habilitada && metodo.value === 'tarjeta';
        const datosTarjeta = fila.querySelector('[data-card-fields]');

        if (esOpcional) {
            metodo.required = habilitada;
            fila.querySelector('.importe-pago').required = habilitada;
        }

        datosTarjeta.style.display = esTarjeta ? 'block' : 'none';
        datosTarjeta.querySelectorAll('input').forEach((campo) => {
            campo.required = esTarjeta;
            campo.disabled = !esTarjeta;
        });
    }

    function actualizarResumenImportes() {
        const saldo = Number(resumenImportes.dataset.saldo);
        const importe = Array.from(importesPago)
            .filter((campo) => !campo.disabled && !campo.closest('fieldset')?.disabled)
            .reduce((total, campo) => total + (Number(campo.value) || 0), 0);
        const restante = Math.max(0, saldo - importe);

        resumenImportes.classList.toggle('error-list', importe > saldo);
        if (importe > saldo) {
            resumenImportes.textContent = `El importe supera el saldo pendiente de $${saldo.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}.`;
            return;
        }

        resumenImportes.textContent = `Importe registrado: $${importe.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}. Saldo después del pago: $${restante.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}.`;
    }

    filasPago.forEach((fila) => {
        fila.querySelector('select').addEventListener('change', () => actualizarFilaPago(fila));
        actualizarFilaPago(fila);
    });

    segundoPagoToggle.addEventListener('change', () => {
        segundoPago.disabled = !segundoPagoToggle.checked;
        segundoPago.style.display = segundoPagoToggle.checked ? 'block' : 'none';
        actualizarFilaPago(segundoPago);
        actualizarResumenImportes();
    });

    importesPago.forEach((campo) => campo.addEventListener('input', actualizarResumenImportes));
    actualizarResumenImportes();
</script>
</body>
</html>
