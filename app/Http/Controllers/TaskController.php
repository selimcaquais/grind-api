<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Realisation;

class TaskController extends Controller
{
    // Récupérer toutes les tâches
    public function index()
    {
        // Get auth user
        $user = auth()->user();

        // Get task of the users
        $tasks = Task::where('user_id', $user->id)->get();

        return $this->respondWithSuccess($tasks);
    }

    // Récupérer une tâche spécifique
    public function show($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return $this->respondWithError('Tâche non trouvée', 404);
        }

        return $this->respondWithSuccess($task);
    }

    // Create new task
    public function store(Request $request)
    {  
        try {
            // set streak to 0 and user_id to the user auth
            $user = auth()->user();
            $request['streak'] = 0;
            $request['user_id'] = $user->id;

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'iteration_max' => 'required|integer|min:1',
                'streak' => 'required|integer|min:0',
                'days' => 'required|array', // Is Array ?
                'days.*' => 'integer|min:0|max:6', // Validation of the days
                'user_id' => 'required|exists:users,id',
            ]);

            $task = Task::create($validated);

            $currentDate = Carbon::now($user->timezone);
            $dayOfWeek = $currentDate->dayOfWeek();

            if (in_array($dayOfWeek, $task->days)){
                $realisation = Realisation::create([
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'date' => $currentDate->toDateString(),
                    'iteration' => 0,
                    'iteration_max' => $task->iteration_max,
                    'streak'=> 0,
                ]);
            }

            return $this->respondWithSuccess($task, 'Tâche créée avec succès', 201);

        } catch (ValidationException $e) {
            // Validation error 422
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Error management
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update an existing task
    public function update(Request $request, $id)
    {   
        try {
            // get the task
            $task = Task::find($id);
            $user = auth()->user();

            // check if user don't try to modify other users tasks
            if ($user->id == $task->user_id) {
                if (!$task) {
                    return $this->respondWithError('Tâche non trouvée', 404);
                }
    
                $validated = $request->validate([
                    'name' => 'nullable|string|max:255',
                    'iteration_max' => 'nullable|integer|min:1',
                    'streak' => 'nullable|integer|min:0',
                    'days' => 'nullable|array',
                    'days.*' => 'integer|min:0|max:6',
                ]);
    
                $user = $request->user();
                $date = Carbon::now($user->timezone);
    
                $today = $date->toDateString(); // get time and day by the timzone of the user
                $dayOfWeek = $date->dayOfWeek(); // get the actual day of the week

                // get iteration and streak of today realisation
                $realisation = Realisation::where('user_id', $user->id)
                                            ->whereDate('date', $today)
                                            ->where('task_id', $task->id)
                                            ->first();
                

                //Check if today realisation exist
                if ($realisation) {
                    // modify if iteration of realisation is bigger than actual iteration_max
                    if ($validated['iteration_max'] < $realisation->iteration) {
                        $realisation->iteration = $validated['iteration_max'];
                    }

                    // If today date is not longer in task day we delete the realisation
                    if (!in_array($dayOfWeek, $validated['days'])) {
                        $realisation->delete();
                    } else {
                        // if the date is alaws good we modify the task
                        $realisation->iteration_max = $validated['iteration_max'];
                        $realisation->save(); 
                    }
                } else {
                    if (in_array($dayOfWeek, $validated['days'])) {
                        Realisation::create([
                            'user_id' => $user->id,
                            'task_id' => $task->id,
                            'date' => $today,
                            'iteration' => 0, 
                            'iteration_max' => $validated['iteration_max'],
                            'streak' => $task->streak, 
                        ]);
                    }
                }
                $task->update($validated);
                return $this->respondWithSuccess($task, 'Tâche mise à jour avec succès');
            } else {
                return $this->respondWithError("Tryna access to a task that not yours",401);
            }
        }  catch (ValidationException $e) {
            // Validation error 422
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Error management
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Supprimer une tâche
    public function destroy($id)
    {
        try{
            $task = Task::find($id);
            $user = auth()->user();
            // check if user don't try to delete other users tasks
            if ($user->id == $task->user_id) {
                if (!$task) {
                    return $this->respondWithError('Tâche non trouvée', 404);
                }
        
                $task->delete();
        
                return $this->respondWithSuccess(null, 'Tâche supprimée avec succès');
            }
    
        } catch (\Exception $e) {
            // Error management
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    } 
}
