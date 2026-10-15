<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Route::get('/', function () {
    $tiposHabitacion = DB::table('habitaciones')
        ->select('nombre', 'descripcion', 'precio')
        ->selectRaw('COUNT(*) as unidades')
        ->where('disponible', true)
        ->groupBy('nombre', 'descripcion', 'precio')
        ->orderBy('precio')
        ->get();

    return view('welcome', compact('tiposHabitacion'));
})->name('public.inicio');

Route::get('/reservar/habitaciones', function (Request $request) {
    $fechas = $request->validate([
        'fecha_entrada' => 'required|date_format:Y-m-d|after_or_equal:today',
        'fecha_salida' => 'required|date_format:Y-m-d|after:fecha_entrada',
    ]);

    $habitaciones = DB::table('habitaciones')
        ->where('disponible', true)
        ->whereNotExists(function ($query) use ($fechas) {
            $query->selectRaw('1')
                ->from('reservaciones')
                ->whereColumn('reservaciones.habitacion_id', 'habitaciones.id')
                ->where('reservaciones.estado', '!=', 'cancelada')
                ->where('reservaciones.fecha_entrada', '<', $fechas['fecha_salida'])
                ->where('reservaciones.fecha_salida', '>', $fechas['fecha_entrada']);
        })
        ->orderBy('precio')
        ->orderBy('numero')
        ->get()
        ->groupBy('nombre');

    return view('publico.habitaciones', compact('habitaciones', 'fechas'));
})->name('public.reservar.habitaciones');

Route::get('/reservar/checkout', function (Request $request) {
    $datos = $request->validate([
        'habitacion_id' => 'required|integer|exists:habitaciones,id',
        'fecha_entrada' => 'required|date_format:Y-m-d|after_or_equal:today',
        'fecha_salida' => 'required|date_format:Y-m-d|after:fecha_entrada',
    ]);

    $habitacion = DB::table('habitaciones')
        ->where('id', $datos['habitacion_id'])
        ->where('disponible', true)
        ->first();

    $ocupada = ! $habitacion || DB::table('reservaciones')
        ->where('habitacion_id', $datos['habitacion_id'])
        ->where('estado', '!=', 'cancelada')
        ->where('fecha_entrada', '<', $datos['fecha_salida'])
        ->where('fecha_salida', '>', $datos['fecha_entrada'])
        ->exists();

    if ($ocupada) {
        return redirect()->route('public.reservar.habitaciones', [
            'fecha_entrada' => $datos['fecha_entrada'],
            'fecha_salida' => $datos['fecha_salida'],
        ])->with('availability_error', 'Esa habitación acaba de reservarse. Elige otra disponible.');
    }

    $noches = (int) Carbon::parse($datos['fecha_entrada'])
        ->diffInDays(Carbon::parse($datos['fecha_salida']));
    $total = (float) $habitacion->precio * $noches;

    return view('publico.checkout', compact('habitacion', 'datos', 'noches', 'total'));
})->name('public.reservar.checkout');

