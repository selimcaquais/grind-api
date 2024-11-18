<?php

namespace App\Http\Controllers;

use App\Models\Realisation;
use App\Models\Task;
use Illuminate\Http\Request;

class RealisationController extends Controller
{
    // update a realisation
    public function update(Request $request, $id){
        try {
            $user = auth()->user();
            $realisation = Realisation::find($id);
            // check if the user trying to modify the realisation own them
            if ($user->id == $realisation->user_id){

                $validated = $request->validate([
                    'iteration' => 'required|integer|min:0'
                ]);

                // if the iteration given is above the max, we prevent it by giving it the max value
                if ($validated['iteration'] > $realisation->iteration_max){
                    $validated['iteration'] = $realisation->iteration_max;
                }

                $realisation->update($validated);
                return $this->respondWithSuccess($realisation, 'Realisation mise à jour avec succès');
            }

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
}