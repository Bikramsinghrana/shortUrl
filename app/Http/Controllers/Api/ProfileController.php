<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{   

    public function index(){

        $user = auth()->user();

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
