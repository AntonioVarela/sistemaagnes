<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\grupo;
use App\Models\User;

class GrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios profesores para asignar como titulares
        $profesores = User::where('rol', 'profesor')->get();
        
        if ($profesores->isEmpty()) {
            // Si no hay profesores, usar el primer usuario disponible
            $profesores = User::take(3)->get();
        }

        $grupos = [
            [
                'nombre' => 'Primer Grado A',
                'seccion' => 'A',
            ],
            [
                'nombre' => 'Primer Grado B',
                'seccion' => 'B',
            ],
            [
                'nombre' => 'Segundo Grado A',
                'seccion' => 'A',
            ],
            [
                'nombre' => 'Segundo Grado B',
                'seccion' => 'B',
            ],
            [
                'nombre' => 'Tercer Grado A',
                'seccion' => 'A',
            ],
            [
                'nombre' => 'Tercer Grado B',
                'seccion' => 'B',
            ],
            [
                'nombre' => 'Cuarto Grado A',
                'seccion' => 'A',
            ],
            [
                'nombre' => 'Cuarto Grado B',
                'seccion' => 'B',
            ],
            [
                'nombre' => 'Quinto Grado A',
                'seccion' => 'A',
            ],
            [
                'nombre' => 'Quinto Grado B',
                'seccion' => 'B',
            ],
            [
                'nombre' => 'Sexto Grado A',
                'seccion' => 'A',
            ],
            [
                'nombre' => 'Sexto Grado B',
                'seccion' => 'B',
            ],
            [
                'nombre' => 'Primero de Secundaria A',
                'seccion' => 'A',
            ],
            [
                'nombre' => 'Primero de Secundaria B',
                'seccion' => 'B',
            ],
            [
                'nombre' => 'Segundo de Secundaria A',
                'seccion' => 'A',
            ],
            [
                'nombre' => 'Segundo de Secundaria B',
                'seccion' => 'B',
            ],
            [
                'nombre' => 'Tercero de Secundaria A',
                'seccion' => 'A',
            ],
            [
                'nombre' => 'Tercero de Secundaria B',
                'seccion' => 'B',
            ],
        ];

        foreach ($grupos as $index => $grupoData) {
            $profesor = $profesores[$index % $profesores->count()];
            
            grupo::create([
                'nombre' => $grupoData['nombre'],
                'seccion' => $grupoData['seccion'],
                'titular' => $profesor->id,
            ]);
        }

        $this->command->info('Grupos de ejemplo creados exitosamente.');
    }
}
