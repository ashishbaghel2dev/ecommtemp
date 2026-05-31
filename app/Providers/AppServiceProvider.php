<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
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
            : collect(json_decode(Cookie::get('wishlist_products', '[]'), true))
                ->filter()
                ->unique()
                ->count();

        $view->with('navCategories', Category::query()
            ->active()
            ->parent()
            ->sorted()
            ->with(['children' => function ($query) {
                $query->active()->sorted();
            }])
            ->get())
            ->with('navCartCount', (int) ($cart?->total_quantity ?? 0))
            ->with('navCartTotal', (float) ($cart?->grand_total ?? $cart?->subtotal ?? 0))
            ->with('navWishlistCount', (int) $wishlistCount);
    });
}
}