Route::post('/reservar/checkout', function (Request $request) {
    $numeroTarjeta = preg_replace('/\D/', '', (string) $request->input('numero_tarjeta', ''));
    $datosValidacion = $request->except(['numero_tarjeta', 'codigo_seguridad']);
    $datosValidacion['numero_tarjeta'] = $numeroTarjeta;
    $datosValidacion['codigo_seguridad'] = (string) $request->input('codigo_seguridad', '');

    $validador = Validator::make($datosValidacion, [
        'habitacion_id' => 'required|integer|exists:habitaciones,id',
        'fecha_entrada' => 'required|date_format:Y-m-d|after_or_equal:today',
        'fecha_salida' => 'required|date_format:Y-m-d|after:fecha_entrada',
        'nombre' => 'required|string|max:255',
        'apellido_paterno' => 'required|string|max:255',
        'apellido_materno' => 'required|string|max:255',
        'numero' => 'required|string|max:50',
        'email' => 'required|email|max:255',
        'titular_tarjeta' => 'required|string|max:255',
        'numero_tarjeta' => 'required|digits_between:13,19',
        'vencimiento_tarjeta' => 'required|date_format:Y-m',
        'codigo_seguridad' => 'required|digits_between:3,4',
    ]);

    if ($validador->fails()) {
        return back()
            ->withErrors($validador)
            ->withInput($request->except(['numero_tarjeta', 'codigo_seguridad']));
    }

    $datos = $validador->validated();
    $vencimiento = Carbon::createFromFormat('!Y-m', $datos['vencimiento_tarjeta']);

    if ($vencimiento->endOfMonth()->isBefore(now())) {
        return back()
            ->withErrors(['vencimiento_tarjeta' => 'La tarjeta debe estar vigente.'])
            ->withInput($request->except(['numero_tarjeta', 'codigo_seguridad']));
    }

    $ultimos4 = substr($datos['numero_tarjeta'], -4);
    unset($datos['numero_tarjeta'], $datos['codigo_seguridad']);
    $noches = (int) Carbon::parse($datos['fecha_entrada'])
        ->diffInDays(Carbon::parse($datos['fecha_salida']));

    $confirmacion = DB::transaction(function () use ($datos, $ultimos4, $noches) {
        $habitacion = DB::table('habitaciones')
            ->where('id', $datos['habitacion_id'])
            ->where('disponible', true)
            ->first();

        if (! $habitacion) {
            return null;
        }

        $ocupada = DB::table('reservaciones')
            ->where('habitacion_id', $habitacion->id)
            ->where('estado', '!=', 'cancelada')
            ->where('fecha_entrada', '<', $datos['fecha_salida'])
            ->where('fecha_salida', '>', $datos['fecha_entrada'])
            ->exists();

        if ($ocupada) {
            return null;
        }

        $email = strtolower(trim($datos['email']));
        $huesped = DB::table('usuarios')
            ->where('tipo_usuario', 'huesped')
            ->where('email', $email)
            ->first();

        $perfil = [
            'nombre' => trim($datos['nombre']),
            'apellido_paterno' => trim($datos['apellido_paterno']),
            'apellido_materno' => trim($datos['apellido_materno']),
            'numero' => trim($datos['numero']),
            'email' => $email,
            'tipo_usuario' => 'huesped',
            'role' => 'huesped',
            'updated_at' => now(),
        ];

        if ($huesped) {
            DB::table('usuarios')->where('id', $huesped->id)->update($perfil);
            $huespedId = $huesped->id;
        } else {
            $perfil['created_at'] = now();
            $huespedId = DB::table('usuarios')->insertGetId($perfil);
        }

        $total = (float) $habitacion->precio * $noches;
        $reservacionId = DB::table('reservaciones')->insertGetId([
            'huesped_id' => $huespedId,
            'habitacion_id' => $habitacion->id,
            'fecha_entrada' => $datos['fecha_entrada'],
            'fecha_salida' => $datos['fecha_salida'],
            'total' => $total,
            'estado' => 'pagada',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pagos')->insert([
            'reservacion_id' => $reservacionId,
            'metodo_pago' => 'tarjeta',
            'monto' => $total,
            'estado' => 'pagado',
            'fecha_pago' => now(),
            'titular_tarjeta' => trim($datos['titular_tarjeta']),
            'tarjeta_ultimos4' => $ultimos4,
            'vencimiento_tarjeta' => $datos['vencimiento_tarjeta'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'referencia' => 'H-'.now()->format('Y').'-'.str_pad((string) $reservacionId, 6, '0', STR_PAD_LEFT),
            'huesped' => trim($datos['nombre'].' '.$datos['apellido_paterno']),
            'habitacion' => $habitacion->nombre,
            'numero_habitacion' => $habitacion->numero,
            'fecha_entrada' => $datos['fecha_entrada'],
            'fecha_salida' => $datos['fecha_salida'],
            'noches' => $noches,
            'total' => $total,
            'tarjeta_ultimos4' => $ultimos4,
        ];
    });

    if (! $confirmacion) {
        return redirect()->route('public.reservar.habitaciones', [
            'fecha_entrada' => $datos['fecha_entrada'],
            'fecha_salida' => $datos['fecha_salida'],
        ])->with('availability_error', 'Esa habitación ya no está disponible para esas fechas.');
    }

    return redirect()->route('public.reserva.confirmacion')
        ->with('reserva_confirmada', $confirmacion);
})->name('public.reservar.guardar');

Route::get('/reservar/confirmacion', function () {
    $confirmacion = session('reserva_confirmada');

    if (! $confirmacion) {
        return redirect()->route('public.inicio');
    }

    return view('publico.confirmacion', compact('confirmacion'));
})->name('public.reserva.confirmacion');

Route::get('/home', function () {
    return match (auth()->user()->role) {
        'administrador' => redirect('/admin'),
        'recepcionista' => redirect('/recepcion'),
        'cocina', 'limpieza', 'mantenimiento', 'servicio_cuarto' => redirect()->route('operacion.servicios'),
        default => abort(403),
    };
})->middleware('auth');

Route::get('/admin', function () {
    $totalHuespedes = DB::table('usuarios')
        ->where('tipo_usuario', 'huesped')
        ->count();
    $totalReservaciones = DB::table('reservaciones')
        ->where('estado', '!=', 'cancelada')
        ->count();

    $ocupacionPorTipo = DB::table('habitaciones')
        ->leftJoin('reservaciones', function ($join) {
            $join->on('habitaciones.id', '=', 'reservaciones.habitacion_id')
                ->where('reservaciones.estado', '!=', 'cancelada')
                ->whereDate('reservaciones.fecha_entrada', '<=', today())
                ->whereDate('reservaciones.fecha_salida', '>', today());
        })
        ->select(
            'habitaciones.nombre',
            DB::raw('COUNT(DISTINCT habitaciones.id) as habitaciones_totales'),
            DB::raw('COUNT(DISTINCT reservaciones.habitacion_id) as habitaciones_ocupadas')
        )
        ->groupBy('habitaciones.nombre')
        ->orderByDesc('habitaciones_ocupadas')
        ->orderBy('habitaciones.nombre')
        ->get();

    return view('admin.dashboard', compact(
        'totalHuespedes',
        'totalReservaciones',
        'ocupacionPorTipo'
    ));
})->middleware(['auth', 'role:administrador'])->name('admin');

Route::get('/admin/huespedes', function () {
    $huespedes = DB::table('usuarios')
        ->where('tipo_usuario', 'huesped')
        ->orderBy('apellido_paterno')
        ->orderBy('nombre')
        ->get();

    return view('admin.huespedes', compact('huespedes'));
})->middleware(['auth', 'role:administrador'])->name('admin.huespedes.index');

Route::get('/admin/huespedes/crear', function () {
    return view('admin.crear-huesped');
})->middleware(['auth', 'role:administrador'])->name('admin.huespedes.create');

Route::post('/admin/huespedes', function (Request $request) {
    $datos = $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido_paterno' => 'required|string|max:255',
        'apellido_materno' => 'required|string|max:255',
        'numero' => 'required|string|max:50',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('usuarios', 'email')->where(fn ($query) => $query->where('tipo_usuario', 'huesped')),
        ],
    ]);

    DB::table('usuarios')->insert([
        'nombre' => $datos['nombre'],
        'apellido_paterno' => $datos['apellido_paterno'],
        'apellido_materno' => $datos['apellido_materno'],
        'numero' => $datos['numero'],
        'email' => strtolower(trim($datos['email'])),
        'tipo_usuario' => 'huesped',
        'role' => 'huesped',
        'rfc' => null,
        'nss' => null,
        'user_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('admin.huespedes.index')
        ->with('success', 'El huésped se registró correctamente.');
})->middleware(['auth', 'role:administrador'])->name('admin.huespedes.store');

Route::get('/admin/huespedes/{huespedId}/editar', function ($huespedId) {
    $huesped = DB::table('usuarios')
        ->where('tipo_usuario', 'huesped')
        ->find($huespedId);

    if (! $huesped) {
        abort(404);
    }

    return view('admin.editar-huesped', compact('huesped'));
})->middleware(['auth', 'role:administrador'])->name('admin.huespedes.edit');

Route::put('/admin/huespedes/{huespedId}', function (Request $request, $huespedId) {
    $huesped = DB::table('usuarios')
        ->where('tipo_usuario', 'huesped')
        ->find($huespedId);

    if (! $huesped) {
        abort(404);
    }

    $datos = $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido_paterno' => 'required|string|max:255',
        'apellido_materno' => 'required|string|max:255',
        'numero' => 'required|string|max:50',
        'email' => 'required|email|max:255',
    ]);

    DB::table('usuarios')
        ->where('id', $huespedId)
        ->update([
            'nombre' => $datos['nombre'],
            'apellido_paterno' => $datos['apellido_paterno'],
            'apellido_materno' => $datos['apellido_materno'],
            'numero' => $datos['numero'],
            'email' => $datos['email'],
            'updated_at' => now(),
        ]);

    return redirect()->route('admin.huespedes.index')
        ->with('success', 'Los datos del huésped se actualizaron correctamente.');
})->middleware(['auth', 'role:administrador'])->name('admin.huespedes.update');

