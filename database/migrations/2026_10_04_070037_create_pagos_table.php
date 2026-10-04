<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reservacion_id')
                ->unique()
                ->constrained('reservaciones')
                ->cascadeOnDelete();

            $table->string('metodo_pago');
            $table->decimal('monto', 10, 2);
            $table->string('estado')->default('pagado');
            $table->timestamp('fecha_pago');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
