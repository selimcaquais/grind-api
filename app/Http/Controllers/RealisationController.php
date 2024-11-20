<?php

namespace App\Http\Controllers;

use App\Models\Realisation;
use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RealisationController extends Controller
{
    // show realisation by date 
    public function showByDate (Request $request) {
        try {
            $user = auth()->user();
            $period = $request->period;
            $now = Carbon::now($user->timezone);
            
            // Calculate the starting date
            switch ($period) {
                case 'week':
                    $startDate = $now->copy()->subWeek();
                    break;
                case 'month':
                    $startDate = $now->copy()->subMonth();
                    break;
                case '3months':
                    $startDate = $now->copy()->subMonths(3);
                    break;
                case '6months':
                    $startDate = $now->copy()->subMonths(6);
                    break;
                case 'year':
                    $startDate = $now->copy()->subYear();
                    break;
                default:
                    return response()->json([
                        'error' => 'Période invalide. Veuillez spécifier une période valide : week, month, 3months, 6months, year.'
                    ], 400);
            }

            // Get realisation of the auth user by date
            $realisations = Realisation::where('user_id', $user->id)
                                        ->whereBetween('date', [$startDate->toDateString(), $now->toDateString()])
                                        ->orderBy('date', 'desc')
                                        ->get()
                                        ->map(function ($realisation) {
                                            $carbonDate = Carbon::parse($realisation->date); 
                                             return [
                                                'date' => $realisation->date,
                                                'dayOfTheWeek' => $carbonDate->dayOfWeek,
                                                'realisation_streak' => $realisation->streak,
                                            ];
                                        });
        
            return $this->respondWithSuccess($realisations, 'Realisation récupérer avec succès');

        } catch (\Exception $e) {
            // Error management
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

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