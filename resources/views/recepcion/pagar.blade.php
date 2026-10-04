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
    <p><strong>Total a pagar:</strong> ${{ number_format($reservacion->total, 2) }}</p>

    <form method="POST" action="/pagos/{{ $reservacion->id }}">
        @csrf

        <label for="metodo_pago">Método de pago</label>
        <select id="metodo_pago" name="metodo_pago" required>
            <option value="">Selecciona un método</option>
            <option value="efectivo" @selected(old('metodo_pago') === 'efectivo')>Efectivo</option>
            <option value="tarjeta" @selected(old('metodo_pago') === 'tarjeta')>Tarjeta</option>
            <option value="transferencia" @selected(old('metodo_pago') === 'transferencia')>Transferencia</option>
        </select>
        @error('metodo_pago')
            <div class="error">{{ $message }}</div>
        @enderror

        <div class="tarjeta" id="datos-tarjeta">
            <label for="titular_tarjeta">Titular de la tarjeta</label>
            <input
                id="titular_tarjeta"
                type="text"
                name="titular_tarjeta"
                value="{{ old('titular_tarjeta') }}"
                autocomplete="off"
            >
            @error('titular_tarjeta')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="numero_tarjeta">Número de tarjeta de prueba</label>
            <input
                id="numero_tarjeta"
                type="text"
                name="numero_tarjeta"
                inputmode="numeric"
                autocomplete="off"
                placeholder="Solo usa un número ficticio"
            >
            @error('numero_tarjeta')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="vencimiento_tarjeta">Vencimiento</label>
            <input
                id="vencimiento_tarjeta"
                type="month"
                name="vencimiento_tarjeta"
                value="{{ old('vencimiento_tarjeta') }}"
            >
            @error('vencimiento_tarjeta')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="codigo_seguridad">Código de seguridad de prueba</label>
            <input
                id="codigo_seguridad"
                type="password"
                name="codigo_seguridad"
                inputmode="numeric"
                autocomplete="new-password"
                placeholder="3 o 4 dígitos"
            >
            @error('codigo_seguridad')
                <div class="error">{{ $message }}</div>
            @enderror

            <p class="aviso">
                Usa datos ficticios. La aplicación guarda solo el titular, el vencimiento
                y los últimos cuatro dígitos; no guarda el número completo ni el código.
            </p>
        </div>

        <button type="submit">Registrar pago</button>
        <a href="/pagos" class="boton cancelar">Cancelar</a>
    </form>
</main>

<script>
    const metodoPago = document.getElementById('metodo_pago');
    const datosTarjeta = document.getElementById('datos-tarjeta');
    const camposTarjeta = [
        document.getElementById('titular_tarjeta'),
        document.getElementById('numero_tarjeta'),
        document.getElementById('vencimiento_tarjeta'),
        document.getElementById('codigo_seguridad'),
    ];

    function actualizarCamposTarjeta() {
        const esTarjeta = metodoPago.value === 'tarjeta';

        datosTarjeta.style.display = esTarjeta ? 'block' : 'none';

        camposTarjeta.forEach((campo) => {
            campo.required = esTarjeta;
            campo.disabled = !esTarjeta;
        });
    }

    metodoPago.addEventListener('change', actualizarCamposTarjeta);
    actualizarCamposTarjeta();
</script>
</body>
</html>