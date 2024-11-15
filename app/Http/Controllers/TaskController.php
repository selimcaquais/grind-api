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
                'days.*' => 'integer|min:1|max:7', // Validation of the days
                'user_id' => 'required|exists:users,id',
            ]);

            // return $currentDateTime;
            $currentDateTime = Carbon::now($user->timezone);

            $task = Task::create($validated);

            $realisation = Realisation::create([
                'user_id' => $user->id,
                'task_id' => $task->id,
                'date' => $currentDateTime,
                'iteration' => 0,
                'iteration_max' => $task->iteration_max,
                'streak'=> 0,
            ]);

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

    // Mettre à jour une tâche existante
    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return $this->respondWithError('Tâche non trouvée', 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'iteration_max' => 'nullable|integer|min:1',
            'streak' => 'nullable|integer|min:0',
            'days' => 'nullable|array',
            'days.*' => 'integer|min:1|max:7',
        ]);

        $task->update($validated);

        return $this->respondWithSuccess($task, 'Tâche mise à jour avec succès');
    }

    // Supprimer une tâche
    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return $this->respondWithError('Tâche non trouvée', 404);
        }

        $task->delete();

        return $this->respondWithSuccess(null, 'Tâche supprimée avec succès');
    }
}
