<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
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
        Request $request,
        WishlistService $wishlistService,
        CartService $cartService
    ) {
        $validated = $request->validate([
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'selected_product_attribute_value_ids' => 'nullable|array',
            'selected_product_attribute_value_ids.*' => 'integer|exists:product_attribute_values,id',
        ]);

        $product->load(['attributeValues', 'variants']);

        $pavIds = collect($validated['selected_product_attribute_value_ids'] ?? [])
            ->filter()
            ->unique()
            ->values();

        $pavs = ProductAttributeValue::query()
            ->with(['attribute', 'attributeValue'])
            ->where('product_id', $product->id)
            ->whereIn('id', $pavIds)
            ->get();

        if ($pavIds->count() !== $pavs->count()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attribute selections for this product.',
            ], 422);
        }

        $variantId = $validated['product_variant_id'] ?? null;
        if ($variantId !== null) {
            $variantId = (int) $variantId;
        }

        if ($pavs->isNotEmpty() && $product->type === 'configurable' && $product->variants->isNotEmpty()) {
            $variantId = $cartService->resolveVariantId($product, $pavs);
            if (! $variantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'That combination is not available. Try another option.',
                ], 422);
            }
        }

        if ($variantId && $pavs->isEmpty()) {
            $belongs = ProductVariant::query()
                ->where('product_id', $product->id)
                ->whereKey($variantId)
                ->exists();

            if (! $belongs) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid variant for this product.',
                ], 422);
            }
        }

        $pavIdArray = $pavIds->all();
        $meta = $pavs->isNotEmpty()
            ? ['product_attribute_values' => $cartService->buildAttributeMeta($pavs)]
            : null;

        $added = $wishlistService->toggle(
            $product->id,
            $variantId,
            $pavIdArray,
            $meta
        );

        return response()->json([
            'success' => true,
            'added'   => $added,
            'message' => $added ? 'Added to wishlist.' : 'Removed from wishlist.',
            'count'   => $this->wishlistCount($wishlistService, $product->id, $variantId, $pavIdArray, $added),
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

        $ids = collect(app(WishlistService::class)->cookieItems())
            ->pluck('product_id')
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

    private function wishlistCount(
        WishlistService $wishlistService,
        ?int $toggledProductId = null,
        ?int $variantId = null,
        array $pavIds = [],
        ?bool $added = null
    ): int
    {
        if (Auth::check()) {
            return Wishlist::query()
                ->where('user_id', Auth::id())
                ->count();
        }

        $items = collect($wishlistService->cookieItems());

        if ($toggledProductId !== null && $added !== null) {
            $signature = $wishlistService->attributeSignatureFromPavIds($pavIds);
            $key = $wishlistService->entryKey((int) $toggledProductId, $variantId, $signature);

            $items = $added
                ? $items->push(['key' => $key])
                : $items->reject(fn ($item) => ($item['key'] ?? '') === $key);
        }

        return $items->pluck('key')->unique()->count();
    }
}
