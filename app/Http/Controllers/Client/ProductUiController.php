<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Services\ProductPageService;
use Illuminate\Support\Facades\Cookie;


class ProductUiController extends Controller
{
    public function show(Product $product, ProductPageService $productPageService)
    {
        abort_unless($product->is_active, 404);

        Product::query()
            ->whereKey($product->id)
            ->increment('view_count');

        $recentlyViewed = json_decode(
            Cookie::get('recently_viewed_products', '[]'),
            true
        );

        if (! is_array($recentlyViewed)) {
            $recentlyViewed = [];
        }

        $recentlyViewed = array_values(array_diff($recentlyViewed, [$product->id]));

        array_unshift($recentlyViewed, $product->id);

        Cookie::queue(
            'recently_viewed_products',
            json_encode(array_slice($recentlyViewed, 0, 10)),
            60 * 24 * 30
        );

        $data = $productPageService->getProductPageData($product);
        $data['viewCount'] = Product::query()
            ->whereKey($product->id)
            ->value('view_count');
        $data['visibleReviews'] = $this->getVisibleReviews($product);
        $data['recentlyViewedProducts'] = $this->getRecentlyViewedProducts($recentlyViewed, $product);

        return view('client.pages.products.show', $data);
    }

    private function getVisibleReviews(Product $product)
    {
        return Review::query()
            ->with(['user', 'images'])
            ->where('product_id', $product->id)
            ->where('status', 'approved')
            ->latest()
            ->take(10)
            ->get();
    }

    private function getRecentlyViewedProducts(array $recentlyViewedIds, Product $product)
    {
        $ids = collect($recentlyViewedIds)
            ->filter(fn ($id) => (int) $id !== (int) $product->id)
            ->unique()
            ->take(4)
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Product::query()
            ->active()
            ->whereIn('id', $ids)
            ->whereNotNull('slug')
            ->with(['category'])
            ->get()
            ->sortBy(fn ($item) => $ids->search($item->id))
            ->values();
    }
}
