<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'email' => 'required|string|email|max:320|unique:users',
                'password' => 'required|string|min:8|max:60',
            ]);

            // Creation of the user if validation check
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'registration_date' => now(),
            ]);

            // Create acess_token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return acces_token
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
            
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

    public function login(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
    
            // Check if email exist
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new ValidationException('The provided credentials are incorrect.');
            }
    
            // Create access_token
            $token = $user->createToken('auth_token')->plainTextToken;
    
            // Return response with access_token and user information
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
    
        } catch (ValidationException $e) {
            // Validation error 422
            return response()->json([
                'message' => $e->getMessage(),
            ], 422); 
    
        } catch (\Exception $e) {
            // Errors managements
            return response()->json([
                'error' => $e->getMessage(),
            ], 500); 
        }
    }

    public function logout(Request $request)
    {
        try {
            // Get user auth
            $user = Auth::user();

            // Revoke actual token_access
            $user->tokens->each(function ($token) {
                $token->delete();  // Delete all tokens link to this user
            });

            // Return sucessfull message
            return response()->json([
                'message' => 'Successfully logged out.',
            ], 200);

        } catch (\Exception $e) {
            // Errors managements
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
}
};