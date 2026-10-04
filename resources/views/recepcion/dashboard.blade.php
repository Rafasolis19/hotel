<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#173b3c">
    <title>Recepción | Hotel</title>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
    <style>
        body { min-height: 100vh; }
        .reception-header {
            min-height: 82px;
            padding: 18px clamp(22px, 4vw, 70px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            background: var(--hotel-ink-deep);
            color: white;
            border-bottom: 2px solid var(--hotel-gold);
        }
        .reception-brand { display: flex; align-items: center; gap: 14px; color: white; font-family: Georgia, 'Times New Roman', serif; font-size: 27px; }
        .reception-brand-mark { width: 44px; height: 44px; display: grid; place-items: center; border: 1px solid var(--hotel-gold); border-radius: 50%; color: var(--hotel-gold); font-size: 21px; }
        .reception-header small { color: rgba(255,255,255,.82); font-size: 15px; }
        .reception-layout { width: min(1920px, calc(100% - 64px)); margin: 36px auto 60px; display: grid; grid-template-columns: 310px minmax(0, 1fr); gap: 30px; align-items: start; }
        .reception-sidebar, .dashboard-panel { border: 1px solid var(--hotel-line); border-radius: 6px; background: var(--hotel-paper); box-shadow: var(--hotel-shadow); }
        .reception-sidebar { position: sticky; top: 20px; overflow: hidden; }
        .sidebar-title { margin: 0; padding: 25px 24px 16px; color: var(--hotel-muted); font-size: 13px; letter-spacing: .14em; text-transform: uppercase; }
        .reception-nav { display: grid; padding: 0 13px 16px; }
        .reception-nav a { display: flex; align-items: center; gap: 13px; padding: 16px 12px; border-radius: 4px; color: var(--hotel-ink) !important; font-size: 17px; font-weight: 700; text-decoration: none; }
        .reception-nav a:hover, .reception-nav a[aria-current="page"] { background: #eaf1eb; color: var(--hotel-teal) !important; }
        .nav-icon { width: 25px; text-align: center; font-size: 21px; }
        .sidebar-logout { padding: 17px 22px 22px; border-top: 1px solid var(--hotel-line); }
        .sidebar-logout button { width: 100%; }
        .reception-main { min-width: 0; padding: 0 !important; border: 0 !important; border-radius: 0 !important; background: transparent !important; box-shadow: none !important; }
        .reception-hero {
            position: relative;
            min-height: 285px;
            padding: 38px clamp(26px, 4vw, 54px);
            display: flex;
            align-items: end;
            overflow: hidden;
            border-radius: 6px;
            background: linear-gradient(90deg, rgba(16,42,45,.91), rgba(16,42,45,.58) 58%, rgba(16,42,45,.18)), url('https://images.unsplash.com/photo-1685592437742-3b56edb46b15?auto=format&fit=crop&w=1600&q=85') center 52% / cover no-repeat;
            box-shadow: var(--hotel-shadow);
            color: white;
        }
        .reception-hero h1 { margin: 0 0 11px; color: white !important; font-family: Georgia, 'Times New Roman', serif; font-size: clamp(40px, 4vw, 58px); font-weight: 500; }
        .reception-hero p { margin: 0; color: rgba(255,255,255,.88); font-size: 18px; }
        .overview-grid { margin-top: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .dashboard-panel { min-width: 0; overflow: hidden; }
        .panel-heading { padding: 23px 26px; display: flex; align-items: center; justify-content: space-between; gap: 10px; border-bottom: 1px solid var(--hotel-line); }
        .panel-heading h2 { margin: 0; color: var(--hotel-ink) !important; font-family: Georgia, 'Times New Roman', serif; font-size: 28px; font-weight: 500; }
        .count-badge { min-width: 38px; padding: 6px 11px; border-radius: 20px; background: #e5efea; color: var(--hotel-teal); font-size: 15px; font-weight: 800; text-align: center; }
        .table-scroll { overflow-x: auto; }
        .dashboard-table { width: 100%; border-collapse: collapse; font-size: 16px; line-height: 1.5; }
        .dashboard-table th { padding: 15px 17px; background: #f1f3ed !important; color: var(--hotel-muted) !important; font-size: 12px; letter-spacing: .08em; text-align: left; text-transform: uppercase; white-space: nowrap; }
        .dashboard-table td { padding: 19px 17px; border-bottom: 1px solid var(--hotel-line); vertical-align: top; }
        .dashboard-table tbody tr:last-child td { border-bottom: 0; }
        .room-number { color: var(--hotel-teal); font-size: 17px; font-weight: 800; white-space: nowrap; }
        .cell-subtitle { display: block; margin-top: 4px; color: var(--hotel-muted); font-size: 13px; }
        .status-pill { display: inline-block; padding: 6px 10px; border-radius: 20px; background: #fff1d6; color: #805b16; font-size: 12px; font-weight: 800; white-space: nowrap; }
        .status-pill.progress { background: #e5efff; color: #345d99; }
        .empty-row { padding: 24px !important; color: var(--hotel-muted); text-align: center; }
        .photo-credit { margin: 12px 3px 0; color: var(--hotel-muted); font-size: 12px; text-align: right; }
        .photo-credit a { color: var(--hotel-teal); }
        @media (max-width: 1050px) { .overview-grid { grid-template-columns: 1fr; } }
        @media (max-width: 760px) {
            .reception-layout { width: min(100% - 24px, 700px); margin-top: 14px; grid-template-columns: 1fr; gap: 16px; }
            .reception-sidebar { position: static; }
            .sidebar-title { padding-bottom: 8px; }
            .reception-nav { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .sidebar-logout { padding: 12px; }
            .sidebar-logout button { width: auto; }
            .reception-hero { min-height: 220px; }
            .dashboard-table { min-width: 570px; }
        }
        @media (max-width: 430px) {
            .reception-header { align-items: flex-start; flex-direction: column; }
            .reception-header small { padding-left: 48px; }
            .reception-nav { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="reception-header">
        <div class="reception-brand"><span class="reception-brand-mark" aria-hidden="true">H</span><span>HOTEL</span></div>
        <small>Panel de recepción · {{ auth()->user()->name }}</small>
    </header>

    <div class="reception-layout">
        <aside class="reception-sidebar">
            <h2 class="sidebar-title">Menú de recepción</h2>
            <nav class="reception-nav" aria-label="Menú de recepción">
                <a href="/recepcion" aria-current="page"><span class="nav-icon">⌂</span>Resumen</a>
                <a href="/huespedes"><span class="nav-icon">♙</span>Huéspedes</a>
                <a href="/habitaciones"><span class="nav-icon">▰</span>Habitaciones</a>
                <a href="/reservaciones"><span class="nav-icon">▦</span>Reservaciones</a>
                <a href="/pagos"><span class="nav-icon">▤</span>Pagos</a>
                <a href="{{ route('recepcion.servicios.index') }}"><span class="nav-icon">♧</span>Servicios</a>
            </nav>
            <form class="sidebar-logout" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        </aside>

        <main class="reception-main">
            <section class="reception-hero">
                <div>
                    <h1>Bienvenido, {{ auth()->user()->name }}</h1>
                    <p>Vista general de habitaciones ocupadas y servicios que requieren atención.</p>
                </div>
            </section>

            <div class="overview-grid">
                <section class="dashboard-panel">
                    <div class="panel-heading">
                        <h2>Habitaciones ocupadas</h2>
                        <span class="count-badge">{{ $habitacionesOcupadas->count() }}</span>
                    </div>
                    <div class="table-scroll">
                        <table class="dashboard-table">
                            <thead>
                                <tr><th>Habitación</th><th>Huésped</th><th>Salida</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($habitacionesOcupadas as $habitacion)
                                    <tr>
                                        <td><span class="room-number">N.º {{ $habitacion->habitacion_numero }}</span><span class="cell-subtitle">{{ $habitacion->habitacion_nombre }}</span></td>
                                        <td>{{ $habitacion->huesped_nombre }} {{ $habitacion->apellido_paterno }} {{ $habitacion->apellido_materno }}</td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($habitacion->fecha_salida)->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="empty-row" colspan="3">No hay habitaciones ocupadas hoy.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="dashboard-panel">
                    <div class="panel-heading">
                        <h2>Servicios activos</h2>
                        <span class="count-badge">{{ $serviciosActivos->count() }}</span>
                    </div>
                    <div class="table-scroll">
                        <table class="dashboard-table">
                            <thead>
                                <tr><th>Servicio</th><th>Habitación / huésped</th><th>Estado</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($serviciosActivos as $servicio)
                                    <tr>
                                        <td>{{ $servicio->servicio_nombre }}<span class="cell-subtitle">{{ \Illuminate\Support\Carbon::parse($servicio->created_at)->format('d/m H:i') }}</span></td>
                                        <td><span class="room-number">N.º {{ $servicio->habitacion_numero }}</span><span class="cell-subtitle">{{ $servicio->huesped_nombre }} {{ $servicio->apellido_paterno }}</span></td>
                                        <td><span class="status-pill {{ $servicio->estado === 'en_proceso' ? 'progress' : '' }}">{{ $servicio->estado === 'en_proceso' ? 'En proceso' : 'Solicitado' }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td class="empty-row" colspan="3">No hay servicios activos en este momento.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
            <p class="photo-credit">Fotografía de habitación: <a href="https://unsplash.com/es/fotos/un-unk-con-una-cama-grande-y-un-televisor-en-la-pared-NYpT6hsGQoc" target="_blank" rel="noopener noreferrer">Unsplash</a></p>
        </main>
    </div>
</body>
</html>
