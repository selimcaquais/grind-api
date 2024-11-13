<?php

namespace App\Http\Controllers;

use App\Models\Realisation;
use App\Models\Task;
use Illuminate\Http\Request;

class RealisationController extends Controller
{
    // Créer une nouvelle réalisation pour une tâche spécifique
    public function store(Request $request, $taskId)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'iteration' => 'required|integer',
            'iteration_max' => 'required|integer',
            'streak' => 'required|integer',
        ]);

        $task = Task::findOrFail($taskId);

        // Créer la réalisation
        $realisation = Realisation::create([
            'user_id' => $validated['user_id'],
            'task_id' => $task->id,
            'date' => $validated['date'],
            'iteration' => $validated['iteration'],
            'iteration_max' => $validated['iterationMax'],
            'streak' => $validated['streak'],
        ]);

        return $this->respondWithSuccess($realisation, 'Réalisation créée avec succès');
    }

    // Afficher toutes les réalisations d'une tâche spécifique
    public function index($taskId)
    {
        $task = Task::findOrFail($taskId);

        // Récupérer toutes les réalisations de la tâche
        $realisations = $task->realisations;

        return $this->respondWithSuccess($realisations, 'Liste des réalisations');
    }

    // Afficher une réalisation spécifique
    public function show($realisationId)
    {
        $realisation = Realisation::findOrFail($realisationId);

        return $this->respondWithSuccess($realisation, 'Réalisation récupérée');
    }

    // Supprimer une réalisation
    public function destroy($realisationId)
    {
        $realisation = Realisation::findOrFail($realisationId);
        $realisation->delete();

        return $this->respondWithSuccess([], 'Réalisation supprimée');
    }
}