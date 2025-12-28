<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// Route::post('/register', [RegistrationController::class, 'register']);
// Route::post('/login', [LoginController::class, 'login']);

// Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
//     Route::post('login', [LoginController::class, 'store']);
// });


Route::name('api.')->group(function () {

    Route::post('register', [RegistrationController::class, 'register'])->name('register');
    Route::post('login', [LoginController::class, 'login'])->name('login');
});

Route::middleware(['auth:api', 'verified'])->prefix('users')->group(function() {
    Route::get('profile/index', [ProfileController::class, 'index'])->name('api.user.profile.index');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('api.user.profile.update');
});