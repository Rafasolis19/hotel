<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    DB::table('solicitudes_servicios')
        ->where('estado', 'solicitado')
        ->where('auto_completar', true)
        ->where('created_at', '<=', now()->subMinute())
        ->update([
            'estado' => 'completado',
            'completed_at' => now(),
            'updated_at' => now(),
        ]);
})->everyTenSeconds()->name('complete-old-hotel-service-requests');
