<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Cookie;

class WishlistService
{
    public const COOKIE_KEY = 'wishlist_products';

    public function toggle(int $productId, ?int $variantId = null, array $pavIds = [], ?array $meta = null): bool
    {
        $pavIds = $this->normalizePavIds($pavIds);
        $signature = $this->attributeSignatureFromPavIds($pavIds);

        if (auth()->check()) {
            return $this->databaseWishlist($productId, $variantId, $signature, $meta);
        }

        return $this->cookieWishlist($productId, $variantId, $signature, $pavIds, $meta);
    }

    public function attributeSignatureFromPavIds(array $ids): string
    {
        $ids = $this->normalizePavIds($ids);

        return $ids === [] ? '' : hash('sha256', implode(',', $ids));
    }

    public function normalizePavIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        sort($ids);

        return $ids;
    }

    private function databaseWishlist(int $productId, ?int $variantId, string $signature, ?array $meta): bool
    {
        $query = Wishlist::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->where('attribute_signature', $signature);

        $variantId
            ? $query->where('product_variant_id', $variantId)
            : $query->whereNull('product_variant_id');

        $existing = $query->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'attribute_signature' => $signature,
            'meta' => $meta,
        ]);

        return true;
    }

    private function cookieWishlist(int $productId, ?int $variantId, string $signature, array $pavIds, ?array $meta): bool
    {
        $wishlist = $this->cookieItems();
        $key = $this->entryKey($productId, $variantId, $signature);

        $exists = collect($wishlist)->contains(fn ($item) => ($item['key'] ?? '') === $key);

        if ($exists) {
            $wishlist = array_values(array_filter(
                $wishlist,
                fn ($item) => ($item['key'] ?? '') !== $key
            ));

            Cookie::queue(self::COOKIE_KEY, json_encode($wishlist), 60 * 24 * 30);

            return false;
        }

        array_unshift($wishlist, [
            'key' => $key,
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'attribute_signature' => $signature,
            'pav_ids' => $pavIds,
            'meta' => $meta,
        ]);

        Cookie::queue(self::COOKIE_KEY, json_encode($wishlist), 60 * 24 * 30);

        return true;
    }

    public function cookieItems(): array
    {
        $raw = json_decode(Cookie::get(self::COOKIE_KEY, '[]'), true);

        if (! is_array($raw)) {
            return [];
        }

        return collect($raw)
            ->filter()
            ->map(function ($item) {
                if (is_numeric($item)) {
                    $productId = (int) $item;

                    return [
                        'key' => $this->entryKey($productId, null, ''),
                        'product_id' => $productId,
                        'product_variant_id' => null,
                        'attribute_signature' => '',
                        'pav_ids' => [],
                        'meta' => null,
                    ];
                }

                if (! is_array($item) || empty($item['product_id'])) {
                    return null;
                }

                $productId = (int) $item['product_id'];
                $variantId = isset($item['product_variant_id']) ? (int) $item['product_variant_id'] : null;
                $signature = (string) ($item['attribute_signature'] ?? '');

                return [
                    'key' => $item['key'] ?? $this->entryKey($productId, $variantId, $signature),
                    'product_id' => $productId,
                    'product_variant_id' => $variantId,
                    'attribute_signature' => $signature,
                    'pav_ids' => isset($item['pav_ids']) && is_array($item['pav_ids'])
                        ? $this->normalizePavIds($item['pav_ids'])
                        : [],
                    'meta' => $item['meta'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public function entryKey(int $productId, ?int $variantId, string $signature): string
    {
        return $productId . ':' . ($variantId ?: 'base') . ':' . ($signature ?: 'base');
    }

    public function mergeGuestWishlist(): void
    {
        if (! auth()->check()) {
            return;
        }

        foreach ($this->cookieItems() as $item) {
            Wishlist::firstOrCreate(
                [
                    'user_id' => auth()->id(),
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'attribute_signature' => $item['attribute_signature'],
                ],
                [
                    'meta' => $item['meta'],
                ]
            );
        }

        Cookie::queue(Cookie::forget(self::COOKIE_KEY));
    }
}
