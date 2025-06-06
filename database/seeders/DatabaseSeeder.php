<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'antonio.varela@colegioagnes.edu.mx',
            'password' => bcrypt('22Agk#04'),
            'rol' => 'administrador',
        ]);

        User::factory()->create([
            'name' => 'Profesor',
            'email' => 'profesor@colegioagnes.edu.mx',
            'password' => bcrypt('22Agk#04'),
            'rol' => 'profesor',
        ]);
        
        
    }
}
