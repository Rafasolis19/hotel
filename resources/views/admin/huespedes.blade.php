<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar huéspedes - Hotel</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; background: #f4f6f9; color: #1f2937; }
        main { max-width: 1150px; margin: 15px auto; padding: 25px; background: white; border-radius: 10px; box-shadow: 0 2px 8px #ddd; overflow-x: auto; }
        h1 { color: #172554; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #172554; color: white; }
        .boton { display: inline-block; padding: 9px 13px; border-radius: 6px; background: #172554; color: white; text-decoration: none; }
        .volver { display: inline-block; margin-top: 20px; padding: 10px 15px; border-radius: 6px; background: #dc2626; color: white; text-decoration: none; }
        .mensaje { padding: 12px; margin-top: 15px; background: #dcfce7; color: #166534; border-radius: 6px; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <h1>Huéspedes</h1>
    <p>Desde esta pantalla el administrador puede corregir los datos registrados.</p>

    @if (session('success'))
        <div class="mensaje">{{ session('success') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido paterno</th>
                <th>Apellido materno</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($huespedes as $huesped)
                <tr>
                    <td>{{ $huesped->nombre }}</td>
                    <td>{{ $huesped->apellido_paterno }}</td>
                    <td>{{ $huesped->apellido_materno }}</td>
                    <td>{{ $huesped->numero }}</td>
                    <td>{{ $huesped->email }}</td>
                    <td>
                        <a class="boton" href="{{ route('admin.huespedes.edit', $huesped->id) }}">Editar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No hay huéspedes registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a class="volver" href="{{ route('admin') }}">← Volver al panel</a>
</main>
</body>
</html>
