<?php

namespace App\Http\Controllers\Client;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class HomeController
{
    public function index()
    {
        $products = Schema::hasTable('products')
            ? Product::active()
                ->with(['category', 'labels', 'variants', 'attributeValues.attribute', 'attributeValues.attributeValue'])
                ->latest()
                ->get()
            : new Collection();
        $variantAttributeIds = $products
            ->flatMap(fn ($product) => $product->variants)
            ->flatMap(fn ($variant) => array_keys($variant->attributes ?: []))
            ->filter()
            ->unique()
            ->values();
        $variantValueIds = $products
            ->flatMap(fn ($product) => $product->variants)
            ->flatMap(fn ($variant) => array_values($variant->attributes ?: []))
            ->filter()
            ->unique()
            ->values();
        $variantAttributes = Schema::hasTable('attributes')
            ? Attribute::whereIn('id', $variantAttributeIds)->pluck('name', 'id')
            : new Collection();
        $variantValues = Schema::hasTable('attribute_values')
            ? AttributeValue::whereIn('id', $variantValueIds)->pluck('value', 'id')
            : new Collection();

        return view('client.home.home', compact('products', 'variantAttributes', 'variantValues'));
    }
}
