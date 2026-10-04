<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#173b3c">
    <title>@yield('title', 'Hotel | Reservaciones')</title>
    <style>
        :root {
            --ink: #173b3c;
            --ink-deep: #102a2d;
            --teal: #2f7771;
            --teal-light: #e6f0eb;
            --gold: #d8b777;
            --cream: #f7f5ef;
            --paper: #fffefa;
            --muted: #687574;
            --line: #e5e7df;
            --danger: #a63e35;
            --shadow: 0 18px 50px rgba(23, 59, 60, .10);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background: var(--cream);
            color: var(--ink-deep);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.55;
        }
        a { color: inherit; }
        button, input, select { font: inherit; }
        .site-header {
            position: relative;
            z-index: 2;
            background: var(--paper);
            border-bottom: 1px solid rgba(23, 59, 60, .08);
        }
        .nav-wrap {
            width: min(1180px, calc(100% - 44px));
            min-height: 76px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--ink);
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 23px;
            font-weight: 700;
            letter-spacing: .02em;
            text-decoration: none;
        }
        .brand-mark {
            width: 35px;
            height: 35px;
            display: grid;
            place-items: center;
            border: 1px solid var(--gold);
            border-radius: 50%;
            color: var(--teal);
            font-size: 17px;
        }
        .nav-links { display: flex; align-items: center; gap: 28px; }
        .nav-links a {
            color: #425756;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }
        .nav-links a:hover { color: var(--teal); }
        .nav-links .staff-link { color: var(--teal); }
        .page-shell { min-height: calc(100vh - 150px); }
        .container { width: min(1180px, calc(100% - 44px)); margin: 0 auto; }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: var(--teal);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }
        .eyebrow::before { width: 22px; height: 1px; background: var(--gold); content: ''; }
        h1, h2, h3 { font-family: Georgia, 'Times New Roman', serif; font-weight: 500; line-height: 1.12; }
        .button {
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0 22px;
            border: 1px solid var(--teal);
            border-radius: 3px;
            background: var(--teal);
            color: white;
            cursor: pointer;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            transition: transform .18s ease, background .18s ease;
        }
        .button:hover { transform: translateY(-1px); background: var(--ink); }
        .button-light { border-color: rgba(255,255,255,.7); background: transparent; color: white; }
        .button-light:hover { background: rgba(255,255,255,.12); }
        .button-small { min-height: 42px; padding: 0 17px; }
        .field-label { display: block; margin-bottom: 7px; color: var(--ink); font-size: 12px; font-weight: 800; }
        .field-input, .field-select {
            width: 100%;
            min-height: 47px;
            padding: 10px 12px;
            border: 1px solid #ccd5cf;
            border-radius: 3px;
            background: white;
            color: var(--ink-deep);
        }
        .field-input:focus, .field-select:focus { border-color: var(--teal); outline: 3px solid rgba(47,119,113,.14); }
        .field-error { margin-top: 5px; color: var(--danger); font-size: 12px; }
        .alert {
            margin: 0 0 20px;
            padding: 13px 16px;
            border: 1px solid #eac9c3;
            border-radius: 3px;
            background: #fff2ef;
            color: #84342e;
            font-size: 13px;
        }
        .notice {
            padding: 13px 15px;
            border: 1px solid #e8d7ae;
            border-radius: 3px;
            background: #fff9e9;
            color: #665323;
            font-size: 12px;
        }
        .site-footer { margin-top: 70px; padding: 30px 0; background: var(--ink-deep); color: rgba(255,255,255,.72); }
        .footer-inner { display: flex; justify-content: space-between; gap: 20px; font-size: 12px; }

        .hero {
            position: relative;
            overflow: hidden;
            min-height: 570px;
            background: var(--ink-deep);
            color: white;
        }
        .hero::before, .hero::after { position: absolute; content: ''; pointer-events: none; }
        .hero::before {
            inset: 0;
            background:
                linear-gradient(90deg, rgba(16,42,45,.94) 0%, rgba(16,42,45,.79) 37%, rgba(16,42,45,.25) 100%),
                linear-gradient(0deg, rgba(16,42,45,.28), transparent 46%),
                url('https://images.unsplash.com/photo-1685592437742-3b56edb46b15?auto=format&fit=crop&w=1800&q=85') center 54% / cover no-repeat;
        }
        .hero::after {
            right: -80px;
            bottom: -180px;
            width: 600px;
            height: 340px;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 50%;
            box-shadow: 0 0 0 38px rgba(255,255,255,.035), 0 0 0 76px rgba(255,255,255,.025);
        }
        .hero-content {
            position: relative;
            z-index: 1;
            width: min(1180px, calc(100% - 44px));
            min-height: 570px;
            margin: 0 auto;
            display: flex;
            align-items: center;
        }
        .hero-copy { width: min(540px, 58%); padding: 76px 0 115px; }
        .hero .eyebrow { color: #e6d2a9; }
        .hero h1 { max-width: 520px; margin: 20px 0 18px; font-size: clamp(44px, 5vw, 67px); letter-spacing: -.035em; }
        .hero p { max-width: 440px; margin: 0 0 28px; color: rgba(255,255,255,.78); font-size: 16px; }
        .hero-note { margin-top: 16px; color: rgba(255,255,255,.62); font-size: 11px; }
        .booking-bar {
            position: relative;
            z-index: 2;
            width: min(1040px, calc(100% - 44px));
            margin: -43px auto 0;
            padding: 19px;
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            align-items: end;
            gap: 16px;
            border: 1px solid rgba(23,59,60,.08);
            border-radius: 4px;
            background: var(--paper);
            box-shadow: var(--shadow);
        }
        .section { padding: 88px 0 0; }
        .section-heading { display: flex; align-items: end; justify-content: space-between; gap: 30px; margin-bottom: 28px; }
        .section-heading h2 { margin: 12px 0 0; font-size: 37px; }
        .section-heading p { max-width: 390px; margin: 0; color: var(--muted); font-size: 14px; }
        .room-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; }
        .room-card { overflow: hidden; border: 1px solid var(--line); border-radius: 4px; background: var(--paper); box-shadow: 0 8px 28px rgba(23,59,60,.055); }
        .room-art { position: relative; height: 175px; overflow: hidden; background: url('https://images.unsplash.com/photo-1652161854125-1f5289c13eac?auto=format&fit=crop&w=1100&q=85') center 54% / cover no-repeat; }
        .room-art::before { position: absolute; inset: 0; background: linear-gradient(0deg, rgba(16,42,45,.45), transparent 68%); content: ''; }
        .room-art::after { content: none; }
        .room-card:nth-child(3n + 2) .room-art { background-image: url('https://images.unsplash.com/photo-1685592437742-3b56edb46b15?auto=format&fit=crop&w=1100&q=85'); }
        .room-card:nth-child(3n) .room-art { background-image: url('https://images.unsplash.com/photo-1652161854125-1f5289c13eac?auto=format&fit=crop&w=1100&q=85'); background-position: center 38%; }
        .art-label { position: absolute; z-index: 1; left: 17px; bottom: 14px; padding: 5px 9px; border: 1px solid rgba(255,255,255,.55); background: rgba(16,42,45,.35); color: white; font-size: 10px; letter-spacing: .12em; text-transform: uppercase; }
        .room-info { padding: 21px; }
        .photo-credit { margin: 14px 0 0; color: var(--muted); font-size: 11px; text-align: right; }
        .photo-credit a { color: var(--teal); }
        .room-info h3 { margin: 0 0 8px; font-size: 24px; }
        .room-info p { min-height: 43px; margin: 0; color: var(--muted); font-size: 13px; }
        .room-price { margin-top: 17px; color: var(--ink); font-size: 19px; font-weight: 800; }
        .room-price small { color: var(--muted); font-size: 11px; font-weight: 400; }
        .room-count { margin-top: 7px; color: var(--teal); font-size: 11px; font-weight: 800; }
        .room-bottom { display: flex; align-items: end; justify-content: space-between; gap: 12px; margin-top: 18px; }
        .feature-strip { margin-top: 82px; padding: 30px 0; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .feature { display: flex; gap: 13px; align-items: start; }
        .feature-icon { width: 34px; height: 34px; flex: 0 0 34px; display: grid; place-items: center; border: 1px solid #d8c292; border-radius: 50%; color: var(--teal); }
        .feature strong { display: block; margin-bottom: 3px; font-family: Georgia, 'Times New Roman', serif; font-size: 16px; font-weight: 500; }
        .feature span { color: var(--muted); font-size: 12px; }

        .subhero { padding: 48px 0 38px; background: #edf0e9; }
        .subhero h1 { margin: 10px 0 7px; font-size: clamp(34px, 4vw, 48px); }
        .subhero p { margin: 0; color: var(--muted); }
        .date-summary { margin-top: 20px; display: flex; flex-wrap: wrap; gap: 10px; }
        .date-chip { padding: 8px 12px; border: 1px solid #d8dfd7; background: rgba(255,255,255,.68); color: var(--ink); font-size: 12px; }
        .results-section { padding: 35px 0 0; }
        .results-heading { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
        .results-heading h2 { margin: 0; font-size: 28px; }
        .results-heading p { margin: 0; color: var(--muted); font-size: 13px; }
        .type-list { display: grid; gap: 16px; }
        .type-card { display: grid; grid-template-columns: 210px 1fr 245px; gap: 22px; align-items: center; padding: 17px; border: 1px solid var(--line); border-radius: 4px; background: var(--paper); }
        .type-art { height: 142px; border-radius: 3px; background: linear-gradient(145deg, #dacdaf, #91a49a 52%, #385956); position: relative; overflow: hidden; }
        .type-art::after { position: absolute; top: 20px; left: 50%; width: 72px; height: 84px; transform: translateX(-50%); border: 4px solid rgba(255,255,255,.6); background: linear-gradient(180deg, rgba(255,246,222,.65), rgba(69,105,96,.45)); content: ''; }
        .type-copy h3 { margin: 0 0 7px; font-size: 25px; }
        .type-copy p { margin: 0; color: var(--muted); font-size: 13px; }
        .type-meta { margin-top: 12px; color: var(--teal); font-size: 11px; font-weight: 800; }
        .type-select { padding-left: 18px; border-left: 1px solid var(--line); }
        .type-select .field-label { margin-top: 0; }
        .type-select .button { width: 100%; margin-top: 11px; }
        .empty-state { padding: 34px; border: 1px dashed #cbd5ce; background: rgba(255,255,255,.62); text-align: center; }
        .empty-state h3 { margin: 0 0 8px; font-size: 25px; }
        .empty-state p { color: var(--muted); }

        .checkout-wrap { padding: 48px 0; }
        .checkout-title { margin-bottom: 25px; }
        .checkout-title h1 { margin: 9px 0; font-size: 42px; }
        .checkout-title p { margin: 0; color: var(--muted); }
        .checkout-grid { display: grid; grid-template-columns: minmax(260px, .78fr) minmax(0, 1.22fr); gap: 22px; align-items: start; }
        .panel { padding: 24px; border: 1px solid var(--line); border-radius: 4px; background: var(--paper); box-shadow: 0 8px 28px rgba(23,59,60,.045); }
        .panel h2 { margin: 0 0 17px; font-size: 25px; }
        .summary-room { margin: -24px -24px 20px; min-height: 150px; position: relative; background: linear-gradient(145deg, #ddcfb5, #90a39a 48%, #34534f); }
        .summary-room::after { position: absolute; top: 27px; right: 28%; width: 86px; height: 92px; border: 5px solid rgba(255,255,255,.65); background: linear-gradient(180deg, rgba(255,246,222,.65), rgba(69,105,96,.45)); content: ''; }
        .summary-content { padding-top: 4px; }
        .summary-content h2 { margin-bottom: 5px; }
        .summary-content > p { margin: 0; color: var(--muted); font-size: 13px; }
        .summary-line { display: flex; justify-content: space-between; gap: 16px; padding: 11px 0; border-bottom: 1px solid var(--line); font-size: 13px; }
        .summary-line span:first-child { color: var(--muted); }
        .summary-total { margin-top: 5px; padding-top: 15px; font-size: 15px; font-weight: 800; }
        .summary-total strong { color: var(--teal); font-size: 21px; }
        .form-section-title { margin: 22px 0 13px; color: var(--ink); font-size: 12px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-field.full { grid-column: 1 / -1; }
        .pay-submit { width: 100%; margin-top: 19px; }
        .form-footnote { margin: 10px 0 0; color: var(--muted); font-size: 11px; text-align: center; }
        .confirmation-wrap { padding: 70px 0 40px; }
        .confirmation-card { max-width: 740px; margin: 0 auto; padding: 42px; border: 1px solid var(--line); background: var(--paper); box-shadow: var(--shadow); text-align: center; }
        .success-mark { width: 64px; height: 64px; margin: 0 auto 19px; display: grid; place-items: center; border-radius: 50%; background: #e2f0e7; color: #2d7652; font-size: 29px; }
        .confirmation-card h1 { margin: 10px 0; font-size: 43px; }
        .confirmation-card > p { color: var(--muted); }
        .confirmation-details { margin: 25px 0; padding: 18px 20px; background: #f5f4ee; text-align: left; }
        .confirmation-details .summary-line:last-child { border-bottom: 0; }
        .confirmation-actions { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }

        @media (max-width: 760px) {
            .nav-wrap { width: min(100% - 30px, 1180px); min-height: 68px; }
            .nav-links { gap: 13px; }
            .nav-links a:not(.staff-link) { display: none; }
            .brand { font-size: 20px; }
            .container, .hero-content { width: min(100% - 30px, 1180px); }
            .hero, .hero-content { min-height: 570px; }
            .hero::before { inset: 0; background-image: linear-gradient(90deg, rgba(16,42,45,.82), rgba(16,42,45,.45)), url('https://images.unsplash.com/photo-1685592437742-3b56edb46b15?auto=format&fit=crop&w=1200&q=85'); }
            .hero-copy { width: 100%; padding: 65px 0 105px; }
            .hero h1 { max-width: 540px; }
            .booking-bar { width: calc(100% - 30px); grid-template-columns: 1fr 1fr; }
            .booking-bar .button { grid-column: 1 / -1; }
            .section { padding-top: 65px; }
            .section-heading { display: block; }
            .section-heading p { margin-top: 12px; }
            .room-grid { grid-template-columns: 1fr 1fr; }
            .feature-strip { grid-template-columns: 1fr; margin-top: 56px; }
            .type-card { grid-template-columns: 150px 1fr; gap: 16px; }
            .type-art { height: 130px; }
            .type-select { grid-column: 1 / -1; padding: 15px 0 0; border-top: 1px solid var(--line); border-left: 0; }
            .type-select .button { width: auto; min-width: 200px; }
            .checkout-grid { grid-template-columns: 1fr; }
            .footer-inner { flex-direction: column; }
        }
        @media (max-width: 500px) {
            .room-grid { grid-template-columns: 1fr; }
            .booking-bar { grid-template-columns: 1fr; }
            .booking-bar .button { grid-column: auto; }
            .type-card { grid-template-columns: 1fr; }
            .type-art { height: 155px; }
            .type-select { grid-column: auto; }
            .form-grid { grid-template-columns: 1fr; }
            .form-field.full { grid-column: auto; }
            .panel { padding: 20px; }
            .summary-room { margin: -20px -20px 18px; }
            .confirmation-card { padding: 29px 20px; }
            .confirmation-card h1 { font-size: 35px; }
        }
    </style>
    @stack('head')
</head>
<body>
    <header class="site-header">
        <div class="nav-wrap">
            <a class="brand" href="{{ route('public.inicio') }}">
                <span class="brand-mark" aria-hidden="true">H</span>
                <span>HOTEL</span>
            </a>
            <nav class="nav-links" aria-label="Navegación principal">
                <a href="{{ route('public.inicio') }}#habitaciones">Habitaciones</a>
                <a href="{{ route('public.inicio') }}#experiencia">La experiencia</a>
                <a class="staff-link" href="{{ route('login') }}">Acceso de personal&nbsp; ↗</a>
            </nav>
        </div>
    </header>

    <main class="page-shell">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <span>HOTEL · Reservaciones</span>
            <span>Esta página forma parte de una demostración académica.</span>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
