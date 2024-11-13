<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;
use App\Models\Realisation;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        
        $users = User::factory(10)->create();

        foreach ($users as $user) {
            $tasks = Task::factory(5)->create(['user_id' => $user->id]);

            foreach ($tasks as $task) {
                Realisation::factory(3)->create([
                    'task_id' => $task->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
