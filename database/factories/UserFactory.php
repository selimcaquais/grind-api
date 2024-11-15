<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        // Array of timezone
        $timezones = [
            'America/New_York',
            'Europe/London',
            'Asia/Tokyo',
            'Australia/Sydney',
            'Europe/Paris',
            'America/Los_Angeles'
        ];

        return [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'registration_date' => $this->faker->date(),
            'user_streak' => $this->faker->numberBetween(0, 100),
            'timezone' => $timezones[array_rand($timezones)],  //Random timezone
        ];
    }
}
