<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Support\Collection;

class CouponService
{
    public function availableCoupons(Cart $cart, ?User $user = null): Collection
    {
        $cart->loadMissing('items.product');

        return Coupon::query()
            ->active()
            ->orderBy('expires_at')
            ->get()
            ->filter(fn (Coupon $coupon) => $this->validate($coupon, $cart, $user)['valid'])
            ->values();
    }

    public function apply(Cart $cart, string $code, ?User $user = null): array
    {
        $coupon = Coupon::query()
            ->whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])
            ->first();

        if (! $coupon) {
            return $this->fail('This coupon code does not exist.');
        }

        $validation = $this->validate($coupon, $cart, $user);
        if (! $validation['valid']) {
            return $validation;
        }

        $totals = $this->calculateTotals($cart, $coupon);
        $cart->update([
            'coupon_code' => $coupon->code,
            'discount_total' => $totals['coupon_discount'],
            'shipping_total' => $totals['shipping_total'],
            'tax_total' => $totals['tax_total'],
            'grand_total' => $totals['grand_total'],
        ]);

        return [
            'valid' => true,
            'message' => 'Coupon applied successfully.',
            'coupon' => $coupon,
            'totals' => $totals,
        ];
    }

    public function remove(Cart $cart): array
    {
        $totals = $this->baseTotals($cart);
        $cart->update([
            'coupon_code' => null,
            'discount_total' => 0,
            'shipping_total' => $totals['shipping_total'],
            'tax_total' => $totals['tax_total'],
            'grand_total' => $totals['grand_total'],
        ]);

        return [
            'valid' => true,
            'message' => 'Coupon removed.',
            'totals' => $totals + ['coupon_discount' => 0],
        ];
    }

    public function recalculateCart(Cart $cart, ?User $user = null): array
    {
        $cart->loadMissing('items.product');

        if (! $cart->coupon_code) {
            $totals = $this->baseTotals($cart);
            $cart->update($totals + ['discount_total' => 0]);

            return $totals + ['coupon_discount' => 0, 'coupon' => null];
        }

        $coupon = Coupon::where('code', $cart->coupon_code)->first();
        if (! $coupon) {
            return $this->remove($cart)['totals'] + ['coupon' => null];
        }

        $validation = $this->validate($coupon, $cart, $user);
        if (! $validation['valid']) {
            $cart->update(['coupon_code' => null, 'discount_total' => 0]);
            $totals = $this->baseTotals($cart);
            $cart->update($totals);

            return $totals + ['coupon_discount' => 0, 'coupon' => null, 'message' => $validation['message']];
        }

        $totals = $this->calculateTotals($cart, $coupon);
        $cart->update([
            'discount_total' => $totals['coupon_discount'],
            'shipping_total' => $totals['shipping_total'],
            'tax_total' => $totals['tax_total'],
            'grand_total' => $totals['grand_total'],
        ]);

        return $totals + ['coupon' => $coupon];
    }

    public function validate(Coupon $coupon, Cart $cart, ?User $user = null): array
    {
        $cart->loadMissing('items.product');
        $subtotal = (float) $cart->items->sum('subtotal');

        if (! $coupon->is_active) {
            return $this->fail('This coupon is inactive.');
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return $this->fail('This coupon is not active yet.');
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return $this->fail('This coupon has expired.');
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return $this->fail('This coupon has reached its usage limit.');
        }

        if ($coupon->per_user_limit && $user) {
            $used = $coupon->usages()->where('user_id', $user->id)->count();
            if ($used >= $coupon->per_user_limit) {
                return $this->fail('You have already used this coupon.');
            }
        }

        if ($subtotal < (float) $coupon->minimum_order_amount) {
            return $this->fail('Add items worth Rs. '.number_format((float) $coupon->minimum_order_amount - $subtotal, 2).' more to use this coupon.');
        }

        if ($this->eligibleSubtotal($cart, $coupon) <= 0 && $coupon->type !== 'free_shipping') {
            return $this->fail('This coupon is not valid for the products in your cart.');
        }

        return ['valid' => true, 'message' => 'Coupon is valid.'];
    }

    public function œœœ(Cart $cart, Coupon $coupon): array
    {
        $base = $this->baseTotals($cart);
        $eligibleSubtotal = $this->eligibleSubtotal($cart, $coupon);
        $discount = 0.0;
        $shipping = $base['shipping_total'];

        if ($coupon->type === 'flat') {
            $discount = min((float) $coupon->value, $eligibleSubtotal);
        }

        if ($coupon->type === 'percentage') {
            $discount = $eligibleSubtotal * ((float) $coupon->value / 100);
            if ($coupon->maximum_discount_amount) {
                $discount = min($discount, (float) $coupon->maximum_discount_amount);
            }
        }

        if ($coupon->type === 'free_shipping') {
            $discount = 0;
            $shipping = 0;
        }

        $discount = round(max(0, $discount), 2);
        $grandTotal = max(0, $base['subtotal'] - $discount + $shipping + $base['tax_total']);

        return [
            'subtotal' => $base['subtotal'],
            'coupon_discount' => $discount,
            'shipping_total' => round($shipping, 2),
            'tax_total' => $base['tax_total'],
            'grand_total' => round($grandTotal, 2),
            'total_savings' => round($discount + $this->productSavings($cart), 2),
        ];
    }

    public function baseTotals(Cart $cart): array
    {
        $cart->loadMissing('items');
        $subtotal = round((float) $cart->items->sum('subtotal'), 2);
        $shipping = app(ShippingService::class)->calculate($cart);
        $tax = $this->calculateTax($subtotal);

        return [
            'subtotal' => $subtotal,
            'shipping_total' => round($shipping, 2),
            'tax_total' => round($tax, 2),
            'grand_total' => round($subtotal + $shipping + $tax, 2),
        ];
    }

    private function calculateTax(float $subtotal): float
    {
        $settings = AdminSetting::taxConfig();

        if (! $settings['tax_enabled'] || $subtotal <= 0 || $settings['tax_rate'] <= 0) {
            return 0.0;
        }

        return round($subtotal * ($settings['tax_rate'] / 100), 2);
    }

    private function eligibleSubtotal(Cart $cart, Coupon $coupon): float
    {
        $categoryIds = array_filter($coupon->applicable_category_ids ?? []);
        $productIds = array_filter($coupon->applicable_product_ids ?? []);
        $excludedIds = array_filter($coupon->excluded_product_ids ?? []);

        return (float) $cart->items->filter(function ($item) use ($categoryIds, $productIds, $excludedIds) {
            if (in_array($item->product_id, $excludedIds)) {
                return false;
            }

            if ($productIds && ! in_array($item->product_id, $productIds)) {
                return false;
            }

            if ($categoryIds && ! in_array($item->product?->category_id, $categoryIds)) {
                return false;
            }

            return true;
        })->sum('subtotal');
    }

    private function productSavings(Cart $cart): float
    {
        return (float) $cart->items->sum(function ($item) {
            if (! $item->original_price || (float) $item->original_price <= (float) $item->price) {
                return 0;
            }

            return ((float) $item->original_price - (float) $item->price) * (int) $item->quantity;
        });
    }

    private function fail(string $message): array
    {
        return ['valid' => false, 'message' => $message];
    }
}
