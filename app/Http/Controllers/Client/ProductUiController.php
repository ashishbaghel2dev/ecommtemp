<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Services\ProductPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;


class ProductUiController extends Controller
{
    public function category(Category $category, Request $request)
    {
        abort_unless($category->is_active, 404);

        $selectedAttributeValues = collect($request->input('attributes', []))
            ->flatten()
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $priceExpression = $this->finalPriceExpression();

        $productsQuery = Product::query()
            ->active()
            ->where('category_id', $category->id)
            ->with([
                'category',
                'images',
                'labels',
                'variants',
                'attributeValues.attribute',
                'attributeValues.attributeValue',
            ])
            ->when($request->filled('min_price'), function ($query) use ($request, $priceExpression) {
                $query->whereRaw("{$priceExpression} >= ?", [(float) $request->min_price]);
            })
            ->when($request->filled('max_price'), function ($query) use ($request, $priceExpression) {
                $query->whereRaw("{$priceExpression} <= ?", [(float) $request->max_price]);
            })
            ->when($request->boolean('in_stock'), fn ($query) => $query->where('in_stock', true)->where('stock', '>', 0));

        $selectedAttributeValues->each(function (int $attributeValueId) use ($productsQuery) {
            $productsQuery->whereHas('attributeValues', function ($query) use ($attributeValueId) {
                $query->where('attribute_value_id', $attributeValueId);
            });
        });

        match ($request->input('sort', 'latest')) {
            'name_asc' => $productsQuery->orderBy('name'),
            'name_desc' => $productsQuery->orderByDesc('name'),
            'price_low' => $productsQuery->orderByRaw($priceExpression . ' asc'),
            'price_high' => $productsQuery->orderByRaw($priceExpression . ' desc'),
            'popular' => $productsQuery->orderByDesc('view_count'),
            default => $productsQuery->latest(),
        };

        $products = $productsQuery
            ->paginate(12)
            ->withQueryString();

        $categoryProductIds = Product::query()
            ->active()
            ->where('category_id', $category->id)
            ->pluck('id');

        $priceBounds = Product::query()
            ->active()
            ->where('category_id', $category->id)
            ->selectRaw("MIN({$priceExpression}) as min_price, MAX({$priceExpression}) as max_price")
            ->first();

        $filterAttributes = Attribute::query()
            ->active()
            ->where('category_id', $category->id)
            ->where('is_filterable', true)
            ->with(['values' => function ($query) use ($categoryProductIds) {
                $query
                    ->where('is_active', true)
                    ->whereHas('productAttributeValues', function ($subQuery) use ($categoryProductIds) {
                        $subQuery->whereIn('product_id', $categoryProductIds);
                    })
                    ->orderBy('sort_order')
                    ->orderBy('value');
            }])
            ->get()
            ->filter(fn ($attribute) => $attribute->values->isNotEmpty())
            ->values();

        return view('client.pages.products.category', [
            'category' => $category,
            'products' => $products,
            'filterAttributes' => $filterAttributes,
            'selectedAttributeValues' => $selectedAttributeValues,
            'priceBounds' => $priceBounds,
            'currentSort' => $request->input('sort', 'latest'),
        ]);
    }

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

    private function finalPriceExpression(): string
    {
        $now = now()->toDateTimeString();

        return "CASE WHEN sale_price IS NOT NULL AND sale_start IS NOT NULL AND sale_end IS NOT NULL AND sale_start <= " . DB::getPdo()->quote($now) . " AND sale_end >= " . DB::getPdo()->quote($now) . " THEN sale_price ELSE COALESCE(discount_price, price) END";
    }
}
