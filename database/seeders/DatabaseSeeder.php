<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Administrador',
                'password' => '12345678',
                'role' => 'administrador',
            ]
        );

        $recepcionista = User::query()->firstOrCreate(
            ['email' => 'recepcion@hotel.com'],
            [
                'name' => 'Recepcionista',
                'password' => '12345678',
                'role' => 'recepcionista',
            ]
        );

        foreach ([$admin, $recepcionista] as $trabajador) {
            DB::table('usuarios')->updateOrInsert(
                ['user_id' => $trabajador->id],
                [
                    'nombre' => $trabajador->name,
                    'apellido_paterno' => null,
                    'apellido_materno' => null,
                    'rfc' => null,
                    'nss' => null,
                    'numero' => null,
                    'email' => $trabajador->email,
                    'tipo_usuario' => 'trabajador',
                    'role' => $trabajador->role,
                    'updated_at' => now(),
                    'created_at' => $trabajador->created_at ?? now(),
                ]
            );
        }

        $serviciosDemo = [
            ['nombre' => 'Servicio al cuarto', 'descripcion' => 'Alimentos solicitados para la habitación.', 'categoria' => 'cocina'],
            ['nombre' => 'Platillo de cocina', 'descripcion' => 'Preparación de alimentos para el huésped.', 'categoria' => 'cocina'],
            ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas solicitadas para la habitación.', 'categoria' => 'cocina'],
            ['nombre' => 'Lavandería', 'descripcion' => 'Recolección y entrega de prendas.', 'categoria' => 'limpieza'],
            ['nombre' => 'Limpieza y toallas', 'descripcion' => 'Limpieza adicional o artículos para la habitación.', 'categoria' => 'limpieza'],
            ['nombre' => 'Reparación de habitación', 'descripcion' => 'Reporte de mantenimiento en la habitación.', 'categoria' => 'mantenimiento'],
            ['nombre' => 'Alberca', 'descripcion' => 'Acceso o asistencia para el área de alberca.', 'categoria' => 'servicio_cuarto'],
            ['nombre' => 'Cancha de tenis', 'descripcion' => 'Solicitud de uso de la cancha de tenis.', 'categoria' => 'servicio_cuarto'],
        ];

        foreach ($serviciosDemo as $servicio) {
            DB::table('tipos_servicios')->updateOrInsert(
                ['nombre' => $servicio['nombre']],
                [
                    'descripcion' => $servicio['descripcion'],
                    'categoria' => $servicio['categoria'],
                    'activo' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $trabajadoresDemo = [
            [
                'nombre' => 'Celia',
                'apellido_paterno' => 'Cocina',
                'apellido_materno' => 'Demo',
                'email' => 'cocina@hotel.test',
                'role' => 'cocina',
                'rfc' => 'COCI900101ABC',
                'nss' => '10101010101',
                'numero' => '5550000001',
            ],
            [
                'nombre' => 'Luz',
                'apellido_paterno' => 'Limpieza',
                'apellido_materno' => 'Demo',
                'email' => 'limpieza@hotel.test',
                'role' => 'limpieza',
                'rfc' => 'LIMP900101ABC',
                'nss' => '20202020202',
                'numero' => '5550000002',
            ],
            [
                'nombre' => 'Mario',
                'apellido_paterno' => 'Mantenimiento',
                'apellido_materno' => 'Demo',
                'email' => 'mantenimiento@hotel.test',
                'role' => 'mantenimiento',
                'rfc' => 'MANT900101ABC',
                'nss' => '30303030303',
                'numero' => '5550000003',
            ],
            [
                'nombre' => 'Sofia',
                'apellido_paterno' => 'Servicio',
                'apellido_materno' => 'Demo',
                'email' => 'cuarto@hotel.test',
                'role' => 'servicio_cuarto',
                'rfc' => 'SERV900101ABC',
                'nss' => '40404040404',
                'numero' => '5550000004',
            ],
        ];

        foreach ($trabajadoresDemo as $datos) {
            $cuenta = User::query()->updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => trim($datos['nombre'].' '.$datos['apellido_paterno'].' '.$datos['apellido_materno']),
                    'password' => 'HotelDemo123!',
                    'role' => $datos['role'],
                ]
            );

            DB::table('usuarios')->updateOrInsert(
                ['user_id' => $cuenta->id],
                [
                    'nombre' => $datos['nombre'],
                    'apellido_paterno' => $datos['apellido_paterno'],
                    'apellido_materno' => $datos['apellido_materno'],
                    'rfc' => $datos['rfc'],
                    'nss' => $datos['nss'],
                    'numero' => $datos['numero'],
                    'email' => $datos['email'],
                    'tipo_usuario' => 'trabajador',
                    'role' => $datos['role'],
                    'updated_at' => now(),
                    'created_at' => $cuenta->created_at ?? now(),
                ]
            );
        }
    }
}
