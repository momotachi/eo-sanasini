<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 week', '+1 month');
        return [
            'organization_id' => \App\Models\Organization::factory(),
            'name' => $name = $this->faker->unique()->words(3, true) . ' Championship',
            'slug' => str()->slug($name),
            'type' => 'CHAMPIONSHIP',
            'category' => 'SPORT',
            'status' => 'UPCOMING',
            'description' => $this->faker->paragraph(),
            'start_date' => $start,
            'end_date' => (clone $start)->modify('+2 days'),
            'venue' => $this->faker->optional()->company(),
            'is_public' => true,
        ];
    }
}
