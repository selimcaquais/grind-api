<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Get all users
    public function index()
    {
        $user = auth()->user();

        // modify with (if admin) when it's created;
        if ($user->id == 11){
            $users = User::all();
            return $this->respondWithSuccess($users);
        } else {
            return $this->respondWithError('Unauthorized',401);
        }
    }

    // Get one specific user
    public function show($id)
    {
        $user = User::find($id);

        // Check if the user can have access to this ressource
        if (auth()->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        } else {
            return $this->respondWithSuccess($user);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:320|unique:users',
            'password' => 'required|string|min:8|max:60|confirmed',
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($request->password),
        ]);

        return $this->respondWithSuccess($user, 'Utilisateur créé avec succès', 201);
    }

    // Udpate user
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->respondWithError('Utilisateur non trouvé', 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        $user->update($validated);

        return $this->respondWithSuccess($user, 'Utilisateur mis à jour avec succès');
    }

    // Supprimer un utilisateur
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->respondWithError('Utilisateur non trouvé', 404);
        }

        $user->delete();

        return $this->respondWithSuccess(null, 'Utilisateur supprimé avec succès');
    }
}