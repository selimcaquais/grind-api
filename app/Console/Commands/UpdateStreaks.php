<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Realisation;
use App\Models\Task;

class UpdateStreaks extends Command
{
    /**
     * The name and the signature of the command.
     *
     * @var string
     */
    protected $signature = 'streaks:update';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Updates user streaks, tasks streaks and create realisation for tomorrow at midnight for each time zone.';

    /**
     * 
     */
    public function handle()
    {
        // Get all timezone
        $timezones = \DateTimeZone::listIdentifiers();
        $nowUtc = Carbon::now('UTC');
        $currentTimeInTimezone = $nowUtc->copy()->setTimezone("Europe/Paris");
        foreach ($timezones as $timezone) {
            $currentTimeInTimezone = $nowUtc->copy()->setTimezone($timezone);
            
            if ($currentTimeInTimezone->format('H:i') === '00:00') {
                $this->processUsersForTimezone($timezone);
            }
        }
    }

    protected function processUsersForTimezone($timezone)
    {
        $users = User::where('timezone', $timezone)->get();

        $yesterday = Carbon::now($timezone)->subHours(12)->format('Y-m-d');

        // initiate these variables to calculate the user's total "streak"
        $allRealisationIteration = 0;
        $allRealisationIterationMax = 0;

        foreach ($users as $user) {

            // ---- UPDATE USER "STREAK" && TASK "STREAK" ----

            // get all realisation of yesterday for each users
            $realisations = Realisation::where('date', $yesterday)->where('user_id', $user->id)->get();

            foreach($realisations as $realisation) {
                
                // add iteration for the final calcul
                $allRealisationIteration += $realisation->iteration;
                $allRealisationIterationMax += $realisation->iteration_max;

                // if task done add +1 to the task "streak"
                if (($realisation->iteration / $realisation->iteration_max) === 1) {

                    // update task "streak"
                    $task = Task::where('id', $realisation->task_id)->get()->first();
                    $task->streak += 1;
                    $task->save();
                }
            }
            if (($allRealisationIteration / $allRealisationIterationMax) === 1) { 
                $user->user_streak += 1;
                $user->save();
            }

            $tasks = Task::where('user_id', $user->id)->get();

            // ---- CREATE REALISATION FOR THE DAY ----

            $currentDate = Carbon::now($user->timezone);
            $dayOfWeek = $currentDate->dayOfWeek();

            foreach($tasks as $task) {
                if (in_array($dayOfWeek, $task->days)){
                    $realisation = Realisation::create([
                        'user_id' => $user->id,
                        'task_id' => $task->id,
                        'date' => $currentDate->toDateString(),
                        'iteration' => 0,
                        'iteration_max' => $task->iteration_max,
                        'streak'=> $task->streak,
                    ]);
                }
            }
        }
    }
}
