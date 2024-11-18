<?php

namespace Database\Factories;

use App\Models\Realisation;
use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class RealisationFactory extends Factory
{
    protected $model = Realisation::class;
    
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'task_id' => Task::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'iteration' => $this->faker->numberBetween(1, 10),
            'iteration_max' => $this->faker->numberBetween(1, 10),
            'streak' => $this->faker->numberBetween(0, 100),
        ];
    }
}