<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\UserTokenController;
use Illuminate\Support\Facades\Hash;

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
        $user = auth()->user();
        $userToDelete = User::find($id);
        
        if (!$userToDelete) {
            return $this->respondWithError('Utilisateur non trouvé', 404);
        }
        
        if ($user->id == $userToDelete->id){
    
            $userToDelete->delete();
    
            return $this->respondWithSuccess(null, 'Utilisateur supprimé avec succès');
        } else {
            return $this->respondWithError("Don't try to delete other users", 401);
        }   
    }

    public function passwordOrEmailChange(Request $request){

       try{
            $tokenAndEmail = urldecode($request->token);

            //explode email and token
            list($resetPasswordOrEmailToken, $emailCrypted) = explode('&&', $tokenAndEmail);

            // decrypted email
            $data = base64_decode($emailCrypted);
            $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
            $ciphertext = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
            $decryptedEmail = openssl_decrypt($ciphertext, 'aes-256-cbc', env('MAIL_KEY'), 0, $iv);

            $user = User::where('email', $decryptedEmail)->first();
            
            $verifyToken = UserTokenController::verifyToken($resetPasswordOrEmailToken, $decryptedEmail);

            if ($verifyToken == 200) {
                if($request->type == "password"){
                    $user->password = Hash::make($request->data);
                    $user->save();
                } elseif ($request->type == "email"){
                    $request->validate(['data' => 'required|string|email']);
                    $user->email = $request->data;
                    $user->save();
                }
                return $this->respondWithSuccess('Changement done', 200);
            } else {
                return $this->respondWithError("Error while change your information",400);
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