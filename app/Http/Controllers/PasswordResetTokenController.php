<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use Illuminate\Http\Request;

class PasswordResetTokenController extends Controller
{
    // Récupérer tous les tokens de réinitialisation
    public function index()
    {
        $tokens = PasswordResetToken::all();
        return $this->respondWithSuccess($tokens);
    }

    // Créer un token de réinitialisation
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        $token = PasswordResetToken::create($validated);

        return $this->respondWithSuccess($token, 'Token créé avec succès', 201);
    }

    // Supprimer un token de réinitialisation
    public function destroy($email)
    {
        $token = PasswordResetToken::where('email', $email)->first();

        if (!$token) {
            return $this->respondWithError('Token non trouvé', 404);
        }

        $token->delete();

        return $this->respondWithSuccess(null, 'Token supprimé avec succès');
    }
}
