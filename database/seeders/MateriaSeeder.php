<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\materia;

class MateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materias = [
            // Materias básicas
            ['nombre' => 'Matemáticas'],
            ['nombre' => 'Español'],
            ['nombre' => 'Ciencias Naturales'],
            ['nombre' => 'Historia'],
            ['nombre' => 'Geografía'],
            ['nombre' => 'Educación Física'],
            ['nombre' => 'Artes'],
            ['nombre' => 'Música'],
            ['nombre' => 'Inglés'],
            ['nombre' => 'Formación Cívica y Ética'],
            
            // Materias de secundaria
            ['nombre' => 'Álgebra'],
            ['nombre' => 'Geometría'],
            ['nombre' => 'Biología'],
            ['nombre' => 'Química'],
            ['nombre' => 'Física'],
            ['nombre' => 'Literatura'],
            ['nombre' => 'Redacción'],
            ['nombre' => 'Civismo'],
            ['nombre' => 'Tecnología'],
            ['nombre' => 'Informática'],
            
            // Materias especializadas
            ['nombre' => 'Francés'],
            ['nombre' => 'Dibujo Técnico'],
            ['nombre' => 'Filosofía'],
            ['nombre' => 'Psicología'],
            ['nombre' => 'Economía'],
            ['nombre' => 'Derecho'],
            ['nombre' => 'Medicina'],
            ['nombre' => 'Ingeniería'],
            ['nombre' => 'Arquitectura'],
            ['nombre' => 'Diseño Gráfico'],
        ];

        foreach ($materias as $materiaData) {
            materia::create($materiaData);
        }

        $this->command->info('Materias de ejemplo creadas exitosamente.');
    }
}
