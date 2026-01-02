<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{   

    public function index(){

        $user = auth()->user();
        // $user = auth()->user()->only(['id', 'name', 'email']);  // return only array data
        // $user = Auth::user();
        // $user = User::where('id', 1)->first();

        // dd($user);
        // var_dump($user);

        if ($user) {

            $data = [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ];
            return response()->json([
                'success' => true,
                'message' => 'Profile data retrieved successfully',
                'data'    => $data
            ], 200);
            
        }else{
            // User is authenticated
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
        
    }
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        // dd($user);
        $user->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data'    => $user,
        ], 200);
    }
}
