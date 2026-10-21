<?php

namespace Database\Factories;

use App\Models\TicketNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketNote>
 */
class TicketNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->paragraph(),
            // ticket_id y user_id se pasarán desde el seeder
        ];
    }
}
