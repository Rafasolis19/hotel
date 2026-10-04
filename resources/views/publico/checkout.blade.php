@extends('publico.layout')

@section('title', 'Completa tu reservación | Hotel')

@section('content')
    <section class="container checkout-wrap">
        <div class="checkout-title">
            <span class="eyebrow">Último paso</span>
            <h1>Completa tu reservación</h1>
            <p>Revisa tu estancia e ingresa los datos del huésped y de pago.</p>
        </div>

        @if ($errors->any())
            <div class="alert" role="alert">
                <strong>Revisa los datos:</strong>
                <ul style="margin: 6px 0 0; padding-left: 20px">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="checkout-grid">
            <aside class="panel">
                <div class="summary-room" aria-hidden="true"></div>
                <div class="summary-content">
                    <span class="eyebrow">Tu estancia</span>
                    <h2>{{ $habitacion->nombre }}</h2>
                    <p>Habitación {{ $habitacion->numero }} · {{ $habitacion->descripcion }}</p>
                    <div class="summary-line"><span>Llegada</span><strong>{{ \Illuminate\Support\Carbon::parse($datos['fecha_entrada'])->format('d/m/Y') }}</strong></div>
                    <div class="summary-line"><span>Salida</span><strong>{{ \Illuminate\Support\Carbon::parse($datos['fecha_salida'])->format('d/m/Y') }}</strong></div>
                    <div class="summary-line"><span>Duración</span><strong>{{ $noches }} {{ $noches === 1 ? 'noche' : 'noches' }}</strong></div>
                    <div class="summary-line"><span>Precio por noche</span><strong>${{ number_format((float) $habitacion->precio, 2) }}</strong></div>
                    <div class="summary-line summary-total"><span>Total</span><strong>${{ number_format($total, 2) }} MXN</strong></div>
                </div>
            </aside>

            <form class="panel" method="POST" action="{{ route('public.reservar.guardar') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="habitacion_id" value="{{ $habitacion->id }}">
                <input type="hidden" name="fecha_entrada" value="{{ $datos['fecha_entrada'] }}">
                <input type="hidden" name="fecha_salida" value="{{ $datos['fecha_salida'] }}">

                <h2>Datos del huésped</h2>
                <div class="form-grid">
                    <div class="form-field">
                        <label class="field-label" for="nombre">Nombre</label>
                        <input class="field-input" id="nombre" name="nombre" value="{{ old('nombre') }}" autocomplete="given-name" maxlength="255" required>
                        @error('nombre') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field">
                        <label class="field-label" for="apellido_paterno">Apellido paterno</label>
                        <input class="field-input" id="apellido_paterno" name="apellido_paterno" value="{{ old('apellido_paterno') }}" autocomplete="family-name" maxlength="255" required>
                        @error('apellido_paterno') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field">
                        <label class="field-label" for="apellido_materno">Apellido materno</label>
                        <input class="field-input" id="apellido_materno" name="apellido_materno" value="{{ old('apellido_materno') }}" maxlength="255" required>
                        @error('apellido_materno') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field">
                        <label class="field-label" for="numero">Teléfono</label>
                        <input class="field-input" id="numero" name="numero" type="tel" value="{{ old('numero') }}" autocomplete="tel" maxlength="50" required>
                        @error('numero') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field full">
                        <label class="field-label" for="email">Correo electrónico</label>
                        <input class="field-input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required>
                        @error('email') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-section-title">Pago con tarjeta</div>
                <div class="notice">El pago se registra en modo de prueba; no se realiza ningún cargo bancario. Solo se conservan el titular, el vencimiento y los últimos cuatro dígitos. El número completo y el código de seguridad no se almacenan.</div>

                <div class="form-grid" style="margin-top: 15px">
                    <div class="form-field full">
                        <label class="field-label" for="titular_tarjeta">Nombre en la tarjeta</label>
                        <input class="field-input" id="titular_tarjeta" name="titular_tarjeta" value="{{ old('titular_tarjeta') }}" maxlength="255" autocomplete="off" required>
                        @error('titular_tarjeta') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field full">
                        <label class="field-label" for="numero_tarjeta">Número de tarjeta</label>
                        <input class="field-input" id="numero_tarjeta" name="numero_tarjeta" type="text" inputmode="numeric" placeholder="0000 0000 0000 0000" maxlength="23" autocomplete="off" required>
                        @error('numero_tarjeta') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field">
                        <label class="field-label" for="vencimiento_tarjeta">Vencimiento</label>
                        <input class="field-input" id="vencimiento_tarjeta" name="vencimiento_tarjeta" type="month" min="{{ now()->format('Y-m') }}" value="{{ old('vencimiento_tarjeta') }}" autocomplete="off" required>
                        @error('vencimiento_tarjeta') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field">
                        <label class="field-label" for="codigo_seguridad">Código de seguridad</label>
                        <input class="field-input" id="codigo_seguridad" name="codigo_seguridad" type="password" inputmode="numeric" maxlength="4" placeholder="•••" autocomplete="new-password" required>
                        @error('codigo_seguridad') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button class="button pay-submit" type="submit">Confirmar reservación · ${{ number_format($total, 2) }} MXN</button>
                <p class="form-footnote">Al confirmar, se registrarán la reservación y el pago de prueba.</p>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.getElementById('numero_tarjeta')?.addEventListener('input', (event) => {
        const digits = event.target.value.replace(/\D/g, '').slice(0, 19);
        event.target.value = digits.replace(/(.{4})/g, '$1 ').trim();
    });

    document.getElementById('codigo_seguridad')?.addEventListener('input', (event) => {
        event.target.value = event.target.value.replace(/\D/g, '').slice(0, 4);
    });
</script>
@endpush
