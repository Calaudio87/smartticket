<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['Facturación', 'Soporte Técnico', 'Seguridad / Abuso', 'Otros']),
            'priority' => fake()->randomElement(['Baja', 'Media', 'Alta', 'Crítica']),
            'status' => fake()->randomElement(['open', 'in_progress', 'resolved']),
            'summary' => fake()->sentence(6),
        ];
    }
}
