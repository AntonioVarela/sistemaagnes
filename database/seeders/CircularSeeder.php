<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Circular;
use App\Models\grupo;
use App\Models\User;
use Carbon\Carbon;

class CircularSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos grupos y usuarios para crear circulares de prueba
        $grupos = grupo::take(3)->get();
        $usuarios = User::whereIn('rol', ['administrador', 'Coordinador Primaria', 'Coordinador Secundaria'])->take(2)->get();

        if ($grupos->isEmpty() || $usuarios->isEmpty()) {
            $this->command->info('No hay grupos o usuarios disponibles para crear circulares de prueba.');
            return;
        }

        $circulares = [
            [
                'titulo' => 'Circular Semanal del 1-5 de Septiembre',
                'descripcion' => 'Información importante sobre las actividades de la semana, horarios especiales y recordatorios para padres y alumnos.',
                'archivo' => 'circulares/circular_semana_1.pdf',
                'nombre_archivo_original' => 'Circular_Semana_1_Septiembre.pdf',
                'tipo_archivo' => 'application/pdf',
                'fecha_expiracion' => Carbon::now()->addDays(7),
            ],
            [
                'titulo' => 'Circular Informativa - Actividades Extraescolares',
                'descripcion' => 'Detalles sobre las actividades extraescolares disponibles para este semestre y fechas de inscripción.',
                'archivo' => 'circulares/actividades_extraescolares.pdf',
                'nombre_archivo_original' => 'Actividades_Extraescolares_2024.pdf',
                'tipo_archivo' => 'application/pdf',
                'fecha_expiracion' => Carbon::now()->addDays(14),
            ],
            [
                'titulo' => 'Circular de Seguridad Escolar',
                'descripcion' => 'Protocolos de seguridad actualizados y medidas preventivas para la comunidad escolar.',
                'archivo' => 'circulares/seguridad_escolar.pdf',
                'nombre_archivo_original' => 'Protocolos_Seguridad_Escolar.pdf',
                'tipo_archivo' => 'application/pdf',
                'fecha_expiracion' => null, // Sin expiración
            ],
        ];

        foreach ($circulares as $index => $circularData) {
            $grupo = $grupos[$index % $grupos->count()];
            $usuario = $usuarios[$index % $usuarios->count()];

            Circular::create([
                'titulo' => $circularData['titulo'],
                'descripcion' => $circularData['descripcion'],
                'archivo' => $circularData['archivo'],
                'nombre_archivo_original' => $circularData['nombre_archivo_original'],
                'tipo_archivo' => $circularData['tipo_archivo'],
                'usuario_id' => $usuario->id,
                'grupo_id' => $grupo->id,
                'seccion' => $grupo->seccion,
                'fecha_expiracion' => $circularData['fecha_expiracion'],
                'created_at' => Carbon::now()->subDays(rand(1, 10)),
                'updated_at' => Carbon::now()->subDays(rand(1, 10)),
            ]);
        }

        $this->command->info('Circulares de prueba creadas exitosamente.');
    }
}
