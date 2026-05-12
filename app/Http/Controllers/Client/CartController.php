<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {
    }

    public function index()
    {
        $cart = $this->cartService->getCart();
        $cart->load(['items.variant', 'items.product']);

        return view('client.pages.task.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'selected_product_attribute_value_ids' => 'nullable|array',
            'selected_product_attribute_value_ids.*' => 'integer|exists:product_attribute_values,id',
        ]);

        $product = Product::query()
            ->with(['attributeValues', 'variants'])
            ->findOrFail($validated['product_id']);

        $pavIds = collect($validated['selected_product_attribute_value_ids'] ?? [])
            ->filter()
            ->unique()
            ->values();

        $pavs = ProductAttributeValue::query()
            ->where('product_id', $product->id)
            ->whereIn('id', $pavIds)
            ->get();

        if ($pavIds->count() !== $pavs->count()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid attribute selections for this product.',
            ], 422);
        }

        $requiredAttributeIds = $product->attributeValues->pluck('attribute_id')->unique();

        if ($requiredAttributeIds->isNotEmpty()) {
            $selectedByAttribute = $pavs->groupBy('attribute_id');
            if ($selectedByAttribute->count() !== $requiredAttributeIds->count()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please choose one option for each attribute before adding to cart.',
                ], 422);
            }
            foreach ($requiredAttributeIds as $attrId) {
                if ($selectedByAttribute->get($attrId)?->count() !== 1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Please choose exactly one value per attribute.',
                    ], 422);
                }
            }
        }

        $variantId = $validated['product_variant_id'] ?? null;
        if (is_array($variantId)) {
            $variantId = $variantId[0] ?? null;
        }
        if ($variantId !== null) {
            $variantId = (int) $variantId;
        }

        if ($pavs->isNotEmpty() && $product->type !== 'configurable') {
            $variantId = null;
        }

        if ($variantId && $pavs->isEmpty()) {
            $belongs = ProductVariant::query()
                ->where('product_id', $product->id)
                ->whereKey($variantId)
                ->exists();
            if (! $belongs) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid variant for this product.',
                ], 422);
            }
        }

        if ($product->type === 'configurable' && $product->variants->isNotEmpty() && $requiredAttributeIds->isNotEmpty()) {
            $resolved = $this->cartService->resolveVariantId($product, $pavs);
            if (! $resolved) {
                return response()->json([
                    'status' => false,
                    'message' => 'That combination is not available. Try another option.',
                ], 422);
            }
        }

        $item = $this->cartService->addToCart(
            (int) $validated['product_id'],
            (int) $validated['quantity'],
            $variantId,
            $pavIds->all()
        );

        return response()->json([
            'status' => true,
            'message' => 'Product added to cart',
            'cart' => $this->cartService->getCart(),
            'item' => $item,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $this->cartService->updateQuantity(
            (int) $request->item_id,
            (int) $request->quantity
        );

        return response()->json([
            'status' => true,
            'message' => 'Cart updated',
        ]);
    }

    public function increment(int $id)
    {
        $item = $this->cartService->increment($id);

        return response()->json([
            'status' => true,
            'message' => 'Quantity increased',
            'item' => $item,
            'cart' => $this->cartService->getCart(),
        ]);
    }

    public function decrement(int $id)
    {
        $item = $this->cartService->decrement($id);

        return response()->json([
            'status' => true,
            'message' => 'Quantity decreased',
            'item' => $item,
            'cart' => $this->cartService->getCart(),
        ]);
    }

    public function remove(int $id)
    {
        $success = $this->cartService->removeItem($id);

        return response()->json([
            'status' => $success,
            'message' => $success ? 'Item removed' : 'Failed to remove item',
            'cart' => $this->cartService->getCart(),
        ]);
    }

    public function clear()
    {
        $success = $this->cartService->clearCart();

        return response()->json([
            'status' => $success,
            'message' => $success ? 'Cart cleared' : 'Failed to clear cart',
            'cart' => $this->cartService->getCart(),
        ]);
    }
}
