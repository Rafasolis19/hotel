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
        Schema::rename('huespedes', 'usuarios');

        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('tipo_usuario')->default('huesped')->index();
            $table->string('role')->default('huesped');
            $table->string('rfc', 13)->nullable();
            $table->string('nss', 11)->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('apellido_paterno')->nullable()->change();
            $table->string('apellido_materno')->nullable()->change();
            $table->string('numero')->nullable()->change();
        });

        $trabajadores = DB::table('users')->orderBy('id')->get();

        foreach ($trabajadores as $trabajador) {
            DB::table('usuarios')->insert([
                'nombre' => $trabajador->name,
                'apellido_paterno' => null,
                'apellido_materno' => null,
                'numero' => null,
                'email' => $trabajador->email,
                'tipo_usuario' => 'trabajador',
                'role' => $trabajador->role ?? 'recepcionista',
                'user_id' => $trabajador->id,
                'created_at' => $trabajador->created_at,
                'updated_at' => $trabajador->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('usuarios')
            ->where('tipo_usuario', 'trabajador')
            ->delete();

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique('usuarios_user_id_unique');
            $table->dropIndex('usuarios_tipo_usuario_index');
            $table->dropColumn(['tipo_usuario', 'role', 'rfc', 'nss', 'user_id']);
            $table->string('apellido_paterno')->nullable(false)->change();
            $table->string('apellido_materno')->nullable(false)->change();
            $table->string('numero')->nullable(false)->change();
        });

        Schema::rename('usuarios', 'huespedes');
    }
};
