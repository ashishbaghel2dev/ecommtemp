<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\WishlistService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class WishlistController extends Controller
{
    public function index()
    {
        $products = $this->wishlistProducts();

        return view('client.pages.wishlist.index', compact('products'));
    }

    public function toggle(
        Product $product,
        WishlistService $wishlistService
    ) {

        $added = $wishlistService->toggle(
            $product->id
        );

        return response()->json([
            'success' => true,
            'added'   => $added,
            'count'   => $this->wishlistCount($product->id, $added),
        ]);
    }

    private function wishlistProducts()
    {
        if (Auth::check()) {
            return Product::query()
                ->active()
                ->whereIn('id', Wishlist::query()
                    ->where('user_id', Auth::id())
                    ->select('product_id'))
                ->with(['category'])
                ->latest()
                ->get();
        }

        $ids = collect(json_decode(Cookie::get('wishlist_products', '[]'), true))
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Product::query()
            ->active()
            ->whereIn('id', $ids)
            ->with(['category'])
            ->get()
            ->sortBy(fn ($product) => $ids->search($product->id))
            ->values();
    }

    private function wishlistCount(?int $toggledProductId = null, ?bool $added = null): int
    {
        if (Auth::check()) {
            return Wishlist::query()
                ->where('user_id', Auth::id())
                ->count();
        }

        $ids = collect(json_decode(Cookie::get('wishlist_products', '[]'), true))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique();

        if ($toggledProductId !== null && $added !== null) {
            $ids = $added
                ? $ids->push((int) $toggledProductId)
                : $ids->reject(fn ($id) => (int) $id === (int) $toggledProductId);
        }

        return $ids->unique()->count();
    }
}
