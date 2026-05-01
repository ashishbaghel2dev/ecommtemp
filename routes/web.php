<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|------------------------
| PUBLIC ROUTES
|------------------------
*/
Route::get('/', function () {
    return view('client.home.home');
});

/*
|------------------------
| AUTH ROUTES (Login/Register/Verify)
|------------------------
*/

Route::post('/login', [LoginController::class, 'login']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');



Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/callback', [LoginController::class, 'handleGoogleCallback']);
});

// Facebook Authentication Routes
Route::prefix('auth/facebook')->group(function () {
    Route::get('/redirect', [LoginController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('/callback', [LoginController::class, 'handleFacebookCallback']);
});




/*
|------------------------
| ADMIN ROUTES
|------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard', function () {
            return "Admin Dashboard";
        });

    });









Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
