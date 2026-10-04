<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar huésped - Hotel</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; background: #f4f6f9; color: #1f2937; }
        main { max-width: 650px; margin: 20px auto; padding: 28px; background: white; border-radius: 10px; box-shadow: 0 2px 8px #ddd; }
        h1 { color: #172554; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input { width: 100%; padding: 11px; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 5px; }
        .boton { display: inline-block; margin-top: 20px; padding: 11px 16px; border: 0; border-radius: 6px; background: #172554; color: white; text-decoration: none; cursor: pointer; }
        .cancelar { background: #dc2626; }
        .error { color: #b91c1c; margin-top: 5px; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <h1>Editar huésped</h1>

    <form method="POST" action="{{ route('admin.huespedes.update', $huesped->id) }}">
        @csrf
        @method('PUT')

        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="{{ old('nombre', $huesped->nombre) }}" required maxlength="255">
        @error('nombre') <div class="error">{{ $message }}</div> @enderror

        <label for="apellido_paterno">Apellido paterno</label>
        <input id="apellido_paterno" name="apellido_paterno" value="{{ old('apellido_paterno', $huesped->apellido_paterno) }}" required maxlength="255">
        @error('apellido_paterno') <div class="error">{{ $message }}</div> @enderror

        <label for="apellido_materno">Apellido materno</label>
        <input id="apellido_materno" name="apellido_materno" value="{{ old('apellido_materno', $huesped->apellido_materno) }}" required maxlength="255">
        @error('apellido_materno') <div class="error">{{ $message }}</div> @enderror

        <label for="numero">Teléfono</label>
        <input id="numero" name="numero" value="{{ old('numero', $huesped->numero) }}" required maxlength="50">
        @error('numero') <div class="error">{{ $message }}</div> @enderror

        <label for="email">Correo electrónico</label>
        <input id="email" type="email" name="email" value="{{ old('email', $huesped->email) }}" required maxlength="255">
        @error('email') <div class="error">{{ $message }}</div> @enderror

        <button class="boton" type="submit">Guardar cambios</button>
        <a class="boton cancelar" href="{{ route('admin.huespedes.index') }}">Cancelar</a>
    </form>
</main>
</body>
</html>
