<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Curso;
use App\Models\User;

class CursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer usuario administrador
        $admin = User::where('rol', 'administrador')->first();
        
        if (!$admin) {
            $admin = User::first(); // Fallback al primer usuario
        }

        $cursos = [
            [
                'titulo' => 'Curso de Introducción',
                'descripcion' => 'Fundamentos básicos para nuevos estudiantes',
                'categoria' => 'general',
                'nivel' => 'básico',
                'activo' => true,
                'orden' => 1,
                'contenido_detallado' => 'Este curso proporciona una introducción completa a los conceptos fundamentales del sistema educativo.'
            ],
            [
                'titulo' => 'Matemáticas Avanzadas',
                'descripcion' => 'Técnicas avanzadas para estudiantes experimentados',
                'categoria' => 'matematicas',
                'nivel' => 'avanzado',
                'activo' => true,
                'orden' => 2,
                'contenido_detallado' => 'Curso especializado en matemáticas de nivel superior con enfoque en resolución de problemas complejos.'
            ],
            [
                'titulo' => 'Ciencias Experimentales',
                'descripcion' => 'Enfoque en áreas específicas de estudio científico',
                'categoria' => 'ciencias',
                'nivel' => 'intermedio',
                'activo' => true,
                'orden' => 3,
                'contenido_detallado' => 'Exploración práctica de conceptos científicos a través de experimentos y observaciones.'
            ],
            [
                'titulo' => 'Arte y Creatividad',
                'descripcion' => 'Desarrollo de habilidades artísticas y creativas',
                'categoria' => 'arte',
                'nivel' => 'básico',
                'activo' => true,
                'orden' => 4,
                'contenido_detallado' => 'Fomento de la expresión artística y el pensamiento creativo en diferentes medios.'
            ],
            [
                'titulo' => 'Tecnología Digital',
                'descripcion' => 'Herramientas digitales para el aprendizaje moderno',
                'categoria' => 'tecnologia',
                'nivel' => 'intermedio',
                'activo' => true,
                'orden' => 5,
                'contenido_detallado' => 'Integración de tecnologías digitales en el proceso educativo y de aprendizaje.'
            ]
        ];

        foreach ($cursos as $cursoData) {
            Curso::create(array_merge($cursoData, ['user_id' => $admin->id]));
        }
    }
}
