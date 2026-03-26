<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Status>
 */
class StatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Status::class;

    public function definition()
    {
        return [
            'status_name' => $this->faker->randomElement(['À faire', 'En cours', 'Terminé']),
            'color' => $this->faker->hexColor(),
            'user_id' => 1,
        ];
    }
}
