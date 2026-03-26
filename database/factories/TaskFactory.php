<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_name' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'position' => fake()->numberBetween(1, 10),
            'project_id' => Project::factory(),
            'status_id' => fake()->numberBetween(1, 3),
            ];
    }
}
