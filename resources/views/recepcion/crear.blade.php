<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar reservación - Hotel</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
        }

        header {
            background: #0f766e;
            color: white;
            padding: 22px 30px;
            font-size: 24px;
            font-weight: bold;
        }

        main {
            max-width: 650px;
            margin: 35px auto;
            padding: 28px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ddd;
        }

        label {
            display: block;
            margin-top: 18px;
            font-weight: bold;
        }

        select, input {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            margin-top: 7px;
        }

        button, .volver {
            display: inline-block;
            margin-top: 22px;
            padding: 11px 17px;
            border: 0;
            border-radius: 6px;
            background: #0f766e;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .error {
            color: #b91c1c;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
    <header>🏨 Registrar reservación</header>

    <main>
        <h1>{{ $habitacion->nombre }} · Habitación {{ $habitacion->numero }}</h1>
        <p>{{ $habitacion->descripcion }}</p>
        <p><strong>${{ number_format($habitacion->precio, 2) }} por noche</strong></p>

        @if ($huespedes->isEmpty())
            @if ($hayHuespedesRegistrados)
                <p class="error">No hay huéspedes disponibles; todos tienen una estancia activa.</p>
            @else
                <p class="error">Primero debes registrar un huésped.</p>
            @endif
            <a href="{{ auth()->user()->role === 'administrador' ? route('admin.huespedes.index') : '/huespedes' }}" class="volver">Ir a huéspedes</a>
        @else
            <form method="POST" action="/reservaciones">
                @csrf

                <input type="hidden" name="habitacion_id" value="{{ $habitacion->id }}">

                <label for="huesped_id">Huésped</label>
                <select id="huesped_id" name="huesped_id" required>
                    <option value="">Selecciona un huésped</option>
                    @foreach ($huespedes as $huesped)
                        <option value="{{ $huesped->id }}"
                            @selected(old('huesped_id') == $huesped->id)>
                            {{ $huesped->nombre }}
                            {{ $huesped->apellido_paterno }}
                            {{ $huesped->apellido_materno }}
                        </option>
                    @endforeach
                </select>
                @error('huesped_id')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="fecha_entrada">Fecha de entrada</label>
                <input
                    id="fecha_entrada"
                    type="date"
                    name="fecha_entrada"
                    min="{{ now()->toDateString() }}"
                    value="{{ old('fecha_entrada') }}"
                    required
                >
                @error('fecha_entrada')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="fecha_salida">Fecha de salida</label>
                <input
                    id="fecha_salida"
                    type="date"
                    name="fecha_salida"
                    min="{{ now()->toDateString() }}"
                    value="{{ old('fecha_salida') }}"
                    required
                >
                @error('fecha_salida')
                    <div class="error">{{ $message }}</div>
                @enderror

                <button type="submit">Guardar reservación</button>
                <a href="/habitaciones" class="volver">Cancelar</a>
            </form>
        @endif
    </main>
</body>
</html>