Route::get('/admin/pagos', function () {
    $pendientes = DB::table('reservaciones')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->leftJoinSub(
            DB::table('pagos')
                ->select('reservacion_id')
                ->selectRaw('SUM(monto) as monto_pagado')
                ->where('estado', 'pagado')
                ->groupBy('reservacion_id'),
            'pagos_acumulados',
            fn ($join) => $join->on('reservaciones.id', '=', 'pagos_acumulados.reservacion_id')
        )
        ->where('reservaciones.estado', '!=', 'cancelada')
        ->whereRaw('reservaciones.total > COALESCE(pagos_acumulados.monto_pagado, 0)')
        ->select(
            'reservaciones.id',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'habitaciones.nombre as habitacion_nombre',
            'reservaciones.total'
        )
        ->selectRaw('COALESCE(pagos_acumulados.monto_pagado, 0) as monto_pagado')
        ->selectRaw('(reservaciones.total - COALESCE(pagos_acumulados.monto_pagado, 0)) as saldo_pendiente')
        ->orderByDesc('reservaciones.id')
        ->get();

    $pagos = DB::table('pagos')
        ->join('reservaciones', 'pagos.reservacion_id', '=', 'reservaciones.id')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->select(
            'pagos.id',
            'pagos.metodo_pago',
            'pagos.monto',
            'pagos.estado',
            'pagos.fecha_pago',
            'pagos.titular_tarjeta',
            'pagos.tarjeta_ultimos4',
            'pagos.vencimiento_tarjeta',
            'reservaciones.id as reservacion_id',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'habitaciones.nombre as habitacion_nombre'
        )
        ->orderByDesc('pagos.fecha_pago')
        ->get();

    return view('admin.pagos', compact('pagos', 'pendientes'));
})->middleware(['auth', 'role:administrador'])->name('admin.pagos.index');

Route::get('/admin/finanzas', function () {
    $ingresosPagados = DB::table('pagos')
        ->where('estado', 'pagado')
        ->sum('monto');

    $cantidadPagos = DB::table('reservaciones')
        ->where('estado', 'pagada')
        ->count();

    $porCobrar = DB::table('reservaciones')
        ->leftJoinSub(
            DB::table('pagos')
                ->select('reservacion_id')
                ->selectRaw('SUM(monto) as monto_pagado')
                ->where('estado', 'pagado')
                ->groupBy('reservacion_id'),
            'pagos_acumulados',
            fn ($join) => $join->on('reservaciones.id', '=', 'pagos_acumulados.reservacion_id')
        )
        ->where('reservaciones.estado', '!=', 'cancelada')
        ->whereRaw('reservaciones.total > COALESCE(pagos_acumulados.monto_pagado, 0)')
        ->sum(DB::raw('reservaciones.total - COALESCE(pagos_acumulados.monto_pagado, 0)'));

    $pagosPorMetodo = DB::table('pagos')
        ->select(
            'metodo_pago',
            DB::raw('COUNT(*) as cantidad'),
            DB::raw('SUM(monto) as total')
        )
        ->where('estado', 'pagado')
        ->groupBy('metodo_pago')
        ->orderByDesc('total')
        ->get();

    $maxIngresoMetodo = max(1, (float) $pagosPorMetodo->max('total'));

    return view('admin.finanzas', compact(
        'ingresosPagados',
        'cantidadPagos',
        'porCobrar',
        'pagosPorMetodo',
        'maxIngresoMetodo'
    ));
})->middleware(['auth', 'role:administrador'])->name('admin.finanzas');

Route::get('/admin/trabajadores', function () {
    $roles = [
        'administrador' => 'Super Gerente',
        'recepcionista' => 'Recepcionista',
        'cocina' => 'Cocina',
        'limpieza' => 'Limpieza',
        'mantenimiento' => 'Mantenimiento',
        'servicio_cuarto' => 'Servicio al cuarto',
    ];

    $trabajadores = DB::table('usuarios')
        ->where('tipo_usuario', 'trabajador')
        ->select('id', 'user_id', 'nombre as name', 'apellido_paterno', 'apellido_materno', 'rfc', 'nss', 'numero', 'email', 'role', 'created_at')
        ->orderBy('nombre')
        ->get();

    return view('admin.trabajadores', compact('trabajadores', 'roles'));
})->middleware(['auth', 'role:administrador'])->name('admin.trabajadores.index');

Route::get('/admin/trabajadores/crear', function () {
    return view('admin.crear-trabajador');
})->middleware(['auth', 'role:administrador'])->name('admin.trabajadores.create');

Route::get('/admin/trabajadores/{trabajadorId}/editar', function ($trabajadorId) {
    $trabajador = DB::table('usuarios')
        ->where('tipo_usuario', 'trabajador')
        ->find($trabajadorId);

    if (! $trabajador) {
        abort(404);
    }

    $roles = [
        'administrador' => 'Super Gerente',
        'recepcionista' => 'Recepcionista',
        'cocina' => 'Cocina',
        'limpieza' => 'Limpieza',
        'mantenimiento' => 'Mantenimiento',
        'servicio_cuarto' => 'Servicio al cuarto',
    ];

    return view('admin.editar-trabajador', compact('trabajador', 'roles'));
})->middleware(['auth', 'role:administrador'])->name('admin.trabajadores.edit');

