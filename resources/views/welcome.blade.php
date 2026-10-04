@extends('publico.layout')

@section('title', 'Hotel | Descubre tu próxima estancia')

@section('content')
    <section class="hero">
        <div class="hero-content">
            <div class="hero-copy">
                <span class="eyebrow">Una estancia para disfrutar</span>
                <h1>Tu próxima escapada empieza aquí.</h1>
                <p>Encuentra la habitación ideal, elige tus fechas y reserva en unos cuantos pasos.</p>
                <a class="button button-light" href="#reservar">Encuentra tu habitación <span aria-hidden="true">→</span></a>
                <div class="hero-note">Reserva en línea · Pago de prueba sin cargo bancario</div>
            </div>
        </div>
    </section>

    <form class="booking-bar" id="reservar" method="GET" action="{{ route('public.reservar.habitaciones') }}">
        <div>
            <label class="field-label" for="fecha_entrada">Llegada</label>
            <input class="field-input" id="fecha_entrada" type="date" name="fecha_entrada" min="{{ today()->toDateString() }}" value="{{ old('fecha_entrada') }}" required>
            @error('fecha_entrada') <div class="field-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="field-label" for="fecha_salida">Salida</label>
            <input class="field-input" id="fecha_salida" type="date" name="fecha_salida" min="{{ today()->addDay()->toDateString() }}" value="{{ old('fecha_salida') }}" required>
            @error('fecha_salida') <div class="field-error">{{ $message }}</div> @enderror
        </div>
        <button class="button" type="submit">Buscar disponibilidad <span aria-hidden="true">→</span></button>
    </form>

    <section class="section container" id="habitaciones">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Encuentra tu espacio</span>
                <h2>Habitaciones para cada viaje</h2>
            </div>
            <p>Elige entre las opciones del hotel. La disponibilidad y el precio se consultan directamente con tus fechas.</p>
        </div>

        @if ($tiposHabitacion->isNotEmpty())
            <div class="room-grid">
                @foreach ($tiposHabitacion as $indice => $tipo)
                    <article class="room-card">
                        <div class="room-art" aria-hidden="true"><span class="art-label">Estancia {{ str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT) }}</span></div>
                        <div class="room-info">
                            <h3>{{ $tipo->nombre }}</h3>
                            <p>{{ $tipo->descripcion ?: 'Un espacio cómodo para descansar durante tu visita.' }}</p>
                            <div class="room-price">${{ number_format((float) $tipo->precio, 2) }} <small>MXN / noche</small></div>
                            <div class="room-count">{{ $tipo->unidades }} habitaciones en esta categoría</div>
                        </div>
                    </article>
                @endforeach
            </div>
            <p class="photo-credit">
                Fotografías: <a href="https://unsplash.com/es/fotos/un-unk-con-una-cama-grande-y-un-televisor-en-la-pared-NYpT6hsGQoc" target="_blank" rel="noopener noreferrer">Unsplash</a>
                · <a href="https://unsplash.com/photos/a-bed-in-a-hotel-room-GOzvLXx4E-E" target="_blank" rel="noopener noreferrer">Unsplash</a>
            </p>
        @else
            <div class="empty-state">
                <h3>Estamos preparando las habitaciones</h3>
                <p>Vuelve pronto para consultar disponibilidad.</p>
            </div>
        @endif
    </section>

    <section class="container" id="experiencia">
        <div class="feature-strip">
            <div class="feature">
                <span class="feature-icon" aria-hidden="true">⌕</span>
                <div><strong>Elige tus fechas</strong><span>Consulta opciones para tu estancia.</span></div>
            </div>
            <div class="feature">
                <span class="feature-icon" aria-hidden="true">⌂</span>
                <div><strong>Selecciona tu habitación</strong><span>Escoge el tipo y número disponibles.</span></div>
            </div>
            <div class="feature">
                <span class="feature-icon" aria-hidden="true">✓</span>
                <div><strong>Confirma tu reservación</strong><span>Recibe el resumen al completar el formulario.</span></div>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 28px">
        <p class="notice">El pago con tarjeta se registra en modo de prueba. No se realiza ningún cargo bancario.</p>
    </div>
@endsection

@push('scripts')
<script>
    const arrivalInput = document.getElementById('fecha_entrada');
    const departureInput = document.getElementById('fecha_salida');

    arrivalInput?.addEventListener('change', () => {
        if (!arrivalInput.value) return;
        const nextDate = new Date(`${arrivalInput.value}T00:00:00`);
        nextDate.setDate(nextDate.getDate() + 1);
        const minimumDeparture = [nextDate.getFullYear(), String(nextDate.getMonth() + 1).padStart(2, '0'), String(nextDate.getDate()).padStart(2, '0')].join('-');
        departureInput.min = minimumDeparture;
        if (departureInput.value && departureInput.value < minimumDeparture) departureInput.value = minimumDeparture;
    });
</script>
@endpush
