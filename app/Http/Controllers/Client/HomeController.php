<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\Review;
use App\Models\Banner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        $products = Schema::hasTable('products')
            ? Product::active()
                ->with([
                    'category',
                    'labels',
                    'variants',
                    'attributeValues.attribute',
                    'attributeValues.attributeValue'
                ])
                ->latest()
                ->get()
            : new Collection();

        /*
        |--------------------------------------------------------------------------
        | Variant Attribute IDs
        |--------------------------------------------------------------------------
        */

        $variantAttributeIds = $products
            ->flatMap(fn ($product) => $product->variants)
            ->flatMap(fn ($variant) => array_keys($variant->attributes ?: []))
            ->filter()
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Variant Value IDs
        |--------------------------------------------------------------------------
        */

        $variantValueIds = $products
            ->flatMap(fn ($product) => $product->variants)
            ->flatMap(fn ($variant) => array_values($variant->attributes ?: []))
            ->filter()
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Variant Attributes
        |--------------------------------------------------------------------------
        */

        $variantAttributes = Schema::hasTable('attributes')
            ? Attribute::whereIn('id', $variantAttributeIds)
                ->pluck('name', 'id')
            : new Collection();

        /*
        |--------------------------------------------------------------------------
        | Variant Values
        |--------------------------------------------------------------------------
        */

        $variantValues = Schema::hasTable('attribute_values')
            ? AttributeValue::whereIn('id', $variantValueIds)
                ->pluck('value', 'id')
            : new Collection();

        /*
        |--------------------------------------------------------------------------
        | Approved Reviews
        |--------------------------------------------------------------------------
        */

        $reviews = Review::with([
                'user',
                'product',
                'images'
            ])
            ->where('status', 'approved')
            ->latest()
            ->take(10)
            ->get();


     /*
        |--------------------------------------------------------------------------
        | Approved banners        
        |--------------------------------------------------------------------------
        */

              $homeSliders = Banner::where('is_active', 1)
            ->where('position', 'home_slider')
            ->orderBy('priority', 'asc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view('client.home.home', compact(
            'products',
            'variantAttributes',
            'variantValues',
            'reviews',
            'homeSliders'
        ));
    }
}