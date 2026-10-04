<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $agregarTitular = ! Schema::hasColumn('pagos', 'titular_tarjeta');
        $agregarUltimos4 = ! Schema::hasColumn('pagos', 'tarjeta_ultimos4');
        $agregarVencimiento = ! Schema::hasColumn('pagos', 'vencimiento_tarjeta');

        if ($agregarTitular || $agregarUltimos4 || $agregarVencimiento) {
            Schema::table('pagos', function (Blueprint $table) use (
                $agregarTitular,
                $agregarUltimos4,
                $agregarVencimiento
            ) {
                if ($agregarTitular) {
                    $table->string('titular_tarjeta')->nullable();
                }

                if ($agregarUltimos4) {
                    $table->string('tarjeta_ultimos4', 4)->nullable();
                }

                if ($agregarVencimiento) {
                    $table->string('vencimiento_tarjeta', 7)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        $columnasAEliminar = [];

        foreach ([
            'titular_tarjeta',
            'tarjeta_ultimos4',
            'vencimiento_tarjeta',
        ] as $columna) {
            if (Schema::hasColumn('pagos', $columna)) {
                $columnasAEliminar[] = $columna;
            }
        }

        if ($columnasAEliminar) {
            Schema::table('pagos', function (Blueprint $table) use ($columnasAEliminar) {
                $table->dropColumn($columnasAEliminar);
            });
        }
    }
};
