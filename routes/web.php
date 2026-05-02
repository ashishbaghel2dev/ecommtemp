<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;



/*
|------------------------
| PUBLIC ROUTES
|------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');



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



/*
|------------------------
| USER ROUTES
|------------------------
*/


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

});







/*
|------------------------
| ADMIN ROUTES
|------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

     Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('home');

 });




/*
|------------------------
| SUPER ADMIN ROUTES
|------------------------
*/




