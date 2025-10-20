<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\materia>
 */
class MateriaFactory extends Factory
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
                'Matemáticas', 'Español', 'Ciencias Naturales',
                'Historia', 'Geografía', 'Educación Física',
                'Artes', 'Música', 'Inglés', 'Formación Cívica y Ética',
                'Álgebra', 'Geometría', 'Biología', 'Química', 'Física',
                'Literatura', 'Redacción', 'Civismo', 'Tecnología', 'Informática'
            ]),
        ];
    }
}