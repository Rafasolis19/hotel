<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('habitaciones', function (Blueprint $table) {
            $table->string('numero')->nullable()->unique();
        });

        $tiposHabitacion = [
            'Habitación sencilla' => 100,
            'Habitación doble' => 200,
            'Habitación triple' => 300,
            'Habitación con bañera' => 400,
            'Suite presidencial' => 500,
        ];

        foreach ($tiposHabitacion as $nombre => $centena) {
            $habitacionesExistentes = DB::table('habitaciones')
                ->where('nombre', $nombre)
                ->orderBy('id')
                ->get();

            if ($habitacionesExistentes->isEmpty()) {
                continue;
            }

            foreach ($habitacionesExistentes as $indice => $habitacion) {
                DB::table('habitaciones')
                    ->where('id', $habitacion->id)
                    ->update(['numero' => (string) ($centena + $indice + 1)]);
            }

            $habitacionBase = $habitacionesExistentes->first();

            for ($indice = $habitacionesExistentes->count(); $indice < 20; $indice++) {
                DB::table('habitaciones')->insert([
                    'nombre' => $habitacionBase->nombre,
                    'descripcion' => $habitacionBase->descripcion,
                    'precio' => $habitacionBase->precio,
                    'disponible' => $habitacionBase->disponible,
                    'numero' => (string) ($centena + $indice + 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::create('tipos_servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('tipos_servicios')->insert([
            [
                'nombre' => 'Lavandería',
                'descripcion' => 'Recolección y entrega de prendas.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Alberca',
                'descripcion' => 'Acceso o asistencia para el área de alberca.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Servicio al cuarto',
                'descripcion' => 'Atención o alimentos directamente en la habitación.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Cancha de tenis',
                'descripcion' => 'Solicitud de uso de la cancha de tenis.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::create('solicitudes_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservacion_id')
                ->constrained('reservaciones')
                ->cascadeOnDelete();
            $table->foreignId('tipo_servicio_id')
                ->constrained('tipos_servicios')
                ->restrictOnDelete();
            $table->string('estado')->default('solicitado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_servicios');
        Schema::dropIfExists('tipos_servicios');

        Schema::table('habitaciones', function (Blueprint $table) {
            $table->dropUnique('habitaciones_numero_unique');
            $table->dropColumn('numero');
        });
    }
};
