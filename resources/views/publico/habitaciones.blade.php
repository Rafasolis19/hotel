@extends('publico.layout')

@section('title', 'Habitaciones disponibles | Hotel')

@section('content')
    <section class="subhero">
        <div class="container">
            <span class="eyebrow">Planea tu estancia</span>
            <h1>Habitaciones disponibles</h1>
            <p>Selecciona la categoría y el número de habitación que prefieras.</p>
            <div class="date-summary">
                <span class="date-chip">Llegada · {{ \Illuminate\Support\Carbon::parse($fechas['fecha_entrada'])->format('d/m/Y') }}</span>
                <span class="date-chip">Salida · {{ \Illuminate\Support\Carbon::parse($fechas['fecha_salida'])->format('d/m/Y') }}</span>
            </div>
        </div>
    </section>

    <section class="container results-section">
        @if (session('availability_error'))
            <div class="alert" role="alert">{{ session('availability_error') }}</div>
        @endif

        <div class="results-heading">
            <h2>Elige tu habitación</h2>
            <p>{{ $habitaciones->sum(fn ($lista) => $lista->count()) }} opciones para estas fechas</p>
        </div>

        @if ($habitaciones->isNotEmpty())
            <div class="type-list">
                @foreach ($habitaciones as $nombre => $lista)
                    @php($habitacionEjemplo = $lista->first())
                    <article class="type-card">
                        <div class="type-art" aria-hidden="true"></div>
                        <div class="type-copy">
                            <span class="eyebrow">{{ $lista->count() }} disponibles</span>
                            <h3>{{ $nombre }}</h3>
                            <p>{{ $habitacionEjemplo->descripcion ?: 'Un espacio cómodo para descansar durante tu visita.' }}</p>
                            <div class="type-meta">${{ number_format((float) $habitacionEjemplo->precio, 2) }} MXN por noche</div>
                        </div>
                        <form class="type-select" method="GET" action="{{ route('public.reservar.checkout') }}">
                            <input type="hidden" name="fecha_entrada" value="{{ $fechas['fecha_entrada'] }}">
                            <input type="hidden" name="fecha_salida" value="{{ $fechas['fecha_salida'] }}">
                            <label class="field-label" for="habitacion_{{ $habitacionEjemplo->id }}">Número de habitación</label>
                            <select class="field-select" name="habitacion_id" id="habitacion_{{ $habitacionEjemplo->id }}" required>
                                @foreach ($lista as $habitacion)
                                    <option value="{{ $habitacion->id }}">Habitación {{ $habitacion->numero }}</option>
                                @endforeach
                            </select>
                            <button class="button button-small" type="submit">Continuar con esta <span aria-hidden="true">→</span></button>
                        </form>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <h3>No encontramos habitaciones libres</h3>
                <p>Prueba con otras fechas para consultar disponibilidad.</p>
                <a class="button button-small" href="{{ route('public.inicio') }}#reservar">Cambiar fechas</a>
            </div>
        @endif
    </section>
@endsection
