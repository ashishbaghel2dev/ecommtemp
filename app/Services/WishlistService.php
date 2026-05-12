<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Cookie;

class WishlistService
{
    public function toggle(int $productId): bool
    {
        if (auth()->check()) {

            return $this->databaseWishlist($productId);
        }

        return $this->cookieWishlist($productId);
    }

    /*
    |--------------------------------------------------------------------------
    | Database Wishlist
    |--------------------------------------------------------------------------
    */

    private function databaseWishlist(int $productId): bool
    {
        $exists = Wishlist::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {

            Wishlist::query()
                ->where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->delete();

            return false;
        }

        Wishlist::create([
            'user_id'    => auth()->id(),
            'product_id' => $productId,
        ]);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Guest Wishlist
    |--------------------------------------------------------------------------
    */

    private function cookieWishlist(int $productId): bool
    {
        $wishlist = json_decode(
            Cookie::get('wishlist_products', '[]'),
            true
        );

        if (in_array($productId, $wishlist)) {

            $wishlist = array_diff(
                $wishlist,
                [$productId]
            );

            Cookie::queue(
                'wishlist_products',
                json_encode(array_values($wishlist)),
                60 * 24 * 30
            );

            return false;
        }

        array_unshift($wishlist, $productId);

        Cookie::queue(
            'wishlist_products',
            json_encode($wishlist),
            60 * 24 * 30
        );

        return true;
    }


    public function mergeGuestWishlist(): void
{
    if (!auth()->check()) {
        return;
    }

    $cookieWishlist = json_decode(
        Cookie::get('wishlist_products', '[]'),
        true
    );

    if (empty($cookieWishlist)) {
        return;
    }

    foreach ($cookieWishlist as $productId) {

        Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $productId,
        ]);

    }

    Cookie::queue(
        Cookie::forget('wishlist_products')
    );
}


}