<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabajadores y roles - Hotel</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; background: #f4f6f9; color: #1f2937; }
        main { max-width: 1150px; margin: 15px auto; padding: 25px; background: white; border-radius: 10px; box-shadow: 0 2px 8px #ddd; overflow-x: auto; }
        h1 { color: #172554; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #172554; color: white; }
        .actualizar { display: inline-block; padding: 9px 13px; border: 0; border-radius: 6px; background: #172554; color: white; text-decoration: none; cursor: pointer; }
        .mensaje { padding: 12px; margin-top: 15px; background: #dcfce7; color: #166534; border-radius: 6px; }
        .error { margin-top: 14px; padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 6px; }
        .volver { display: inline-block; margin-top: 20px; padding: 10px 15px; border-radius: 6px; background: #dc2626; color: white; text-decoration: none; }
        .nuevo { display: inline-block; margin-top: 8px; padding: 10px 15px; border-radius: 6px; background: #0f766e; color: white; text-decoration: none; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
</head>
<body>
<main>
    <h1>Trabajadores y roles</h1>
    <p>Consulta los datos laborales y administra quién puede entrar al panel de administración o recepción.</p>

    <a class="nuevo" href="{{ route('admin.trabajadores.create') }}">＋ Registrar nueva persona</a>

    @if (session('success'))
        <div class="mensaje">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>RFC</th>
                <th>NSS</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Rol actual</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($trabajadores as $trabajador)
                <tr>
                    <td>{{ $trabajador->name }} {{ $trabajador->apellido_paterno }} {{ $trabajador->apellido_materno }}</td>
                    <td>{{ $trabajador->rfc ?: '—' }}</td>
                    <td>{{ $trabajador->nss ?: '—' }}</td>
                    <td>{{ $trabajador->numero ?: '—' }}</td>
                    <td>{{ $trabajador->email }}</td>
                    <td>{{ $roles[$trabajador->role] ?? ucfirst($trabajador->role) }}</td>
                    <td>
                        <a class="actualizar" href="{{ route('admin.trabajadores.edit', $trabajador->id) }}">Actualizar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay trabajadores registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a class="volver" href="{{ route('admin') }}">← Volver al panel</a>
</main>
</body>
</html>