Route::post('/admin/trabajadores', function (Request $request) {
    $datos = $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido_paterno' => 'required|string|max:255',
        'apellido_materno' => 'required|string|max:255',
        'rfc' => 'required|string|min:12|max:13',
        'nss' => 'required|digits:11',
        'numero' => 'required|string|max:50',
        'email' => 'required|email|max:255|unique:users,email',
        'role' => 'required|in:administrador,recepcionista,cocina,limpieza,mantenimiento,servicio_cuarto',
        'password' => 'required|string|min:8|confirmed',
    ]);

    DB::transaction(function () use ($datos) {
        $cuenta = User::query()->create([
            'name' => trim($datos['nombre'].' '.$datos['apellido_paterno'].' '.$datos['apellido_materno']),
            'email' => $datos['email'],
            'password' => $datos['password'],
            'role' => $datos['role'],
        ]);

        DB::table('usuarios')->insert([
            'nombre' => $datos['nombre'],
            'apellido_paterno' => $datos['apellido_paterno'],
            'apellido_materno' => $datos['apellido_materno'],
            'rfc' => strtoupper($datos['rfc']),
            'nss' => $datos['nss'],
            'numero' => $datos['numero'],
            'email' => $datos['email'],
            'tipo_usuario' => 'trabajador',
            'role' => $datos['role'],
            'user_id' => $cuenta->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    });

    return redirect()->route('admin.trabajadores.index')
        ->with('success', 'La persona trabajadora y su cuenta se registraron correctamente.');
})->middleware(['auth', 'role:administrador'])->name('admin.trabajadores.store');

Route::put('/admin/trabajadores/{trabajadorId}', function (Request $request, $trabajadorId) {
    $trabajador = DB::table('usuarios')
        ->where('tipo_usuario', 'trabajador')
        ->find($trabajadorId);

    if (! $trabajador) {
        abort(404);
    }

    if (! $trabajador->user_id) {
        abort(409, 'Este perfil laboral no tiene una cuenta de acceso asociada.');
    }

    $datos = $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido_paterno' => 'required|string|max:255',
        'apellido_materno' => 'required|string|max:255',
        'rfc' => 'required|string|min:12|max:13',
        'nss' => 'required|digits:11',
        'numero' => 'required|string|max:50',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($trabajador->user_id),
        ],
        'role' => 'required|in:administrador,recepcionista,cocina,limpieza,mantenimiento,servicio_cuarto',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    if ((int) $request->user()->id === (int) $trabajador->user_id && $datos['role'] !== 'administrador') {
        return back()->withErrors([
            'role' => 'No puedes quitarte tu propio acceso de administrador.',
        ])->withInput();
    }

    if ($trabajador->role === 'administrador' && $datos['role'] !== 'administrador') {
        $administradores = DB::table('usuarios')
            ->where('tipo_usuario', 'trabajador')
            ->where('role', 'administrador')
            ->count();

        if ($administradores <= 1) {
            return back()->withErrors([
                'role' => 'Debe permanecer al menos un administrador en el sistema.',
            ])->withInput();
        }
    }

    DB::transaction(function () use ($trabajador, $trabajadorId, $datos) {
        DB::table('usuarios')
            ->where('id', $trabajadorId)
            ->update([
                'nombre' => $datos['nombre'],
                'apellido_paterno' => $datos['apellido_paterno'],
                'apellido_materno' => $datos['apellido_materno'],
                'rfc' => strtoupper($datos['rfc']),
                'nss' => $datos['nss'],
                'numero' => $datos['numero'],
                'email' => $datos['email'],
                'role' => $datos['role'],
                'updated_at' => now(),
            ]);

        $cuenta = User::query()->findOrFail($trabajador->user_id);
        $cuenta->name = trim($datos['nombre'].' '.$datos['apellido_paterno'].' '.$datos['apellido_materno']);
        $cuenta->email = $datos['email'];
        $cuenta->role = $datos['role'];

        if (! empty($datos['password'])) {
            $cuenta->password = $datos['password'];
        }

        $cuenta->save();
    });

    return redirect()->route('admin.trabajadores.index')
        ->with('success', 'Los datos del trabajador se actualizaron correctamente.');
})->middleware(['auth', 'role:administrador'])->name('admin.trabajadores.update');

Route::get('/admin/servicios', function () {
    $tiposServicios = DB::table('tipos_servicios')
        ->orderBy('nombre')
        ->get();

    $solicitudes = DB::table('solicitudes_servicios')
        ->join('tipos_servicios', 'solicitudes_servicios.tipo_servicio_id', '=', 'tipos_servicios.id')
        ->join('reservaciones', 'solicitudes_servicios.reservacion_id', '=', 'reservaciones.id')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->select(
            'solicitudes_servicios.id',
            'solicitudes_servicios.estado',
            'solicitudes_servicios.observaciones',
            'solicitudes_servicios.created_at',
            'solicitudes_servicios.completed_at',
            'tipos_servicios.categoria',
            'tipos_servicios.nombre as servicio_nombre',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'habitaciones.nombre as habitacion_nombre',
            'habitaciones.numero as habitacion_numero'
        )
        ->orderByDesc('solicitudes_servicios.created_at')
        ->get();

    return view('admin.servicios', compact('tiposServicios', 'solicitudes'));
})->middleware(['auth', 'role:administrador'])->name('admin.servicios.index');

