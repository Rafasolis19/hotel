<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Huésped</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
        }

        .formulario {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px #ddd;
        }

        h1 {
            color: #0f766e;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            background: #0f766e;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
        }

        .volver {
            display: inline-block;
            margin-top: 18px;
            padding: 10px 15px;
            border-radius: 6px;
            background: #dc2626;
            color: white;
            text-decoration: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
        }

        th {
            background: #0f766e;
            color: white;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>

<body>

<div class="formulario">

    <h1>Registrar Huésped</h1>

    @if ($errors->any())
        <div style="color: #b91c1c;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/huespedes">
        @csrf

        <label for="nombre">Nombre</label>
        <input id="nombre" type="text" name="nombre" value="{{ old('nombre') }}" required>

        <label for="apellido_paterno">Apellido paterno</label>
        <input id="apellido_paterno" type="text" name="apellido_paterno"
               value="{{ old('apellido_paterno') }}" required>

        <label for="apellido_materno">Apellido materno</label>
        <input id="apellido_materno" type="text" name="apellido_materno"
               value="{{ old('apellido_materno') }}" required>

        <label for="numero">Número de teléfono</label>
        <input id="numero" type="text" name="numero" value="{{ old('numero') }}" required>

        <label for="email">Correo electrónico</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>

        <button type="submit">Guardar huésped</button>
    </form>

    <hr>

    <h2>Huéspedes registrados</h2>

    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Apellido paterno</th>
            <th>Apellido materno</th>
            <th>Teléfono</th>
            <th>Email</th>
        </tr>

        @forelse ($huespedes as $huesped)
            <tr>
                <td>{{ $huesped->nombre }}</td>
                <td>{{ $huesped->apellido_paterno }}</td>
                <td>{{ $huesped->apellido_materno }}</td>
                <td>{{ $huesped->numero }}</td>
                <td>{{ $huesped->email }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Todavía no hay huéspedes registrados.</td>
            </tr>
        @endforelse
    </table>

    <a class="volver" href="/recepcion">← Regresar al panel</a>

</div>

</body>
</html>
