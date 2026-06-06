<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HomeCarouselImageController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductLabelController;
use App\Http\Controllers\Admin\SocialMediaLinkController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WishlistManagementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductUiController;
use App\Http\Controllers\Client\ReviewController;
use App\Http\Controllers\Client\WishlistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\CartController;


/*
|------------------------
| PUBLIC ROUTES
|------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products/{product:slug}', [ProductUiController::class, 'show'])
    ->name('products.show');

Route::get('/categories/{category:slug}', [ProductUiController::class, 'category'])
    ->name('categories.show');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/increment/{id}', [CartController::class, 'increment'])->name('cart.increment');
Route::post('/cart/decrement/{id}', [CartController::class, 'decrement'])->name('cart.decrement');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

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
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])
        ->name('dashboard.profile');
    Route::get('/dashboard/profile/edit', [DashboardController::class, 'editProfile'])
        ->name('dashboard.profile.edit');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])
        ->name('dashboard.profile.update');
    Route::post('/dashboard/profile/addresses', [DashboardController::class, 'storeAddress'])
        ->name('dashboard.addresses.store');
    Route::put('/dashboard/profile/addresses/{address}', [DashboardController::class, 'updateAddress'])
        ->name('dashboard.addresses.update');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markRead']);

    Route::get('reviews', [ReviewController::class, 'index']);
    Route::get('reviews/{review}', [ReviewController::class, 'show']);


});




Route::get('/reviews', [ReviewController::class, 'index'])
    ->name('reviews.index');

Route::post('/reviews/store', [ReviewController::class, 'store'])
    ->name('reviews.store');
Route::post('/reviews/{review}/helpful', [ReviewController::class, 'helpful'])
    ->name('reviews.helpful');

/*
|------------------------
| ADMIN ROUTES
|------------------------
*/
Route::prefix('admin/dashboard')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    Route::get('/home-carousel-images', [HomeCarouselImageController::class, 'index'])->name('home-carousel-images.index');
    Route::put('/home-carousel-images/{homeCarouselImage}', [HomeCarouselImageController::class, 'update'])->name('home-carousel-images.update');

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
    Route::get('/products/category-attributes/{category}', [ProductController::class, 'categoryAttributes'])->name('products.category-attributes');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/product-labels', [ProductLabelController::class, 'index'])->name('productlabels.index');
    Route::get('/product-labels/create', [ProductLabelController::class, 'create'])->name('productlabels.create');
    Route::post('/product-labels', [ProductLabelController::class, 'store'])->name('productlabels.store');
    Route::get('/product-labels/{id}/edit', [ProductLabelController::class, 'edit'])->name('productlabels.edit');
    Route::put('/product-labels/{id}', [ProductLabelController::class, 'update'])->name('productlabels.update');
    Route::delete('/product-labels/{id}', [ProductLabelController::class, 'destroy'])->name('productlabels.destroy');
    Route::get('/get-subcategories/{id}', [ProductController::class, 'getSubcategories']);

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('admin.reviews.index');
    Route::get('/reviews/{review}/edit', [AdminReviewController::class, 'edit'])->name('admin.reviews.edit');
    Route::put('/reviews/{review}', [AdminReviewController::class, 'update'])->name('admin.reviews.update');
    Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->name('admin.reviews.show');
    Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::post('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('admin.reviews.reject');
    Route::post('/reviews/{review}/reply', [AdminReviewController::class, 'reply'])->name('admin.reviews.reply');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    Route::post('/reviews/{id}/restore', [AdminReviewController::class, 'restore'])->name('admin.reviews.restore');

    Route::get('/wishlists', [WishlistManagementController::class, 'index'])->name('wishlists.index');
    Route::get(
        '/wishlisted-products/{product_id}/users',
        [WishlistManagementController::class, 'showUsers']
    )->name('admin.wishlist.users');

});

/*
|------------------------
| OPTIONAL (LOGOUT)
|------------------------
*/
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
