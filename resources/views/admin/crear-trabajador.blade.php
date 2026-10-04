<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar persona - Hotel</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; background: #f4f6f9; color: #1f2937; }
        main { max-width: 760px; margin: 15px auto; padding: 28px; background: white; border-radius: 12px; box-shadow: 0 2px 8px #ddd; }
        h1 { color: #172554; }
        .intro { color: #64748b; }
        form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input, select { width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px; font: inherit; }
        .campo-completo { grid-column: 1 / -1; }
        .boton, .volver { display: inline-block; margin-top: 15px; padding: 11px 16px; border: 0; border-radius: 6px; background: #172554; color: white; text-decoration: none; cursor: pointer; font: inherit; }
        .volver { margin-left: 8px; background: #dc2626; }
        .error, .errores { color: #991b1b; }
        .errores { padding: 11px; border-radius: 6px; background: #fee2e2; }
        @media (max-width: 620px) { body { padding: 14px; } form { grid-template-columns: 1fr; } .campo-completo { grid-column: auto; } }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <h1>Registrar nueva persona</h1>
    <p class="intro">Se guardará su perfil laboral en <code>usuarios</code> y se creará una cuenta para iniciar sesión.</p>

    @if ($errors->any())
        <div class="errores">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.trabajadores.store') }}">
        @csrf
        <div>
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" value="{{ old('nombre') }}" maxlength="255" required>
        </div>
        <div>
            <label for="apellido_paterno">Apellido paterno</label>
            <input id="apellido_paterno" name="apellido_paterno" value="{{ old('apellido_paterno') }}" maxlength="255" required>
        </div>
        <div>
            <label for="apellido_materno">Apellido materno</label>
            <input id="apellido_materno" name="apellido_materno" value="{{ old('apellido_materno') }}" maxlength="255" required>
        </div>
        <div>
            <label for="rfc">RFC</label>
            <input id="rfc" name="rfc" value="{{ old('rfc') }}" minlength="12" maxlength="13" required>
        </div>
        <div>
            <label for="nss">Número de Seguro Social</label>
            <input id="nss" name="nss" value="{{ old('nss') }}" inputmode="numeric" pattern="[0-9]{11}" maxlength="11" required>
        </div>
        <div>
            <label for="numero">Teléfono</label>
            <input id="numero" name="numero" value="{{ old('numero') }}" maxlength="50" required>
        </div>
        <div class="campo-completo">
            <label for="email">Correo electrónico de acceso</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" maxlength="255" required>
        </div>
        <div>
            <label for="role">Rol</label>
            <select id="role" name="role" required>
                <option value="recepcionista" @selected(old('role', 'recepcionista') === 'recepcionista')>Recepcionista</option>
                <option value="administrador" @selected(old('role') === 'administrador')>Super Gerente / Administrador</option>
                <option value="cocina" @selected(old('role') === 'cocina')>Cocina</option>
                <option value="limpieza" @selected(old('role') === 'limpieza')>Limpieza</option>
                <option value="mantenimiento" @selected(old('role') === 'mantenimiento')>Mantenimiento</option>
                <option value="servicio_cuarto" @selected(old('role') === 'servicio_cuarto')>Servicio al cuarto</option>
            </select>
        </div>
        <div>
            <label for="password">Contraseña inicial</label>
            <input id="password" type="password" name="password" minlength="8" autocomplete="new-password" required>
        </div>
        <div>
            <label for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required>
        </div>
        <div class="campo-completo">
            <button class="boton" type="submit">Registrar persona</button>
            <a class="volver" href="{{ route('admin.trabajadores.index') }}">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>
