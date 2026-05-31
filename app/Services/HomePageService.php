<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;

class HomePageService
{
    public function getHomePageData(): array
    {
        $products = $this->getProducts();

        /*
        |--------------------------------------------------------------------------
        | Variant Attribute IDs
        |--------------------------------------------------------------------------
        */

        $variantAttributeIds = $products
            ->flatMap(function ($product) {

                if (!$product->relationLoaded('variants')) {
                    return [];
                }

                return $product->variants;
            })
            ->flatMap(function ($variant) {

                if (!is_array($variant->attributes)) {
                    return [];
                }

                return array_keys($variant->attributes);
            })
            ->filter()
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Variant Value IDs
        |--------------------------------------------------------------------------
        */

        $variantValueIds = $products
            ->flatMap(function ($product) {

                if (!$product->relationLoaded('variants')) {
                    return [];
                }

                return $product->variants;
            })
            ->flatMap(function ($variant) {

                if (!is_array($variant->attributes)) {
                    return [];
                }

                return array_values($variant->attributes);
            })
            ->filter()
            ->unique()
            ->values();

        return [
            'products'          => $products,
            'variantAttributes' => $this->getVariantAttributes($variantAttributeIds),
            'variantValues'     => $this->getVariantValues($variantValueIds),
            'reviews'           => $this->getReviews(),
            'homeSliders'       => $this->getHomeSliders(),
            'homeCategories'    => $this->getHomeCategories(),
            'recentlyViewed'    => $this->getRecentlyViewedProducts(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    private function getProducts(): Collection
    {
        $search = trim((string) request('q', ''));

        return Product::query()
            ->active()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->with([
                'category',
                'labels',
                'variants',
                'attributeValues.attribute',
                'attributeValues.attributeValue',
            ])
            ->latest()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Variant Attributes
    |--------------------------------------------------------------------------
    */

    private function getVariantAttributes(Collection $ids): Collection
    {
        if ($ids->isEmpty()) {
            return collect();
        }

        return Attribute::query()
            ->whereIn('id', $ids)
            ->pluck('name', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Variant Values
    |--------------------------------------------------------------------------
    */

    private function getVariantValues(Collection $ids): Collection
    {
        if ($ids->isEmpty()) {
            return collect();
        }

        return AttributeValue::query()
            ->whereIn('id', $ids)
            ->pluck('value', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Reviews
    |--------------------------------------------------------------------------
    */

    private function getReviews(): Collection
    {
        return Review::query()
            ->with([
                'user',
                'product',
                'images',
            ])
            ->where('status', 'approved')
            ->latest()
            ->take(10)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Home Sliders
    |--------------------------------------------------------------------------
    */

    private function getHomeSliders(): Collection
    {
        return Banner::query()
            ->where('is_active', 1)
            ->where('position', 'home_slider')
            ->orderBy('priority', 'asc')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Home Categories
    |--------------------------------------------------------------------------
    */

    private function getHomeCategories(): Collection
    {
        return Category::query()
            ->active()
            ->sorted()
            ->with(['parent'])
            ->withCount(['children', 'products'])
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Recently Viewed Products
    |--------------------------------------------------------------------------
    */

    private function getRecentlyViewedProducts(): Collection
    {
        $recentlyViewedIds = json_decode(
            Cookie::get('recently_viewed_products', '[]'),
            true
        );

        if (
            empty($recentlyViewedIds) ||
            !is_array($recentlyViewedIds)
        ) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', $recentlyViewedIds)
            ->with([
                'category',
                'labels',
                'variants',
            ])
            ->get();
    }
}
