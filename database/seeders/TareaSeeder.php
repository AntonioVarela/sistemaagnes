<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\tarea;
use App\Models\grupo;
use App\Models\materia;
use Carbon\Carbon;

class TareaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos grupos y materias para crear tareas
        $grupos = grupo::take(5)->get();
        $materias = materia::take(8)->get();

        if ($grupos->isEmpty() || $materias->isEmpty()) {
            $this->command->info('No hay grupos o materias disponibles para crear tareas de prueba.');
            return;
        }

        $tareas = [
            [
                'titulo' => 'Ejercicios de Matemáticas - Suma y Resta',
                'descripcion' => 'Resolver los ejercicios de las páginas 45-50 del libro de texto. Incluir procedimiento completo.',
                'fecha_entrega' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'hora_entrega' => '14:00:00',
            ],
            [
                'titulo' => 'Lectura Comprensiva - Cuento "El Principito"',
                'descripcion' => 'Leer los capítulos 1-3 del libro "El Principito" y responder las preguntas de comprensión lectora.',
                'fecha_entrega' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'hora_entrega' => '10:30:00',
            ],
            [
                'titulo' => 'Experimento de Ciencias - Estados de la Materia',
                'descripcion' => 'Realizar un experimento casero para demostrar los diferentes estados de la materia. Documentar con fotos.',
                'fecha_entrega' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'hora_entrega' => '16:00:00',
            ],
            [
                'titulo' => 'Investigación Histórica - Independencia de México',
                'descripcion' => 'Investigar sobre los principales personajes de la Independencia de México y crear una línea de tiempo.',
                'fecha_entrega' => Carbon::now()->addDays(4)->format('Y-m-d'),
                'hora_entrega' => '12:00:00',
            ],
            [
                'titulo' => 'Proyecto de Arte - Pintura Abstracta',
                'descripcion' => 'Crear una pintura abstracta usando técnicas de acuarela. Incluir una explicación del significado de la obra.',
                'fecha_entrega' => Carbon::now()->addDays(6)->format('Y-m-d'),
                'hora_entrega' => '15:30:00',
            ],
            [
                'titulo' => 'Ejercicios de Inglés - Verbos Irregulares',
                'descripcion' => 'Completar la tabla de verbos irregulares y crear 10 oraciones usando cada verbo.',
                'fecha_entrega' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'hora_entrega' => '11:00:00',
            ],
            [
                'titulo' => 'Mapa de México - Estados y Capitales',
                'descripcion' => 'Dibujar un mapa de México identificando todos los estados y sus capitales. Colorear por regiones.',
                'fecha_entrega' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'hora_entrega' => '13:30:00',
            ],
            [
                'titulo' => 'Composición Musical - Melodía Simple',
                'descripcion' => 'Crear una melodía simple de 8 compases usando flauta o piano. Grabar el resultado.',
                'fecha_entrega' => Carbon::now()->addDays(8)->format('Y-m-d'),
                'hora_entrega' => '14:45:00',
            ],
        ];

        foreach ($tareas as $index => $tareaData) {
            $grupo = $grupos[$index % $grupos->count()];
            $materia = $materias[$index % $materias->count()];

            tarea::create([
                'titulo' => $tareaData['titulo'],
                'descripcion' => $tareaData['descripcion'],
                'fecha_entrega' => $tareaData['fecha_entrega'],
                'hora_entrega' => $tareaData['hora_entrega'],
                'archivo' => null, // Sin archivo adjunto por defecto
                'grupo' => $grupo->id,
                'materia' => $materia->id,
            ]);
        }

        $this->command->info('Tareas de ejemplo creadas exitosamente.');
    }
}
