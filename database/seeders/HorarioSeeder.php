<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\horario;
use App\Models\grupo;
use App\Models\materia;
use App\Models\User;

class HorarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener grupos, materias y maestros
        $grupos = grupo::take(6)->get();
        $materias = materia::take(10)->get();
        $maestros = User::where('rol', 'profesor')->take(8)->get();

        if ($grupos->isEmpty() || $materias->isEmpty() || $maestros->isEmpty()) {
            $this->command->info('No hay grupos, materias o maestros disponibles para crear horarios de prueba.');
            return;
        }

        $horarios = [
            // Horarios de la mañana
            [
                'dias' => 'Lunes, Miércoles, Viernes',
                'hora_inicio' => '08:00',
                'hora_fin' => '09:00',
            ],
            [
                'dias' => 'Lunes, Miércoles, Viernes',
                'hora_inicio' => '09:00',
                'hora_fin' => '10:00',
            ],
            [
                'dias' => 'Lunes, Miércoles, Viernes',
                'hora_inicio' => '10:00',
                'hora_fin' => '11:00',
            ],
            [
                'dias' => 'Martes, Jueves',
                'hora_inicio' => '08:30',
                'hora_fin' => '10:00',
            ],
            [
                'dias' => 'Martes, Jueves',
                'hora_inicio' => '10:00',
                'hora_fin' => '11:30',
            ],
            [
                'dias' => 'Lunes a Viernes',
                'hora_inicio' => '11:30',
                'hora_fin' => '12:30',
            ],
            // Horarios de la tarde
            [
                'dias' => 'Lunes, Miércoles, Viernes',
                'hora_inicio' => '14:00',
                'hora_fin' => '15:00',
            ],
            [
                'dias' => 'Lunes, Miércoles, Viernes',
                'hora_inicio' => '15:00',
                'hora_fin' => '16:00',
            ],
            [
                'dias' => 'Martes, Jueves',
                'hora_inicio' => '14:30',
                'hora_fin' => '16:00',
            ],
            [
                'dias' => 'Lunes a Viernes',
                'hora_inicio' => '16:00',
                'hora_fin' => '17:00',
            ],
        ];

        foreach ($horarios as $index => $horarioData) {
            $grupo = $grupos[$index % $grupos->count()];
            $materia = $materias[$index % $materias->count()];
            $maestro = $maestros[$index % $maestros->count()];

            horario::create([
                'grupo_id' => $grupo->id,
                'materia_id' => $materia->id,
                'maestro_id' => $maestro->id,
                'dias' => $horarioData['dias'],
                'hora_inicio' => $horarioData['hora_inicio'],
                'hora_fin' => $horarioData['hora_fin'],
            ]);
        }

        $this->command->info('Horarios de ejemplo creados exitosamente.');
    }
}
