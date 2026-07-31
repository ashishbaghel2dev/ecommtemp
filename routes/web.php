<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\AboutPartController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\HomeCarouselImageController;
use App\Http\Controllers\Admin\HomePopupController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductLabelController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\SocialMediaLinkController;
use App\Http\Controllers\Admin\TrashController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WishlistManagementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordOtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\CouponController as ClientCouponController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductUiController;
use App\Http\Controllers\Client\ReviewController;
use App\Http\Controllers\Client\SitemapController;
use App\Http\Controllers\Client\WishlistController;
use App\Http\Controllers\Client\InquiryController as ClientInquiryController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\LegalController;
use App\Http\Controllers\Client\FaqController as ClientFaqController;
use App\Http\Controllers\Client\GalleryController as ClientGalleryController;
use App\Http\Controllers\Client\BlogController as ClientBlogController;
use App\Http\Controllers\Client\TagController as ClientTagController;


/*
|------------------------
| PUBLIC ROUTES
|------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap.xml');

Route::get('/products', [ProductUiController::class, 'index'])
    ->name('client.products.index');

Route::get('/products/images/{path}', function (string $path) {
    $file = public_path('product-images/'.$path);

    abort_unless(is_file($file), 404);

    return response()->file($file);
})->where('path', '.*')->name('products.images');

Route::get('/products/{product:slug}', [ProductUiController::class, 'show'])
    ->name('products.show');

Route::get('/categories/{category:slug}', [ProductUiController::class, 'category'])
    ->name('categories.show');

Route::get('/labels/{label:slug}', [ProductUiController::class, 'label'])
    ->name('labels.show');

Route::get('/contact', [ClientInquiryController::class, 'create'])->name('contact');
Route::post('/contact', [ClientInquiryController::class, 'store'])->name('contact.store');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/increment/{id}', [CartController::class, 'increment'])->name('cart.increment');
Route::post('/cart/decrement/{id}', [CartController::class, 'decrement'])->name('cart.decrement');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/coupon/apply', [ClientCouponController::class, 'apply'])->name('coupon.apply');
Route::delete('/coupon/remove', [ClientCouponController::class, 'remove'])->name('coupon.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/login', [CheckoutController::class, 'checkoutLogin'])->name('checkout.login');
Route::post('/checkout/register', [CheckoutController::class, 'checkoutRegister'])->name('checkout.register');
Route::post('/checkout/addresses', [CheckoutController::class, 'storeAddress'])->name('checkout.addresses.store');
Route::put('/checkout/addresses/{address}', [CheckoutController::class, 'updateAddress'])->name('checkout.addresses.update');
Route::delete('/checkout/addresses/{address}', [CheckoutController::class, 'deleteAddress'])->name('checkout.addresses.delete');
Route::post('/checkout/addresses/{address}/default', [CheckoutController::class, 'setDefaultAddress'])->name('checkout.addresses.default');
Route::match(['get', 'post'], '/checkout/review', [CheckoutController::class, 'review'])->name('checkout.review');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/orders/{order}/invoice', [CheckoutController::class, 'invoice'])->name('orders.invoice');

Route::get('/faq', [ClientFaqController::class, 'index'])->name('client.faqs.index');
Route::get('/gallery', [ClientGalleryController::class, 'index'])->name('client.gallery.index');
Route::get('/blog', [ClientBlogController::class, 'index'])->name('client.blogs.index');
Route::redirect('/blogs', '/blog', 301)->name('client.blogs.alias');
Route::get('/blogs/{blog:slug}', [ClientBlogController::class, 'show'])->name('client.blogs.show');
Route::get('/tags', [ClientTagController::class, 'index'])->name('client.tags.index');
Route::get('/tags/{tag:slug}', [ClientTagController::class, 'show'])->name('client.tags.show');


/*
|------------------------
| legal ROUTES
|------------------------
*/



        Route::get('/payment-policy', [LegalController::class, 'paymentPolicy'])
        ->name('payment-policy');

    Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])
        ->name('privacy-policy');

    Route::get('/return-refund-policy', [LegalController::class, 'returnRefundPolicy'])
        ->name('return-refund-policy');

    Route::get('/shipping-policy', [LegalController::class, 'shippingPolicy'])
        ->name('shipping-policy');

    Route::get('/terms-conditions', [LegalController::class, 'termsConditions'])
        ->name('terms-conditions');