Route::get('/operacion/servicios', function (Request $request) {
    $role = $request->user()->role;
    $labels = [
        'cocina' => 'Cocina',
        'limpieza' => 'Limpieza',
        'mantenimiento' => 'Mantenimiento',
        'servicio_cuarto' => 'Servicio al cuarto',
    ];

    $solicitudes = DB::table('solicitudes_servicios')
        ->join('tipos_servicios', 'solicitudes_servicios.tipo_servicio_id', '=', 'tipos_servicios.id')
        ->join('reservaciones', 'solicitudes_servicios.reservacion_id', '=', 'reservaciones.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->where('tipos_servicios.categoria', $role)
        ->select(
            'solicitudes_servicios.id',
            'solicitudes_servicios.estado',
            'solicitudes_servicios.observaciones',
            'solicitudes_servicios.created_at',
            'solicitudes_servicios.completed_at',
            'tipos_servicios.nombre as servicio_nombre',
            'habitaciones.nombre as habitacion_nombre',
            'habitaciones.numero as habitacion_numero'
        )
        ->orderByRaw("CASE WHEN solicitudes_servicios.estado = 'completado' THEN 1 ELSE 0 END")
        ->orderBy('solicitudes_servicios.created_at')
        ->get();

    return view('operacion.servicios', [
        'area' => $labels[$role],
        'solicitudes' => $solicitudes,
    ]);
})->middleware(['auth', 'role:cocina,limpieza,mantenimiento,servicio_cuarto'])->name('operacion.servicios');

Route::put('/operacion/servicios/{solicitudId}/estado', function (Request $request, $solicitudId) {
    $datos = $request->validate([
        'estado' => 'required|in:en_proceso,completado',
    ]);

    $tiposServicioDelArea = DB::table('tipos_servicios')
        ->select('id')
        ->where('categoria', $request->user()->role);

    $solicitud = DB::table('solicitudes_servicios')
        ->where('id', $solicitudId)
        ->whereIn('tipo_servicio_id', $tiposServicioDelArea)
        ->where('estado', '!=', 'completado')
        ->first();

    if (! $solicitud) {
        abort(404);
    }

    DB::table('solicitudes_servicios')
        ->where('id', $solicitudId)
        ->update([
            'estado' => $datos['estado'],
            'completed_at' => $datos['estado'] === 'completado' ? now() : null,
            'updated_at' => now(),
        ]);

    return redirect()->route('operacion.servicios')
        ->with('success', 'El estado de la solicitud se actualizó.');
})->middleware(['auth', 'role:cocina,limpieza,mantenimiento,servicio_cuarto'])->name('operacion.servicios.update');

Route::get('/recepcion', function () {
    DB::table('solicitudes_servicios')
        ->where('estado', 'solicitado')
        ->where('auto_completar', true)
        ->where('created_at', '<=', now()->subMinute())
        ->update([
            'estado' => 'completado',
            'completed_at' => now(),
            'updated_at' => now(),
        ]);

    $habitacionesOcupadas = DB::table('reservaciones')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->select(
            'habitaciones.numero as habitacion_numero',
            'habitaciones.nombre as habitacion_nombre',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'reservaciones.fecha_entrada',
            'reservaciones.fecha_salida'
        )
        ->where('reservaciones.estado', '!=', 'cancelada')
        ->whereDate('reservaciones.fecha_entrada', '<=', today())
        ->whereDate('reservaciones.fecha_salida', '>', today())
        ->orderBy('habitaciones.numero')
        ->get();

    $serviciosActivos = DB::table('solicitudes_servicios')
        ->join('tipos_servicios', 'solicitudes_servicios.tipo_servicio_id', '=', 'tipos_servicios.id')
        ->join('reservaciones', 'solicitudes_servicios.reservacion_id', '=', 'reservaciones.id')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->select(
            'solicitudes_servicios.id',
            'solicitudes_servicios.estado',
            'solicitudes_servicios.observaciones',
            'solicitudes_servicios.created_at',
            'tipos_servicios.nombre as servicio_nombre',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'habitaciones.numero as habitacion_numero'
        )
        ->whereIn('solicitudes_servicios.estado', ['solicitado', 'en_proceso'])
        ->orderByRaw("CASE WHEN solicitudes_servicios.estado = 'solicitado' THEN 0 ELSE 1 END")
        ->orderBy('solicitudes_servicios.created_at')
        ->get();

    return view('recepcion.dashboard', compact('habitacionesOcupadas', 'serviciosActivos'));
})->middleware(['auth', 'role:recepcionista']);

/*
|--------------------------------------------------------------------------
| Habitaciones
|--------------------------------------------------------------------------
*/

Route::get('/habitaciones', function () {
    $habitaciones = DB::table('habitaciones')
        ->orderBy('nombre')
        ->orderBy('numero')
        ->get();

    $reservasActivas = DB::table('reservaciones')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->select(
            'reservaciones.habitacion_id',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'reservaciones.fecha_entrada',
            'reservaciones.fecha_salida'
        )
        ->where('reservaciones.estado', '!=', 'cancelada')
        ->whereDate('reservaciones.fecha_entrada', '<=', today())
        ->whereDate('reservaciones.fecha_salida', '>', today())
        ->get()
        ->keyBy('habitacion_id');

    $proximasReservas = DB::table('reservaciones')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->select(
            'reservaciones.habitacion_id',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'reservaciones.fecha_entrada',
            'reservaciones.fecha_salida'
        )
        ->where('reservaciones.estado', '!=', 'cancelada')
        ->whereDate('reservaciones.fecha_entrada', '>', today())
        ->orderBy('reservaciones.fecha_entrada')
        ->get()
        ->groupBy('habitacion_id')
        ->map(fn ($reservas) => $reservas->first());

    foreach ($habitaciones as $habitacion) {
        $habitacion->reservacion_actual = $reservasActivas->get($habitacion->id);
        $habitacion->proxima_reservacion = $proximasReservas->get($habitacion->id);
    }

    return view('recepcion.habitaciones', compact('habitaciones'));
})->middleware(['auth', 'role:recepcionista']);

/*
|--------------------------------------------------------------------------
| Huéspedes
|--------------------------------------------------------------------------
*/

Route::get('/huespedes', function () {
    $huespedes = DB::table('usuarios')
        ->where('tipo_usuario', 'huesped')
        ->orderBy('nombre')
        ->get();

    return view('recepcion.huespedes', compact('huespedes'));
})->middleware(['auth', 'role:recepcionista']);

Route::post('/huespedes', function (Request $request) {
    $datos = $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido_paterno' => 'required|string|max:255',
        'apellido_materno' => 'required|string|max:255',
        'numero' => 'required|string|max:50',
        'email' => 'required|email|max:255',
    ]);

    DB::table('usuarios')->insert([
        'nombre' => $datos['nombre'],
        'apellido_paterno' => $datos['apellido_paterno'],
        'apellido_materno' => $datos['apellido_materno'],
        'numero' => $datos['numero'],
        'email' => $datos['email'],
        'tipo_usuario' => 'huesped',
        'role' => 'huesped',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect('/huespedes')
        ->with('success', 'Huésped registrado correctamente.');
})->middleware(['auth', 'role:recepcionista']);

/*
|--------------------------------------------------------------------------
| Reservaciones
|--------------------------------------------------------------------------
*/

Route::get('/reservaciones/crear/{habitacionId}', function ($habitacionId) {
    $habitacion = DB::table('habitaciones')->find($habitacionId);

    if (! $habitacion || ! $habitacion->disponible) {
        abort(404);
    }

    $huespedes = DB::table('usuarios')
        ->where('tipo_usuario', 'huesped')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('reservaciones')
                ->whereColumn('reservaciones.huesped_id', 'usuarios.id')
                ->where('reservaciones.estado', '!=', 'cancelada')
                ->whereDate('reservaciones.fecha_entrada', '<=', today())
                ->whereDate('reservaciones.fecha_salida', '>', today());
        })
        ->orderBy('nombre')
        ->get();

    $hayHuespedesRegistrados = DB::table('usuarios')
        ->where('tipo_usuario', 'huesped')
        ->exists();

    return view('recepcion.crear', compact('habitacion', 'huespedes', 'hayHuespedesRegistrados'));
})->middleware(['auth', 'role:recepcionista']);

