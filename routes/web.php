<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\UrlRedirectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Invitation acceptance routes removed - users created immediately with credentials sent via email

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Short URLs routes
    Route::prefix('short-urls')->name('short-urls.')->group(function () {
        Route::get('/index', [ShortUrlController::class, 'index'])->name('index');
        Route::get('/create', [ShortUrlController::class, 'create'])->name('create');
        Route::match(['get', 'post'], '/store', [ShortUrlController::class, 'store'])->name('store');
        Route::get('/{shortUrl}', [ShortUrlController::class, 'show'])->name('show');
        Route::get('/{shortUrl}/edit', [ShortUrlController::class, 'edit'])->name('edit');
        Route::put('/{shortUrl}', [ShortUrlController::class, 'update'])->name('update');
        Route::delete('/{shortUrl}', [ShortUrlController::class, 'destroy'])->name('destroy');
    });

    // Invitations routes (for sending invites)
    Route::prefix('invitations')->name('invitations.')->group(function () {
        Route::get('/', [InvitationController::class, 'index'])->name('index');
        Route::get('/create', [InvitationController::class, 'create'])->name('create');
        Route::post('/', [InvitationController::class, 'store'])->name('store');
        Route::delete('/{invitation}', [InvitationController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Public URL Redirect (Catch-all route - MUST be last!)
|--------------------------------------------------------------------------
*/
Route::get('/{shortCode}', [UrlRedirectController::class, 'redirect'])->where('shortCode', '[a-zA-Z0-9]+')->name('url.redirect');
