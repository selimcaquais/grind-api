<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Realisation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::now($user->timezone)->toDateString(); // get time and day by the timzone of the user
        
        $realisations = Realisation::where('user_id', $user->id)
                                    ->whereDate('date', $today)
                                    ->with('task')
                                    ->get()
                                    ->map(function ($realisation) {
                                        return [
                                            'task_id' => $realisation->task->id,
                                            'realisation_id' => $realisation->id,
                                            'task_name' => $realisation->task->name,
                                            'iteration' => $realisation->iteration,
                                            'iteration_max' => $realisation->task->iteration_max,
                                            'task_streak' => $realisation->task->streak,
                                        ];
                                    });

        return response()->json([
            'status' => 'success',
            'data' => $realisations,
        ]);
    }
}