/*
|------------------------
| AUTH ROUTES
|------------------------
*/
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');

});

Route::controller(ForgotPasswordOtpController::class)->group(function () {
    Route::get('/forgot-password', 'showEmailForm')->name('password.request');
    Route::post('/forgot-password/send-otp', 'sendOtp')->name('password.otp.send');
    Route::get('/forgot-password/verify', 'showOtpForm')->name('password.otp.form');
    Route::post('/forgot-password/reset', 'resetPassword')->name('password.otp.reset');
    Route::post('/forgot-password/resend-otp', 'resendOtp')->name('password.otp.resend');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
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
    Route::get('/callback', 'handleGoogleCallback')->name('auth.google.callback');
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
    Route::put('/dashboard/password', [DashboardController::class, 'updatePassword'])
        ->name('dashboard.password.update');
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
    Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.index');
    Route::get('/settings/general', [AdminSettingController::class, 'general'])->name('settings.general');
    Route::put('/settings/general', [AdminSettingController::class, 'updateGeneral'])->name('settings.general.update');
    Route::get('/settings/theme', [AdminSettingController::class, 'theme'])->name('settings.theme');
    Route::put('/settings/theme', [AdminSettingController::class, 'updateTheme'])->name('settings.theme.update');
    Route::get('/settings/costs', [AdminSettingController::class, 'costs'])->name('settings.costs');
    Route::put('/settings/costs', [AdminSettingController::class, 'updateCosts'])->name('settings.costs.update');
    Route::get('/settings/search', [AdminSettingController::class, 'search'])->name('settings.search');
    Route::put('/settings/search', [AdminSettingController::class, 'updateSearch'])->name('settings.search.update');
    Route::post('/settings/search/sitemap', [AdminSettingController::class, 'updateSitemap'])->name('settings.sitemap.update');
    Route::get('/settings/seo', [AdminSettingController::class, 'seo'])->name('settings.seo');
    Route::put('/settings/seo', [AdminSettingController::class, 'updateSeo'])->name('settings.seo.update');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/customers', [SalesController::class, 'customers'])->name('sales.customers');
    Route::get('/sales/customers/{user}', [SalesController::class, 'customer'])->name('sales.customers.show');
    Route::get('/sales/items', [SalesController::class, 'items'])->name('sales.items');
    Route::get('/sales/orders/{order}', [SalesController::class, 'show'])->name('sales.orders.show');
    Route::put('/sales/orders/{order}', [SalesController::class, 'update'])->name('sales.orders.update');

    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    Route::get('/home-carousel-images', [HomeCarouselImageController::class, 'index'])->name('home-carousel-images.index');
    Route::put('/home-carousel-images/{homeCarouselImage}', [HomeCarouselImageController::class, 'update'])->name('home-carousel-images.update');
    Route::get('/home-popup', [HomePopupController::class, 'edit'])->name('home-popup.edit');
    Route::put('/home-popup', [HomePopupController::class, 'update'])->name('home-popup.update');

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


    Route::resource('about-parts', AboutPartController::class)
         ->parameters(['about-parts' => 'aboutPart'])
         ->except(['show']);
    Route::resource('gallery', GalleryController::class)->except(['show']);   
    Route::resource('faqs', FaqController::class)->except(['show']);   
    Route::resource('blogs', BlogController::class)->except(['show']);
    Route::resource('tags', TagController::class)->except(['show', 'create', 'store']);  

    Route::get('inquiries', [AdminInquiryController::class, 'index'])->name('inquiries.index');
    Route::patch('inquiries/{inquiry}/status', [AdminInquiryController::class, 'updateStatus'])->name('inquiries.status');
    Route::delete('inquiries/{inquiry}', [AdminInquiryController::class, 'destroy'])->name('inquiries.destroy');

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

    Route::resource('/coupons', AdminCouponController::class)->except(['show']);

    Route::get('/trash/{module?}', [TrashController::class, 'index'])->name('trash.index');
    Route::patch('/trash/{module}/{id}/restore', [TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('/trash/{module}/{id}/force-delete', [TrashController::class, 'forceDelete'])->name('trash.force-delete');

});

/*
|------------------------
| OPTIONAL (LOGOUT)
|------------------------
*/
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
