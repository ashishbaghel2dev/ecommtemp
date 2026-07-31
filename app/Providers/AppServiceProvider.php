<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\AdminSetting;
use App\Models\Cart;
use App\Models\HomeCarouselImage;
use App\Models\Product;
use App\Models\SocialMediaLink;
use App\Models\Wishlist;
use App\Services\WishlistService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
 public function boot(): void
{
    Paginator::useBootstrap();

    View::composer('client.includes.navbar', function ($view) {
        $cart = null;

        if (Auth::check()) {
            $cart = Cart::query()
                ->where('user_id', Auth::id())
                ->where('status', 'active')
                ->latest()
                ->first();
        } elseif (session()->isStarted()) {
            $cart = Cart::query()
                ->where('session_id', session()->getId())
                ->whereNull('user_id')
                ->where('status', 'active')
                ->latest()
                ->first();
        }

        $wishlistCount = Auth::check()
            ? Wishlist::query()->where('user_id', Auth::id())->count()
            : collect(app(WishlistService::class)->cookieItems())
                ->pluck('key')
                ->unique()
                ->count();

        $navAllProducts = Product::query()
            ->active()
            ->orderBy('name')
            ->take(8)
            ->get(['id', 'name', 'slug']);

        $navBestsellers = Product::query()
            ->active()
            ->whereHas('labels', function ($query) {
                $query->where('slug', 'best-product');
            })
            ->orderBy('name')
            ->take(6)
            ->get(['id', 'name', 'slug']);

        if ($navBestsellers->isEmpty()) {
            $navBestsellers = $navAllProducts->take(4);
        }

        $navCategories = Category::query()
            ->active()
            ->parent()
            ->sorted()
            ->with(['children' => function ($query) {
                $query->active()->sorted();
            }])
            ->with(['products' => function ($query) {
                $query->active()
                    ->orderBy('name')
                    ->select(['id', 'category_id', 'name', 'slug']);
            }])
            ->take(8)
            ->get();

        $navCarouselImage = HomeCarouselImage::query()
            ->whereNotNull('image')
            ->latest()
            ->first();

        $view->with('navCategories', $navCategories)
            ->with('navCartCount', (int) ($cart?->total_quantity ?? 0))
            ->with('navCartTotal', (float) ($cart?->grand_total ?? $cart?->subtotal ?? 0))
            ->with('navWishlistCount', (int) $wishlistCount)
            ->with('navSocialLinks', SocialMediaLink::query()
                ->active()
                ->ordered()
                ->get())
            ->with('navBestsellers', $navBestsellers)
            ->with('navAllProducts', $navAllProducts)
            ->with('navCarouselImage', $navCarouselImage);
    });

    View::composer('client.includes.footer', function ($view) {
        $view->with('footerCategories', Category::query()
            ->active()
            ->sorted()
            ->take(6)
            ->get(['id', 'name', 'slug']))
            ->with('footerSocialLinks', SocialMediaLink::query()
                ->active()
                ->ordered()
                ->get());
    });

    View::composer('client.layouts.app', function ($view) {
        $view->with('siteSettings', AdminSetting::dashboardConfig());
    });

    View::composer('admin.*', function ($view) {
        $view->with('adminSettings', AdminSetting::dashboardConfig());
    });

    View::composer('emails.*', function ($view) {
        $settings = AdminSetting::dashboardConfig();

        $view->with('mailSettings', $settings)
            ->with('brandName', $settings['user_app_name'] ?? $settings['app_name'] ?? config('app.name'));
    });
}
}
