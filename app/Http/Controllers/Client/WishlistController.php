<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\WishlistService;

class WishlistController 
{
    public function toggle(
        Product $product,
        WishlistService $wishlistService
    ) {

        $added = $wishlistService->toggle(
            $product->id
        );

        return response()->json([
            'success' => true,
            'added'   => $added,
        ]);
    }
}