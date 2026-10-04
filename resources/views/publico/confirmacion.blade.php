@extends('publico.layout')

@section('title', 'Reservación confirmada | Hotel')

@section('content')
    <section class="container confirmation-wrap">
        <div class="confirmation-card">
            <div class="success-mark" aria-hidden="true">✓</div>
            <span class="eyebrow">Todo listo</span>
            <h1>Reservación confirmada</h1>
            <p>Gracias, {{ $confirmacion['huesped'] }}. La habitación quedó reservada para las fechas elegidas.</p>

            <div class="confirmation-details">
                <div class="summary-line"><span>Referencia</span><strong>{{ $confirmacion['referencia'] }}</strong></div>
                <div class="summary-line"><span>Habitación</span><strong>{{ $confirmacion['nombre_habitacion'] ?? $confirmacion['habitacion'] }} · {{ $confirmacion['numero_habitacion'] }}</strong></div>
                <div class="summary-line"><span>Llegada</span><strong>{{ \Illuminate\Support\Carbon::parse($confirmacion['fecha_entrada'])->format('d/m/Y') }}</strong></div>
                <div class="summary-line"><span>Salida</span><strong>{{ \Illuminate\Support\Carbon::parse($confirmacion['fecha_salida'])->format('d/m/Y') }}</strong></div>
                <div class="summary-line"><span>Pago de prueba</span><strong>Tarjeta terminada en {{ $confirmacion['tarjeta_ultimos4'] }}</strong></div>
                <div class="summary-line"><span>Total</span><strong>${{ number_format((float) $confirmacion['total'], 2) }} MXN</strong></div>
            </div>

            <p class="notice" style="text-align: left">El pago se registró en modo de prueba y no representa un cargo bancario real.</p>
            <div class="confirmation-actions" style="margin-top: 23px">
                <a class="button" href="{{ route('public.inicio') }}">Volver al inicio</a>
                <a class="button button-light" style="color: var(--teal); border-color: var(--teal)" href="{{ route('login') }}">Acceso de personal</a>
            </div>
        </div>
    </section>
@endsection
