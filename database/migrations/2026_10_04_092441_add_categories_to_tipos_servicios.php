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
        Schema::table('tipos_servicios', function (Blueprint $table) {
            $table->string('categoria')->default('servicio_cuarto')->index();
        });

        DB::table('tipos_servicios')->where('nombre', 'Lavandería')->update(['categoria' => 'limpieza']);
        DB::table('tipos_servicios')->where('nombre', 'Alberca')->update(['categoria' => 'servicio_cuarto']);
        DB::table('tipos_servicios')->where('nombre', 'Servicio al cuarto')->update(['categoria' => 'cocina']);
        DB::table('tipos_servicios')->where('nombre', 'Cancha de tenis')->update(['categoria' => 'servicio_cuarto']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipos_servicios', function (Blueprint $table) {
            $table->dropIndex(['categoria']);
            $table->dropColumn('categoria');
        });
    }
};
