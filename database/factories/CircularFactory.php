<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\grupo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Circular>
 */
class CircularFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence(4),
            'descripcion' => $this->faker->paragraph(3),
            'archivo' => 'circulares/' . $this->faker->uuid() . '.pdf',
            'nombre_archivo_original' => $this->faker->word() . '.pdf',
            'tipo_archivo' => 'application/pdf',
            'usuario_id' => User::factory(),
            'grupo_id' => grupo::factory(),
            'seccion' => $this->faker->randomElement(['Primaria', 'Secundaria']),
            'fecha_expiracion' => $this->faker->optional(0.7)->dateTimeBetween('now', '+30 days'),
            'es_global' => $this->faker->boolean(20), // 20% chance of being global
        ];
    }
}