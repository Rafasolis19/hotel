<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });

        DB::table('habitaciones')->insert([
            [
                'nombre' => 'Habitación sencilla',
                'descripcion' => 'Habitación para una persona.',
                'precio' => 1000,
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Habitación doble',
                'descripcion' => 'Habitación para dos personas.',
                'precio' => 1500,
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Habitación triple',
                'descripcion' => 'Habitación para tres personas.',
                'precio' => 2000,
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Habitación con bañera',
                'descripcion' => 'Habitación con bañera privada.',
                'precio' => 2500,
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Suite presidencial',
                'descripcion' => 'Suite de lujo para una estancia especial.',
                'precio' => 3000,
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};
