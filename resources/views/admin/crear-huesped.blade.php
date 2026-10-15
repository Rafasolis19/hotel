<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar huésped - Hotel</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; background: #f4f6f9; color: #1f2937; }
        main { max-width: 760px; margin: 20px auto; padding: 28px; background: white; border-radius: 10px; box-shadow: 0 2px 8px #ddd; }
        h1 { color: #172554; }
        .intro { color: #64748b; }
        form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input { width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 5px; font: inherit; }
        .campo-completo { grid-column: 1 / -1; }
        .boton { display: inline-block; margin-top: 12px; padding: 11px 16px; border: 0; border-radius: 6px; background: #172554; color: white; text-decoration: none; cursor: pointer; font: inherit; }
        .cancelar { margin-left: 8px; background: #dc2626; }
        .error { margin-top: 5px; color: #b91c1c; }
        .errores { padding: 11px; border-radius: 6px; background: #fee2e2; color: #991b1b; }
        @media (max-width: 620px) {
            body { padding: 14px; }
            form { grid-template-columns: 1fr; }
            .campo-completo { grid-column: auto; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <h1>Registrar huésped</h1>
    <p class="intro">Agrega sus datos de contacto. Este registro no crea una cuenta para iniciar sesión.</p>

    @if ($errors->any())
        <div class="errores">
            <strong>Revisa los datos:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.huespedes.store') }}">
        @csrf
        <div>
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" value="{{ old('nombre') }}" autocomplete="given-name" maxlength="255" required>
            @error('nombre') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label for="apellido_paterno">Apellido paterno</label>
            <input id="apellido_paterno" name="apellido_paterno" value="{{ old('apellido_paterno') }}" autocomplete="family-name" maxlength="255" required>
            @error('apellido_paterno') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label for="apellido_materno">Apellido materno</label>
            <input id="apellido_materno" name="apellido_materno" value="{{ old('apellido_materno') }}" maxlength="255" required>
            @error('apellido_materno') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label for="numero">Teléfono</label>
            <input id="numero" name="numero" type="tel" value="{{ old('numero') }}" autocomplete="tel" maxlength="50" required>
            @error('numero') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="campo-completo">
            <label for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="campo-completo">
            <button class="boton" type="submit">Guardar huésped</button>
            <a class="boton cancelar" href="{{ route('admin.huespedes.index') }}">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>
