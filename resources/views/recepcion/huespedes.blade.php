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
    </style>
</head>

<body>

<div class="formulario">

    <h1>Registrar Huésped</h1>

    <form method="POST" action="/huespedes">

        @csrf

        <label>Nombre</label>
        <input type="text" name="nombre" required>

        <label>Apellido paterno</label>
        <input type="text" name="apellido_paterno" required>

        <label>Apellido materno</label>
        <input type="text" name="apellido_materno" required>

        <label>Número de teléfono</label>
        <input type="text" name="numero" required>

        <label>Correo electrónico</label>
        <input type="email" name="email" required>

        <button type="submit">Guardar huésped</button>

    </form>

    <hr>

<h2>Huéspedes registrados</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>Nombre</th>
        <th>Apellido paterno</th>
        <th>Apellido materno</th>
        <th>Teléfono</th>
        <th>Email</th>
    </tr>

    @foreach ($huespedes as $huesped)
        <tr>
            <td>{{ $huesped->nombre }}</td>
            <td>{{ $huesped->{'apellido paterno'} }}</td>
            <td>{{ $huesped->{'apellido materno'} }}</td>
            <td>{{ $huesped->{'numero '} }}</td>
            <td>{{ $huesped->email }}</td>
        </tr>
    @endforeach
</table>

</div>

</body>
</html>