<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@hotel.com',
            'password' => '12345678',
            'role' => 'administrador',
        ]);

        User::factory()->create([
            'name' => 'Recepcionista',
            'email' => 'recepcion@hotel.com',
            'password' => '12345678',
            'role' => 'recepcionista',
        ]);
    }
}