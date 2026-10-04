<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('titular_tarjeta')->nullable();
            $table->string('tarjeta_ultimos4', 4)->nullable();
            $table->string('vencimiento_tarjeta', 7)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn([
                'titular_tarjeta',
                'tarjeta_ultimos4',
                'vencimiento_tarjeta',
            ]);
        });
    }
};