Route::get('/reservaciones', function () {
    $reservaciones = DB::table('reservaciones')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->select(
            'reservaciones.id',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'habitaciones.nombre as habitacion_nombre',
            'habitaciones.numero as habitacion_numero',
            'reservaciones.fecha_entrada',
            'reservaciones.fecha_salida',
            'reservaciones.total',
            'reservaciones.estado'
        )
        ->orderByDesc('reservaciones.fecha_entrada')
        ->get();

    return view('recepcion.reservaciones', compact('reservaciones'));
})->middleware(['auth', 'role:recepcionista']);

Route::post('/reservaciones', function (Request $request) {
    $datos = $request->validate([
        'habitacion_id' => 'required|exists:habitaciones,id',
        'huesped_id' => 'required|exists:usuarios,id',
        'fecha_entrada' => 'required|date|after_or_equal:today',
        'fecha_salida' => 'required|date|after:fecha_entrada',
    ]);

    $habitacion = DB::table('habitaciones')
        ->where('id', $datos['habitacion_id'])
        ->where('disponible', true)
        ->first();

    if (! $habitacion) {
        return back()
            ->withErrors([
                'habitacion_id' => 'Esta habitación no está disponible.',
            ])
            ->withInput();
    }

    $esHuesped = DB::table('usuarios')
        ->where('id', $datos['huesped_id'])
        ->where('tipo_usuario', 'huesped')
        ->exists();

    if (! $esHuesped) {
        return back()
            ->withErrors(['huesped_id' => 'Selecciona una persona registrada como huésped.'])
            ->withInput();
    }

    $huespedReservado = DB::table('reservaciones')
        ->where('huesped_id', $datos['huesped_id'])
        ->where('estado', '!=', 'cancelada')
        ->where('fecha_entrada', '<', $datos['fecha_salida'])
        ->where('fecha_salida', '>', $datos['fecha_entrada'])
        ->exists();

    if ($huespedReservado) {
        return back()
            ->withErrors([
                'huesped_id' => 'Este huésped ya tiene una reservación para esas fechas.',
            ])
            ->withInput();
    }

    $ocupada = DB::table('reservaciones')
        ->where('habitacion_id', $habitacion->id)
        ->where('estado', '!=', 'cancelada')
        ->where('fecha_entrada', '<', $datos['fecha_salida'])
        ->where('fecha_salida', '>', $datos['fecha_entrada'])
        ->exists();

    if ($ocupada) {
        return back()
            ->withErrors([
                'fecha_entrada' => 'La habitación ya está reservada para esas fechas.',
            ])
            ->withInput();
    }

    $noches = Carbon::parse($datos['fecha_entrada'])
        ->diffInDays(Carbon::parse($datos['fecha_salida']));

    DB::table('reservaciones')->insert([
        'huesped_id' => $datos['huesped_id'],
        'habitacion_id' => $habitacion->id,
        'fecha_entrada' => $datos['fecha_entrada'],
        'fecha_salida' => $datos['fecha_salida'],
        'total' => $habitacion->precio * $noches,
        'estado' => 'reservada',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect('/reservaciones')
        ->with('success', 'Reservación guardada correctamente.');
})->middleware(['auth', 'role:recepcionista']);

/*
|--------------------------------------------------------------------------
| Solicitudes de servicios para huéspedes alojados
|--------------------------------------------------------------------------
*/

Route::get('/servicios', function () {
    DB::table('solicitudes_servicios')
        ->where('estado', 'solicitado')
        ->where('auto_completar', true)
        ->where('created_at', '<=', now()->subMinute())
        ->update([
            'estado' => 'completado',
            'completed_at' => now(),
            'updated_at' => now(),
        ]);

    $tiposServicios = DB::table('tipos_servicios')
        ->where('activo', true)
        ->orderBy('nombre')
        ->get();

    $huespedesAlojados = DB::table('reservaciones')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->select(
            'reservaciones.id as reservacion_id',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'habitaciones.nombre as habitacion_nombre',
            'habitaciones.numero as habitacion_numero'
        )
        ->where('reservaciones.estado', '!=', 'cancelada')
        ->whereDate('reservaciones.fecha_entrada', '<=', today())
        ->whereDate('reservaciones.fecha_salida', '>', today())
        ->orderBy('habitaciones.numero')
        ->get();

    $solicitudes = DB::table('solicitudes_servicios')
        ->join('tipos_servicios', 'solicitudes_servicios.tipo_servicio_id', '=', 'tipos_servicios.id')
        ->join('reservaciones', 'solicitudes_servicios.reservacion_id', '=', 'reservaciones.id')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->select(
            'solicitudes_servicios.id',
            'solicitudes_servicios.estado',
            'solicitudes_servicios.observaciones',
            'solicitudes_servicios.created_at',
            'solicitudes_servicios.completed_at',
            'solicitudes_servicios.auto_completar',
            'tipos_servicios.nombre as servicio_nombre',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'habitaciones.nombre as habitacion_nombre',
            'habitaciones.numero as habitacion_numero'
        )
        ->orderByDesc('solicitudes_servicios.created_at')
        ->get();

    return view('recepcion.servicios', compact('tiposServicios', 'huespedesAlojados', 'solicitudes'));
})->middleware(['auth', 'role:recepcionista'])->name('recepcion.servicios.index');

Route::post('/servicios/solicitudes', function (Request $request) {
    $datos = $request->validate([
        'reservacion_id' => 'required|exists:reservaciones,id',
        'tipo_servicio_id' => 'required|exists:tipos_servicios,id',
        'observaciones' => 'nullable|string|max:1000',
        'auto_completar' => 'nullable|boolean',
    ]);

    $alojamientoActivo = DB::table('reservaciones')
        ->where('id', $datos['reservacion_id'])
        ->whereIn('huesped_id', DB::table('usuarios')->select('id')->where('tipo_usuario', 'huesped'))
        ->where('estado', '!=', 'cancelada')
        ->whereDate('fecha_entrada', '<=', today())
        ->whereDate('fecha_salida', '>', today())
        ->exists();

    if (! $alojamientoActivo) {
        return back()->withErrors([
            'reservacion_id' => 'Selecciona una reservación de un huésped alojado actualmente.',
        ])->withInput();
    }

    $servicioActivo = DB::table('tipos_servicios')
        ->where('id', $datos['tipo_servicio_id'])
        ->where('activo', true)
        ->exists();

    if (! $servicioActivo) {
        return back()->withErrors([
            'tipo_servicio_id' => 'Selecciona un servicio disponible.',
        ])->withInput();
    }

    DB::table('solicitudes_servicios')->insert([
        'reservacion_id' => $datos['reservacion_id'],
        'tipo_servicio_id' => $datos['tipo_servicio_id'],
        'estado' => 'solicitado',
        'observaciones' => $datos['observaciones'] ?? null,
        'auto_completar' => $datos['auto_completar'] ?? false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('recepcion.servicios.index')
        ->with('success', 'La solicitud de servicio quedó registrada.');
})->middleware(['auth', 'role:recepcionista'])->name('recepcion.servicios.store');

Route::put('/servicios/solicitudes/{solicitudId}/completar', function ($solicitudId) {
    $actualizado = DB::table('solicitudes_servicios')
        ->where('id', $solicitudId)
        ->whereIn('estado', ['solicitado', 'en_proceso'])
        ->update([
            'estado' => 'completado',
            'completed_at' => now(),
            'updated_at' => now(),
        ]);

    if (! $actualizado) {
        return back()->with('success', 'La solicitud ya estaba completada.');
    }

    return redirect()->route('recepcion.servicios.index')
        ->with('success', 'La solicitud de servicio se marcó como completada.');
})->middleware(['auth', 'role:recepcionista'])->name('recepcion.servicios.complete');

/*
|--------------------------------------------------------------------------
| Pagos: pendientes e historial
|--------------------------------------------------------------------------
*/

Route::get('/pagos', function () {
    $pendientes = DB::table('reservaciones')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->leftJoinSub(
            DB::table('pagos')
                ->select('reservacion_id')
                ->selectRaw('SUM(monto) as monto_pagado')
                ->where('estado', 'pagado')
                ->groupBy('reservacion_id'),
            'pagos_acumulados',
            fn ($join) => $join->on('reservaciones.id', '=', 'pagos_acumulados.reservacion_id')
        )
        ->where('reservaciones.estado', '!=', 'cancelada')
        ->whereRaw('reservaciones.total > COALESCE(pagos_acumulados.monto_pagado, 0)')
        ->select(
            'reservaciones.id',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'habitaciones.nombre as habitacion_nombre',
            'reservaciones.total'
        )
        ->selectRaw('COALESCE(pagos_acumulados.monto_pagado, 0) as monto_pagado')
        ->selectRaw('(reservaciones.total - COALESCE(pagos_acumulados.monto_pagado, 0)) as saldo_pendiente')
        ->orderByDesc('reservaciones.id')
        ->get();

    $pagos = DB::table('pagos')
        ->join('reservaciones', 'pagos.reservacion_id', '=', 'reservaciones.id')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->select(
            'reservaciones.id as reservacion_id',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'habitaciones.nombre as habitacion_nombre',
            'pagos.metodo_pago',
            'pagos.monto',
            'pagos.titular_tarjeta',
            'pagos.tarjeta_ultimos4',
            'pagos.fecha_pago'
        )
        ->orderByDesc('pagos.fecha_pago')
        ->get();

    return view('recepcion.pagos', compact('pendientes', 'pagos'));
})->middleware(['auth', 'role:recepcionista']);

/*
|--------------------------------------------------------------------------
| Pagos: formulario para pagar una reservación
|--------------------------------------------------------------------------
*/

Route::get('/pagos/crear/{reservacionId}', function (Request $request, $reservacionId) {
    $reservacion = DB::table('reservaciones')
        ->join('usuarios as huespedes', 'reservaciones.huesped_id', '=', 'huespedes.id')
        ->join('habitaciones', 'reservaciones.habitacion_id', '=', 'habitaciones.id')
        ->select(
            'reservaciones.id',
            'huespedes.nombre as huesped_nombre',
            'huespedes.apellido_paterno',
            'huespedes.apellido_materno',
            'habitaciones.nombre as habitacion_nombre',
            'reservaciones.fecha_entrada',
            'reservaciones.fecha_salida',
            'reservaciones.total'
        )
        ->where('reservaciones.id', $reservacionId)
        ->first();

    if (! $reservacion) {
        abort(404, 'No se encontró esa reservación.');
    }

    $reservacion->monto_pagado = (float) DB::table('pagos')
        ->where('reservacion_id', $reservacionId)
        ->where('estado', 'pagado')
        ->sum('monto');
    $reservacion->saldo_pendiente = round(
        max(0, (float) $reservacion->total - $reservacion->monto_pagado),
        2
    );

    if ($reservacion->saldo_pendiente <= 0) {
        $destino = $request->user()->role === 'administrador'
            ? route('admin.pagos.index')
            : '/pagos';

        return redirect($destino)
            ->with('success', 'Esta reservación ya está liquidada.');
    }

    return view('recepcion.pagar', compact('reservacion'));
})->middleware(['auth', 'role:recepcionista']);

/*
|--------------------------------------------------------------------------
| Pagos: guardar el pago
|--------------------------------------------------------------------------
*/

Route::post('/pagos/{reservacionId}', function (Request $request, $reservacionId) {
    $pagosSolicitados = $request->input('pagos', []);
    $pagosSolicitados = is_array($pagosSolicitados) ? array_values($pagosSolicitados) : [];

    if (! $request->boolean('agregar_segundo_pago')) {
        $pagosSolicitados = array_slice($pagosSolicitados, 0, 1);
    }

    foreach ($pagosSolicitados as $indice => $pagoSolicitado) {
        if (is_array($pagoSolicitado)
            && isset($pagoSolicitado['numero_tarjeta'])
            && is_scalar($pagoSolicitado['numero_tarjeta'])) {
            $pagosSolicitados[$indice]['numero_tarjeta'] = preg_replace(
                '/\D/',
                '',
                (string) $pagoSolicitado['numero_tarjeta']
            );
        }
    }

    $request->merge(['pagos' => $pagosSolicitados]);

    $reglas = [
        'agregar_segundo_pago' => 'sometimes|boolean',
        'pagos' => 'required|array|min:1|max:2',
        'pagos.0.metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
        'pagos.0.monto' => 'required|numeric|gt:0',
        'pagos.0.titular_tarjeta' => 'required_if:pagos.0.metodo_pago,tarjeta|nullable|string|max:255',
        'pagos.0.numero_tarjeta' => 'required_if:pagos.0.metodo_pago,tarjeta|nullable|digits_between:13,19',
        'pagos.0.vencimiento_tarjeta' => 'required_if:pagos.0.metodo_pago,tarjeta|nullable|date_format:Y-m',
        'pagos.0.codigo_seguridad' => 'required_if:pagos.0.metodo_pago,tarjeta|nullable|digits_between:3,4',
        'pagos.1.metodo_pago' => 'required_if:agregar_segundo_pago,1|nullable|in:efectivo,tarjeta,transferencia',
        'pagos.1.monto' => 'required_if:agregar_segundo_pago,1|nullable|numeric|gt:0',
        'pagos.1.titular_tarjeta' => 'required_if:pagos.1.metodo_pago,tarjeta|nullable|string|max:255',
        'pagos.1.numero_tarjeta' => 'required_if:pagos.1.metodo_pago,tarjeta|nullable|digits_between:13,19',
        'pagos.1.vencimiento_tarjeta' => 'required_if:pagos.1.metodo_pago,tarjeta|nullable|date_format:Y-m',
        'pagos.1.codigo_seguridad' => 'required_if:pagos.1.metodo_pago,tarjeta|nullable|digits_between:3,4',
    ];

    $validador = Validator::make($request->all(), $reglas);
    $inputSeguro = $request->except(['pagos']);
    $inputSeguro['pagos'] = [];

    foreach ($pagosSolicitados as $pagoSolicitado) {
        if (is_array($pagoSolicitado)) {
            $inputSeguro['pagos'][] = array_diff_key(
                $pagoSolicitado,
                array_flip(['numero_tarjeta', 'codigo_seguridad'])
            );
        }
    }

    if ($validador->fails()) {
        return back()
            ->withErrors($validador)
            ->withInput($inputSeguro);
    }

    $datos = $validador->validated();
    $totalSolicitado = round(array_sum(array_column($datos['pagos'], 'monto')), 2);

    foreach ($datos['pagos'] as $indice => $pago) {
        if ($pago['metodo_pago'] !== 'tarjeta') {
            continue;
        }

        $vencimiento = Carbon::createFromFormat('!Y-m', $pago['vencimiento_tarjeta']);

        if ($vencimiento->endOfMonth()->isBefore(now())) {
            return back()
                ->withErrors([
                    "pagos.$indice.vencimiento_tarjeta" => 'La tarjeta debe estar vigente.',
                ])
                ->withInput($inputSeguro);
        }
    }

    $resultado = DB::transaction(function () use ($reservacionId, $datos, $totalSolicitado) {
        $reservacion = DB::table('reservaciones')
            ->where('id', $reservacionId)
            ->lockForUpdate()
            ->first();

        if (! $reservacion) {
            return ['estado' => 'no_encontrada'];
        }

        $montoPagado = (float) DB::table('pagos')
            ->where('reservacion_id', $reservacionId)
            ->where('estado', 'pagado')
            ->sum('monto');
        $saldoPendiente = round(max(0, (float) $reservacion->total - $montoPagado), 2);

        if ($saldoPendiente <= 0) {
            return ['estado' => 'liquidada'];
        }

        if ($totalSolicitado > $saldoPendiente) {
            return ['estado' => 'excede_saldo', 'saldo' => $saldoPendiente];
        }

        foreach ($datos['pagos'] as $pago) {
            $registro = [
                'reservacion_id' => $reservacion->id,
                'metodo_pago' => $pago['metodo_pago'],
                'monto' => round((float) $pago['monto'], 2),
                'estado' => 'pagado',
                'fecha_pago' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($pago['metodo_pago'] === 'tarjeta') {
                $registro['titular_tarjeta'] = $pago['titular_tarjeta'];
                $registro['tarjeta_ultimos4'] = substr($pago['numero_tarjeta'], -4);
                $registro['vencimiento_tarjeta'] = $pago['vencimiento_tarjeta'];
            }

            DB::table('pagos')->insert($registro);
        }

        $montoPagado = round($montoPagado + $totalSolicitado, 2);
        $estadoReservacion = $montoPagado >= round((float) $reservacion->total, 2)
            ? 'pagada'
            : 'parcial';

        DB::table('reservaciones')
            ->where('id', $reservacion->id)
            ->update([
                'estado' => $estadoReservacion,
                'updated_at' => now(),
            ]);

        return [
            'estado' => $estadoReservacion,
            'saldo' => round(max(0, (float) $reservacion->total - $montoPagado), 2),
        ];
    });

    if ($resultado['estado'] === 'no_encontrada') {
        abort(404);
    }

    $destino = $request->user()->role === 'administrador'
        ? route('admin.pagos.index')
        : '/pagos';

    if ($resultado['estado'] === 'liquidada') {
        return redirect($destino)
            ->with('success', 'Esta reservación ya está liquidada.');
    }

    if ($resultado['estado'] === 'excede_saldo') {
        return back()
            ->withErrors([
                'pagos' => 'Los importes superan el saldo pendiente de $'.number_format($resultado['saldo'], 2).'.',
            ])
            ->withInput($inputSeguro);
    }

    $mensaje = $resultado['estado'] === 'pagada'
        ? 'Pago registrado. La reservación quedó liquidada.'
        : 'Abono registrado. Saldo pendiente: $'.number_format($resultado['saldo'], 2).'.';

    return redirect($destino)->with('success', $mensaje);
})->middleware(['auth', 'role:recepcionista']);
