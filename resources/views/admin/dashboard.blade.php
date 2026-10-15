<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#173b3c">
    <title>Administración | Hotel</title>
    <link rel="stylesheet" href="{{ asset('css/hotel-theme.css') }}">
    <style>
        body { min-height: 100vh; }
        .admin-header {
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
        .admin-brand { display: flex; align-items: center; gap: 14px; color: white; font-family: Georgia, 'Times New Roman', serif; font-size: 27px; }
        .admin-brand-mark { width: 44px; height: 44px; display: grid; place-items: center; border: 1px solid var(--hotel-gold); border-radius: 50%; color: var(--hotel-gold); font-size: 21px; }
        .admin-header small { color: rgba(255,255,255,.82); font-size: 15px; }
        .admin-layout { width: min(1920px, calc(100% - 64px)); margin: 36px auto 60px; display: grid; grid-template-columns: 310px minmax(0, 1fr); gap: 30px; align-items: start; }
        .admin-sidebar, .admin-panel { border: 1px solid var(--hotel-line); border-radius: 6px; background: var(--hotel-paper); box-shadow: var(--hotel-shadow); }
        .admin-sidebar { position: sticky; top: 20px; overflow: hidden; }
        .sidebar-title { margin: 0; padding: 25px 24px 16px; color: var(--hotel-muted) !important; font-size: 13px !important; letter-spacing: .14em; text-transform: uppercase; }
        .admin-nav { display: grid; padding: 0 13px 16px; }
        .admin-nav a { display: flex; align-items: center; gap: 13px; padding: 16px 12px; border-radius: 4px; color: var(--hotel-ink) !important; font-size: 17px; font-weight: 700; text-decoration: none; }
        .admin-nav a:hover, .admin-nav a[aria-current="page"] { background: #eaf1eb; color: var(--hotel-teal) !important; }
        .nav-icon { width: 25px; text-align: center; font-size: 21px; }
        .sidebar-logout { padding: 17px 22px 22px; border-top: 1px solid var(--hotel-line); }
        .sidebar-logout button { width: 100%; }
        .admin-main { min-width: 0; padding: 0 !important; border: 0 !important; border-radius: 0 !important; background: transparent !important; box-shadow: none !important; }
        .admin-hero {
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
        .admin-hero h1 { margin: 0 0 11px; color: white !important; font-family: Georgia, 'Times New Roman', serif; font-size: clamp(40px, 4vw, 58px) !important; font-weight: 500; }
        .admin-hero p { margin: 0; color: rgba(255,255,255,.88); font-size: 18px !important; }
        .summary-grid { margin-top: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .admin-panel { min-width: 0; overflow: hidden; padding: 25px 27px; }
        .metric-label { margin: 0; color: var(--hotel-muted); font-size: 15px !important; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .metric-number { display: block; margin-top: 8px; color: var(--hotel-teal); font-family: Georgia, 'Times New Roman', serif; font-size: clamp(38px, 4vw, 54px); line-height: 1; }
        .chart-panel { margin-top: 24px; }
        .chart-panel h2 { margin: 0 0 8px; color: var(--hotel-ink) !important; font-family: Georgia, 'Times New Roman', serif; font-size: 30px !important; font-weight: 500; }
        .chart-note { margin: 0 0 24px; color: var(--hotel-muted); font-size: 15px !important; }
        .chart-row { display: grid; grid-template-columns: minmax(200px, 270px) minmax(150px, 1fr) 170px; align-items: center; gap: 20px; margin: 22px 0; }
        .chart-label { color: var(--hotel-ink); font-size: 17px !important; font-weight: 700; }
        .chart-track { height: 26px; overflow: hidden; border-radius: 20px; background: #e5ebe5; }
        .chart-bar { height: 100%; min-width: 0; border-radius: inherit; background: linear-gradient(90deg, #2f7771, #d8b777); }
        .chart-count { color: var(--hotel-teal); font-size: 15px !important; font-weight: 800; text-align: right; white-space: nowrap; }
        .chart-empty { color: var(--hotel-muted); }
        .photo-credit { margin: 12px 3px 0; color: var(--hotel-muted); font-size: 12px; text-align: right; }
        .photo-credit a { color: var(--hotel-teal); }
        @media (max-width: 1050px) { .summary-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 760px) {
            .admin-layout { width: min(100% - 24px, 700px); margin-top: 14px; grid-template-columns: 1fr; gap: 16px; }
            .admin-sidebar { position: static; }
            .sidebar-title { padding-bottom: 8px; }
            .admin-nav { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .sidebar-logout { padding: 12px; }
            .sidebar-logout button { width: auto; }
            .admin-hero { min-height: 220px; }
            .chart-row { grid-template-columns: 1fr; gap: 8px; }
            .chart-count { text-align: left; }
        }
        @media (max-width: 500px) {
            .admin-header { align-items: flex-start; flex-direction: column; }
            .admin-header small { padding-left: 48px; }
            .admin-nav, .summary-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-brand"><span class="admin-brand-mark" aria-hidden="true">H</span><span>HOTEL</span></div>
        <small>Panel de Super Gerencia · {{ auth()->user()->name }}</small>
    </header>

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2 class="sidebar-title">Menú de administración</h2>
            <nav class="admin-nav" aria-label="Menú de administración">
                <a href="{{ route('admin') }}" aria-current="page"><span class="nav-icon">⌂</span>Resumen</a>
                <a href="{{ route('admin.huespedes.index') }}"><span class="nav-icon">♙</span>Huéspedes</a>
                <a href="/habitaciones"><span class="nav-icon">▰</span>Habitaciones</a>
                <a href="/reservaciones"><span class="nav-icon">▦</span>Reservaciones</a>
                <a href="{{ route('admin.pagos.index') }}"><span class="nav-icon">▤</span>Pagos y cobros</a>
                <a href="{{ route('admin.finanzas') }}"><span class="nav-icon">◫</span>Finanzas</a>
                <a href="{{ route('admin.trabajadores.index') }}"><span class="nav-icon">♧</span>Trabajadores y roles</a>
                <a href="{{ route('admin.servicios.index') }}"><span class="nav-icon">▦</span>Solicitudes de servicios</a>
            </nav>
            <form class="sidebar-logout" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        </aside>

        <main class="admin-main">
            <section class="admin-hero">
                <div>
                    <h1>Bienvenido, {{ auth()->user()->name }}</h1>
                    <p>Panel de Super Gerencia · consulta la ocupación y administra el hotel desde aquí.</p>
                </div>
            </section>

            <section class="summary-grid" aria-label="Resumen del hotel">
                <article class="admin-panel">
                    <p class="metric-label">Huéspedes registrados</p>
                    <strong class="metric-number">{{ $totalHuespedes }}</strong>
                </article>
                <article class="admin-panel">
                    <p class="metric-label">Reservaciones activas</p>
                    <strong class="metric-number">{{ $totalReservaciones }}</strong>
                </article>
            </section>

            <section class="admin-panel chart-panel">
                <h2>Ocupación actual por tipo de habitación</h2>
                <p class="chart-note">Habitaciones ocupadas hoy y huéspedes alojados por tipo.</p>

                @forelse ($ocupacionPorTipo as $tipo)
                    @php
                        $porcentaje = $tipo->habitaciones_totales > 0
                            ? ($tipo->habitaciones_ocupadas / $tipo->habitaciones_totales) * 100
                            : 0;
                    @endphp
                    <div class="chart-row">
                        <span class="chart-label">{{ $tipo->nombre }}</span>
                        <div class="chart-track" role="img" aria-label="{{ $tipo->habitaciones_ocupadas }} de {{ $tipo->habitaciones_totales }} habitaciones ocupadas">
                            <div class="chart-bar" style="width: {{ $porcentaje }}%"></div>
                        </div>
                        <span class="chart-count">{{ $tipo->habitaciones_ocupadas }} / {{ $tipo->habitaciones_totales }} ocupadas</span>
                    </div>
                @empty
                    <p class="chart-empty">Todavía no hay habitaciones registradas.</p>
                @endforelse

                <p class="chart-note" style="margin: 22px 0 0">El porcentaje se calcula sobre las habitaciones numeradas de cada tipo.</p>
            </section>
            <p class="photo-credit">Fotografía de habitación: <a href="https://unsplash.com/es/fotos/un-unk-con-una-cama-grande-y-un-televisor-en-la-pared-NYpT6hsGQoc" target="_blank" rel="noopener noreferrer">Unsplash</a></p>
        </main>
    </div>
</body>
</html>
