<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('huesped_id')
                ->constrained('huespedes')
                ->onDelete('cascade');

            $table->foreignId('habitacion_id')
                ->constrained('habitaciones')
                ->onDelete('cascade');

            $table->date('fecha_entrada');
            $table->date('fecha_salida');
            $table->decimal('total', 10, 2);
            $table->string('estado')->default('reservada');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservaciones');
    }
};