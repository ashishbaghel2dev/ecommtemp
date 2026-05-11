<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;


class ProductUiController 
{
   public function show(Product $product)
{
    $recentlyViewed = json_decode(
        Cookie::get('recently_viewed_products', '[]'),
        true
    );

    $recentlyViewed = array_diff($recentlyViewed, [$product->id]);

    array_unshift($recentlyViewed, $product->id);

    $recentlyViewed = array_slice($recentlyViewed, 0, 10);

    Cookie::queue(
        'recently_viewed_products',
        json_encode($recentlyViewed),
        60 * 24 * 30
    );

    return view('client.pages.products.products', compact('product'));
}


}
