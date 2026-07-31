<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\Cart;

class ShippingService
{
    public function settings(): array
    {
        return AdminSetting::shippingConfig();
    }

    public function calculate(Cart $cart, ?string $paymentMethod = null): float
    {
        $cart->loadMissing('items.product');

        $settings = $this->settings();
        $subtotal = round((float) $cart->items->sum('subtotal'), 2);

        if (! $settings['shipping_enabled'] || $subtotal <= 0) {
            return 0.0;
        }

        if (! $this->appliesToPayment($settings, $paymentMethod)) {
            return 0.0;
        }

        if ((float) $settings['shipping_free_above'] > 0 && $subtotal >= (float) $settings['shipping_free_above']) {
            return 0.0;
        }

        if (! $this->cartHasEligibleProducts($cart, $settings)) {
            return 0.0;
        }

        return round((float) $settings['shipping_amount'], 2);
    }

    public function appliesToPayment(array $settings, ?string $paymentMethod): bool
    {
        if ($paymentMethod === 'cod') {
            return (bool) $settings['shipping_apply_cod'];
        }

        if ($paymentMethod === 'online') {
            return (bool) $settings['shipping_apply_online'];
        }

        return (bool) $settings['shipping_apply_online'] || (bool) $settings['shipping_apply_cod'];
    }

    private function cartHasEligibleProducts(Cart $cart, array $settings): bool
    {
        $productIds = $this->productIds($settings['shipping_product_ids'] ?? '');

        if ($productIds === []) {
            return true;
        }

        return $cart->items->contains(fn ($item) => in_array((int) $item->product_id, $productIds, true));
    }

    private function productIds(?string $value): array
    {
        return array_values(array_unique(array_filter(array_map(
            'intval',
            preg_split('/[\s,]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY)
        ))));
    }
}
