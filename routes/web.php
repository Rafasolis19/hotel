<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    if (auth()->user()->role === 'administrador') {
        return redirect('/admin');
    }

    return redirect('/recepcion');
})->middleware('auth');

Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'role:administrador']);

Route::get('/recepcion', function () {
    return view('recepcion.dashboard');
})->middleware(['auth', 'role:recepcionista']);

Route::get('/huespedes', function () {
    $huespedes = DB::table('huespedes')->get();

    return view('recepcion.huespedes', compact('huespedes'));
})->middleware(['auth', 'role:recepcionista']);

Route::post('/huespedes', function (Request $request) {
    DB::table('huespedes')->insert([
        'nombre' => $request->nombre,
        'apellido paterno' => $request->apellido_paterno,
        'apellido materno' => $request->apellido_materno,
        'numero ' => $request->numero,
        'email' => $request->email,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect('/recepcion');
})->middleware(['auth', 'role:recepcionista']);