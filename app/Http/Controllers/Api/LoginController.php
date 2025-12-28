<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    // User Login
    function login(Request $request)
    {
        // $user = User::find(Auth::user()->id);
        // if (!$user || !Hash::check($request->password, $user->password)) {
        //     return response()->json(['message' => 'Invalid credentials'], 401);
        // }

        // 2nd approach
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {

            $user = User::find(Auth::user()->id);

           
            // $user = Auth::user()->only(['email', 'password']);  // convert to array data
            // $user = User::where('id', Auth::id())->select('email', 'password')->first();

            // $user->tokens()->update(['revoked' => true]); // revoke all tokens before creating new token
            // DB::table('oauth_access_tokens')->where('expires_at', '<', now())->orWhere('revoked', true)->delete(); // delete expired and revoked tokens
            $user_token['token'] = $user->createToken('appToken')->accessToken;

            // Log::info('This is a critical system alert!', ['user_id' =>  $user->id]);

            return response()->json([
                'success' => true,
                'token_type' => 'Bearer',
                'access_token' => $user_token
            ], 200);

        } else {
            // failure to authenticate
            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate.',
            ], 401);
        }
    }   
}
