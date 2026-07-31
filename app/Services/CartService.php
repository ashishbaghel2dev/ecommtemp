<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\AdminSetting;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class CartService
{
    const COOKIE_KEY = 'guest_cart';

    public function getCart(): Cart
    {
        if (Auth::check()) {

            return Cart::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'status'  => 'active',
                ],
                [
                    'session_id' => session()->getId(),
                    'currency'   => 'INR',
                ]
            );
        }

        return Cart::firstOrCreate(
            [
                'session_id' => session()->getId(),
                'status'     => 'active',
            ],
            [
                'currency' => 'INR',
            ]
        );
    }

    public function attributeSignatureFromPavIds(array $ids): string
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        sort($ids);

        return $ids === [] ? '' : hash('sha256', implode(',', $ids));
    }

    public function resolveVariantId(Product $product, Collection $pavs): ?int
    {
        if ($product->type !== 'configurable' || $product->variants->isEmpty()) {
            return null;
        }

        $map = [];
        foreach ($pavs as $pav) {
            if ($pav->attribute_value_id) {
                $map[(int) $pav->attribute_id] = (int) $pav->attribute_value_id;
            }
        }

        if ($map === []) {
            return null;
        }

        $target = $this->normalizeAttributeMap($map);

        foreach ($product->variants as $variant) {
            if (! is_array($variant->attributes)) {
                continue;
            }
            $vMap = [];
            foreach ($variant->attributes as $k => $v) {
                $vMap[(int) $k] = (int) $v;
            }
            if ($this->normalizeAttributeMap($vMap) === $target) {
                return (int) $variant->id;
            }
        }

        return null;
    }

    private function normalizeAttributeMap(array $map): string
    {
        ksort($map);

        return json_encode($map);
    }

    public function buildAttributeMeta(Collection $pavs): array
    {
        return $pavs->map(function (ProductAttributeValue $pav) {
            return [
                'id'                  => $pav->id,
                'product_id'          => $pav->product_id,
                'attribute_id'        => $pav->attribute_id,
                'attribute_value_id'  => $pav->attribute_value_id,
                'value'               => $pav->value,
                'attribute_name'      => $pav->relationLoaded('attribute') && $pav->attribute
                    ? $pav->attribute->name
                    : null,
                'attribute_value_label' => $pav->relationLoaded('attributeValue') && $pav->attributeValue
                    ? $pav->attributeValue->value
                    : ($pav->value ?? null),
            ];
        })->values()->all();
    }

    public function addToCart(int $productId, int $quantity = 1, ?int $variantId = null, array $selectedProductAttributeValueIds = [])
    {
        $product = Product::query()
            ->with(['variants', 'attributeValues'])
            ->findOrFail($productId);

        $pavIds = array_values(array_unique(array_filter(array_map('intval', $selectedProductAttributeValueIds))));

        $pavs = $this->loadProductAttributeValues($product->id, $pavIds);

        return $this->addToDatabaseCart($product, $quantity, $variantId, $pavs);
    }

    private function loadProductAttributeValues(int $productId, array $pavIds): Collection
    {
        if ($pavIds === []) {
            return collect();
        }

        return ProductAttributeValue::query()
            ->with(['attribute', 'attributeValue'])
            ->where('product_id', $productId)
            ->whereIn('id', $pavIds)
            ->get();
    }

    private function addToDatabaseCart(Product $product, int $quantity, ?int $variantId, Collection $pavs): CartItem
    {
        $cart = $this->getCart();

        $signature = $this->attributeSignatureFromPavIds($pavs->pluck('id')->all());

        $variant = null;
        if ($pavs->isNotEmpty()) {
            $product->loadMissing('variants');
            $resolved = $this->resolveVariantId($product, $pavs);
            if ($product->type === 'configurable' && $product->variants->isNotEmpty()) {
                $variantId = $resolved;
            }
        }

        if ($variantId) {
            $variant = ProductVariant::query()
                ->where('product_id', $product->id)
                ->whereKey($variantId)
                ->first();
        }

        $price = (float) ($variant
            ? $variant->getFinalPriceAttribute()
            : $product->final_price);

        $originalPrice = (float) ($variant
            ? ($variant->price ?? $product->price)
            : $product->price);

        $unitDiscount = max(0, $originalPrice - $price);

        $item = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->where('attribute_signature', $signature)
            ->first();

        $meta = $pavs->isNotEmpty()
            ? ['product_attribute_values' => $this->buildAttributeMeta($pavs)]
            : null;

        if ($item) {
            $item->quantity += $quantity;
        } else {
            $item = new CartItem();
            $item->cart_id = $cart->id;
            $item->product_id = $product->id;
            $item->product_variant_id = $variantId;
            $item->attribute_signature = $signature;
            $item->product_name = $product->name;
            $item->product_sku = $variant ? $variant->sku : $product->sku;
            $item->product_image = $product->image ?? null;
            $item->price = $price;
            $item->quantity = $quantity;
            $item->original_price = $originalPrice;
            $item->discount_amount = $unitDiscount * $quantity;
            $item->meta = $meta;
        }

        $item->original_price = $originalPrice;
        $item->discount_amount = $unitDiscount * $item->quantity;

        if ($meta !== null) {
            $item->meta = $meta;
        }

        $item->subtotal = $item->price * $item->quantity;
        $item->total = $item->subtotal;

        $item->save();

        $this->recalculate($cart);

        return $item;
    }

    public function claimGuestCartForUser(int $userId, string $previousSessionId): void
    {
        $guestCart = Cart::query()
            ->with('items')
            ->where('session_id', $previousSessionId)
            ->whereNull('user_id')
            ->first();

        if (! $guestCart) {
            return;
        }

        $userCart = Cart::query()->firstOrCreate(
            [
                'user_id' => $userId,
                'status' => 'active',
            ],
            [
                'session_id' => session()->getId(),
                'currency' => 'INR',
            ]
        );

        foreach ($guestCart->items as $guestItem) {
            $sig = $guestItem->attribute_signature ?? '';

            $existing = CartItem::query()
                ->where('cart_id', $userCart->id)
                ->where('product_id', $guestItem->product_id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->where('attribute_signature', $sig)
                ->first();

            if ($existing) {
                $existing->quantity += $guestItem->quantity;
                $existing->discount_amount = max(0, ((float) $existing->original_price - (float) $existing->price) * $existing->quantity);
                $existing->subtotal = $existing->price * $existing->quantity;
                $existing->total = $existing->subtotal;
                $existing->save();
                $guestItem->delete();
            } else {
                $guestItem->cart_id = $userCart->id;
                $guestItem->save();
            }
        }

        $guestCart->refresh();
        if ($guestCart->items()->count() === 0) {
            $guestCart->delete();
        }

        $this->recalculate($userCart);
    }

    public function mergeGuestCart(): void
    {
        if (! Auth::check()) {
            return;
        }

        $cookieCart = json_decode(Cookie::get(self::COOKIE_KEY, '[]'), true);

        if (empty($cookieCart) || ! is_array($cookieCart)) {
            return;
        }

        foreach ($cookieCart as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if (! $product) {
                continue;
            }

            $pavIds = isset($item['pav_ids']) && is_array($item['pav_ids'])
                ? $item['pav_ids']
                : [];

            $this->addToDatabaseCart(
                $product,
                (int) ($item['quantity'] ?? 1),
                isset($item['variant_id']) ? (int) $item['variant_id'] : null,
                $this->loadProductAttributeValues($product->id, $pavIds)
            );
        }

        Cookie::queue(Cookie::forget(self::COOKIE_KEY));
    }

    public function increment(int $itemId): CartItem
    {
        $item = CartItem::findOrFail($itemId);
        $item->incrementQty();
        $item->discount_amount = max(0, ((float) $item->original_price - (float) $item->price) * $item->quantity);
        $item->save();
        $this->recalculate($item->cart);

        return $item;
    }

    public function decrement(int $itemId): ?CartItem
    {
        $item = CartItem::findOrFail($itemId);
        if ($item->quantity > 1) {
            $item->decrementQty();
            $item->discount_amount = max(0, ((float) $item->original_price - (float) $item->price) * $item->quantity);
            $item->save();
        } else {
            $this->removeItem($itemId);

            return null;
        }
        $this->recalculate($item->cart);

        return $item;
    }

    public function updateQuantity(int $itemId, int $quantity): CartItem
    {
        $item = CartItem::findOrFail($itemId);

        $item->quantity = $quantity;
        $item->discount_amount = max(0, ((float) $item->original_price - (float) $item->price) * $quantity);
        $item->subtotal = $item->price * $quantity;
        $item->total = $item->subtotal;

        $item->save();

        $this->recalculate($item->cart);

        return $item;
    }

    public function removeItem(int $itemId): bool
    {
        $item = CartItem::findOrFail($itemId);

        $cart = $item->cart;

        $item->delete();

        $this->recalculate($cart);

        return true;
    }

    public function clearCart(): bool
    {
        $cart = $this->getCart();

        $cart->items()->delete();

        $this->recalculate($cart);

        return true;
    }

    public function recalculate(Cart $cart): void
    {
        $items = $cart->items()->get();

        foreach ($items as $item) {
            $originalPrice = (float) ($item->original_price ?: $item->price);
            $unitDiscount = max(0, $originalPrice - (float) $item->price);
            $lineDiscount = $unitDiscount * (int) $item->quantity;

            if ((float) $item->discount_amount !== $lineDiscount) {
                $item->forceFill(['discount_amount' => $lineDiscount])->save();
            }
        }

        $items = $cart->items()->get();

        $subtotal = $items->sum('subtotal');
        $shipping = app(ShippingService::class)->calculate($cart);
        $taxSettings = AdminSetting::taxConfig();
        $tax = $taxSettings['tax_enabled'] && $taxSettings['tax_rate'] > 0
            ? round((float) $subtotal * ($taxSettings['tax_rate'] / 100), 2)
            : 0;
        $discount = 0;

        if ($cart->coupon_code) {
            $couponTotals = app(CouponService::class)->recalculateCart($cart, Auth::user());
            $discount = $couponTotals['coupon_discount'] ?? (float) $cart->discount_total;
            $shipping = $couponTotals['shipping_total'] ?? $shipping;
            $tax = $couponTotals['tax_total'] ?? $tax;
        }

        $cart->update([
            'total_items'    => $items->count(),
            'total_quantity' => $items->sum('quantity'),
            'subtotal'       => $subtotal,
            'discount_total' => $discount,
            'shipping_total' => $shipping,
            'tax_total'      => $tax,
            'grand_total'    => max(0, $subtotal - $discount + $shipping + $tax),
        ]);
    }
}
