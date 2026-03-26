<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        return [
            'project_name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(10),
            'user_id' => User::factory(), // si nécessaire pour tests
        ];
    }


        public function configure()
        {
            return $this->afterCreating(function (Project $project) {
                Task::factory(5)->create([
                    'project_id' => $project->id,
                    'status_id' => 1, // doit exister
                ]);
            });
        }
}
