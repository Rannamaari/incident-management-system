<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'summary' => $this->faker->sentence(6),
            'affected_services' => $this->faker->words(3, true),
            'started_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'resolved_at' => $this->faker->optional()->dateTimeBetween('now', '+1 day'),
            'duration_minutes' => $this->faker->numberBetween(30, 480),
            'root_cause' => $this->faker->optional()->paragraph(),
            'delay_reason' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(['Open', 'In Progress', 'Monitoring', 'Closed']),
            'severity' => $this->faker->randomElement(['Critical', 'High', 'Medium', 'Low']),
            'outage_category' => $this->faker->randomElement(['Power', 'Core Network', 'Database']),
            'category' => $this->faker->randomElement(['FBB', 'RAN', 'ICT']),
            'fault_type' => $this->faker->randomElement(['Fiber Cut', 'Local Power', 'RRU Faulty']),
            'resolution_team' => $this->faker->randomElement(['GMR', 'Central Team', 'South Team']),
            'travel_time' => $this->faker->optional()->numberBetween(0, 180),
            'work_time' => $this->faker->optional()->numberBetween(0, 360),
        ];
    }
}
