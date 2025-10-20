<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\grupo>
 */
class GrupoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->randomElement([
                'Primer Grado A', 'Primer Grado B',
                'Segundo Grado A', 'Segundo Grado B',
                'Tercer Grado A', 'Tercer Grado B',
                'Cuarto Grado A', 'Cuarto Grado B',
                'Quinto Grado A', 'Quinto Grado B',
                'Sexto Grado A', 'Sexto Grado B',
                'Primero de Secundaria A', 'Primero de Secundaria B',
                'Segundo de Secundaria A', 'Segundo de Secundaria B',
                'Tercero de Secundaria A', 'Tercero de Secundaria B'
            ]),
            'seccion' => $this->faker->randomElement(['Primaria', 'Secundaria']),
            'titular' => User::factory(),
        ];
    }
}