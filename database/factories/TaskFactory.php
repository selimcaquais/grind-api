<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        $days = $this->faker->randomElements([0, 1, 2, 3, 4, 5, 6], $this->faker->numberBetween(1, 7));
        sort($days);
        return [
            'name' => $this->faker->word(),
            'iteration_max' => $this->faker->numberBetween(1, 10), 
            'streak' => $this->faker->numberBetween(0, 100),
            'days' => $days,
            'user_id' => User::factory(),
        ];
    }
}