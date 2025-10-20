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
        // Crear usuarios básicos primero
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

        // Crear usuarios adicionales para tener más datos de prueba
        User::factory()->create([
            'name' => 'Profesor de Matemáticas',
            'email' => 'matematicas@colegioagnes.edu.mx',
            'password' => bcrypt('22Agk#04'),
            'rol' => 'profesor',
        ]);

        User::factory()->create([
            'name' => 'Profesor de Ciencias',
            'email' => 'ciencias@colegioagnes.edu.mx',
            'password' => bcrypt('22Agk#04'),
            'rol' => 'profesor',
        ]);

        User::factory()->create([
            'name' => 'Coordinador Primaria',
            'email' => 'coordinador.primaria@colegioagnes.edu.mx',
            'password' => bcrypt('22Agk#04'),
            'rol' => 'Coordinador Primaria',
        ]);

        User::factory()->create([
            'name' => 'Coordinador Secundaria',
            'email' => 'coordinador.secundaria@colegioagnes.edu.mx',
            'password' => bcrypt('22Agk#04'),
            'rol' => 'Coordinador Secundaria',
        ]);

        // Ejecutar seeders en orden de dependencias
        $this->call([
            MateriaSeeder::class,      // Primero materias (sin dependencias)
            GrupoSeeder::class,        // Luego grupos (depende de usuarios)
            TareaSeeder::class,        // Tareas (depende de grupos y materias)
            HorarioSeeder::class,      // Horarios (depende de grupos, materias y usuarios)
            CursosSeeder::class,       // Cursos (depende de usuarios)
        ]);
    }
}
