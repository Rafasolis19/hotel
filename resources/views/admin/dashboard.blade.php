<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel - Administrador</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        header {
            background: #172554;
            color: white;
            padding: 20px 30px;
        }

        header h1 {
            margin: 0;
        }

        .contenedor {
            padding: 30px;
        }

        .bienvenida {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .menu {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .tarjeta {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ddd;
        }

        .tarjeta h2 {
            color: #172554;
        }

        .tarjeta p {
            color: #555;
        }

        button {
            background: #dc2626;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>

<body>

<header>
    <h1>🏨 Hotel - Panel de Administrador</h1>
</header>

<div class="contenedor">

    <div class="bienvenida">
        <h2>Bienvenido, {{ auth()->user()->name }}</h2>
        <p>Has iniciado sesión como administrador.</p>
    </div>

    <div class="menu">

        <div class="tarjeta">
            <h2>👥 Huéspedes</h2>
            <p>Administrar información de los huéspedes.</p>
        </div>

        <div class="tarjeta">
            <h2>🛏️ Habitaciones</h2>
            <p>Consultar y administrar habitaciones.</p>
        </div>

        <div class="tarjeta">
            <h2>📅 Reservaciones</h2>
            <p>Administrar las reservaciones del hotel.</p>
        </div>

        <div class="tarjeta">
            <h2>💳 Pagos</h2>
            <p>Consultar los pagos realizados.</p>
        </div>

        <div class="tarjeta">
            <h2>🧹 Servicios</h2>
            <p>Administrar los servicios del hotel.</p>
        </div>

        <div class="tarjeta">
            <h2>👤 Usuarios</h2>
            <p>Administrar usuarios y roles del sistema.</p>
        </div>

    </div>

    <br>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>

</div>

</body>
</html>