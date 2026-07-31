<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductLabel;
use App\Models\Review;
use App\Services\ProductPageService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;


class ProductUiController extends Controller
{
    public function index(Request $request)
    {
        $priceExpression = $this->finalPriceExpression();

        $productsQuery = Product::query()
            ->active()
            ->whereNotNull('slug')
            ->with([
                'category',
                'images',
                'labels',
                'variants',
                'attributeValues.attribute',
                'attributeValues.attributeValue',
            ])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q')->toString();
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', (int) $request->category))
            ->when($request->filled('min_price'), function ($query) use ($request, $priceExpression) {
                $query->whereRaw("{$priceExpression} >= ?", [(float) $request->min_price]);
            })
            ->when($request->filled('max_price'), function ($query) use ($request, $priceExpression) {
                $query->whereRaw("{$priceExpression} <= ?", [(float) $request->max_price]);
            })
            ->when($request->boolean('in_stock'), fn ($query) => $query->where('in_stock', true)->where('stock', '>', 0))
            ->when($request->boolean('best_sellers'), function ($query) {
                $query->whereHas('labels', fn ($labelQuery) => $labelQuery->where('slug', 'best-product'));
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

        $priceBounds = Product::query()
            ->active()
            ->selectRaw("MIN({$priceExpression}) as min_price, MAX({$priceExpression}) as max_price")
            ->first();

        $categories = Category::query()
            ->active()
            ->sorted()
            ->get(['id', 'name', 'slug']);

        return view('client.pages.products.index', [
            'products' => $products,
            'categories' => $categories,
            'priceBounds' => $priceBounds,
            'currentSort' => $request->input('sort', 'latest'),
        ]);
    }

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

    public function label(string $label)
    {
        $slug = $label;
        $label = ProductLabel::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        $title = $label?->name ?? match ($slug) {
            'new-arrived' => 'New Arrivals',
            'best-product' => 'Bestsellers',
            default => Str::headline($slug),
        };

        $products = $label
            ? $label->products()
                ->active()
                ->whereNotNull('slug')
                ->with([
                    'category',
                    'images' => fn ($query) => $query->orderByDesc('is_main')->orderBy('sort_order'),
                    'labels',
                    'variants' => fn ($query) => $query->where('is_active', true),
                    'attributeValues.attribute',
                    'attributeValues.attributeValue',
                ])
                ->latest('products.created_at')
                ->paginate(12)
            : new LengthAwarePaginator([], 0, 12, LengthAwarePaginator::resolveCurrentPage(), [
                'path' => request()->url(),
            ]);

        return view('client.pages.products.label', [
            'label' => $label,
            'labelTitle' => $title,
            'products' => $products,
        ]);
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
            ->with([
                'category',
                'images' => fn ($query) => $query->orderByDesc('is_main')->orderBy('sort_order'),
            ])
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
