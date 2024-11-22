<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use App\Mail\EmailResetMail; 
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserTokenController extends Controller
{
    public function createToken(Request $request)
    {   
        try{
            app()->setLocale($request->lang);
            // get ENV variable
            $salt = env('TOKEN_SALT');
            $key = env('MAIL_KEY');

            // decrypt email
            $data = base64_decode($request->email);
            $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
            $ciphertext = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
            $decryptedEmail = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);

            // find user with this email
            $user = User::where('email', $decryptedEmail)->first();


            // create modification access token
            $randomString = Str::random(64); 
            $tokenWithSalt = $randomString . $salt;

            $user->user_token_modification = [
                'token' => hash('sha256', $tokenWithSalt),
                'created_at' => Carbon::now()
            ];
            $user->save();
            
            //send mail in depending on the typeOfToken passed
            switch ($request->typeOfToken) {
                case 'passwordReset':
                    Mail::to($user->email)->send(new PasswordResetMail($randomString.'&&'.$request->email));
                    break;
                case 'emailReset':
                    Mail::to($user->email)->send(new EmailResetMail($randomString.'&&'.$request->email));
                    break;
                default:
                    return response()->json([
                        'error' => 'The type of the token can be usable'
                    ], 400);
            }

            return $this->respondWithSuccess('Email envoyé avec succès');

        } catch (\Exception $e) {
            // Error management
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function verifyToken($resetPasswordOrEmailToken, $decryptedEmail)
    {
        try{
            // get env variable
            $salt = env('TOKEN_SALT');
            $tokenToVerify = hash('sha256',$resetPasswordOrEmailToken . $salt);
            
            $user = User::where('email', $decryptedEmail)->first();
            if (!$user)  return 400;

            $timeLimit = Carbon::now()->subMinutes(10);
            $userToken = json_decode($user->user_token_modification);

            //shape date to good format to compare
            $createdAt = Carbon::parse($userToken->created_at);
            
            if ($userToken->token == $tokenToVerify && $createdAt->greaterThanOrEqualTo($timeLimit)) {
                return 200;
            } else {
                return 400;
            }

        } catch (\Exception $e) {
            // Error management
            return 400;
        }
    }
}
