<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SocialMediaLinkController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\ProductController;


/*
|------------------------
| PUBLIC ROUTES
|------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|------------------------
| AUTH ROUTES
|------------------------
*/
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');

});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');

    Route::get('/otp', 'showOtpForm')->name('otp.form');
    Route::post('/otp-verify', 'verifyOtp')->name('otp.verify');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|------------------------
| SOCIAL LOGIN
|------------------------
*/
Route::prefix('auth/google')->controller(LoginController::class)->group(function () {
    Route::get('/redirect', 'redirectToGoogle')->name('auth.google');
    Route::get('/callback', 'handleGoogleCallback');
});

/*
|------------------------
| USER ROUTES
|------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markRead']);

});

/*
|------------------------
| ADMIN ROUTES
|------------------------
*/
Route::prefix('admin/dashboard')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    Route::get('/social-links', [SocialMediaLinkController::class, 'index'])->name('social-links.index');
    Route::get('/social-links/create', [SocialMediaLinkController::class, 'create'])->name('social-links.create');
    Route::post('/social-links', [SocialMediaLinkController::class, 'store'])->name('social-links.store');
    Route::get('/social-links/{social_link}/edit', [SocialMediaLinkController::class, 'edit'])->name('social-links.edit');
    Route::put('/social-links/{social_link}', [SocialMediaLinkController::class, 'update'])->name('social-links.update');
    Route::delete('/social-links/{social_link}', [SocialMediaLinkController::class, 'destroy'])->name('social-links.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/subcategories', [SubCategoryController::class, 'index'])->name('subcategories.index');
    Route::get('/subcategories/create', [SubCategoryController::class, 'create'])->name('subcategories.create');
    Route::post('/subcategories', [SubCategoryController::class, 'store'])->name('subcategories.store');
    Route::get('/subcategories/{id}/edit', [SubCategoryController::class, 'edit'])->name('subcategories.edit');
    Route::put('/subcategories/{id}', [SubCategoryController::class, 'update'])->name('subcategories.update');
    Route::delete('/subcategories/{id}', [SubCategoryController::class, 'destroy'])->name('subcategories.destroy');


    Route::get('/attributes', [AttributeController::class, 'index'])->name('attributes.index');
    Route::get('/attributes/create', [AttributeController::class, 'create'])->name('attributes.create');
    Route::post('/attributes', [AttributeController::class, 'store'])->name('attributes.store');
    Route::get('/attributes/{id}/edit', [AttributeController::class, 'edit'])->name('attributes.edit');
    Route::put('/attributes/{id}', [AttributeController::class, 'update'])->name('attributes.update');
    Route::delete('/attributes/{id}', [AttributeController::class, 'destroy'])->name('attributes.destroy');

    Route::get('/attribute-values', [AttributeValueController::class, 'index'])->name('attribute-values.index');
    Route::get('/attribute-values/create', [AttributeValueController::class, 'create'])->name('attribute-values.create');
    Route::post('/attribute-values', [AttributeValueController::class, 'store'])->name('attribute-values.store');
    Route::get('/attribute-values/{id}/edit', [AttributeValueController::class, 'edit'])->name('attribute-values.edit');
    Route::put('/attribute-values/{id}', [AttributeValueController::class, 'update'])->name('attribute-values.update');
    Route::delete('/attribute-values/{id}', [AttributeValueController::class, 'destroy'])->name('attribute-values.destroy');


    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');



});

/*
|------------------------
| OPTIONAL (LOGOUT)
|------------------------
*/
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
