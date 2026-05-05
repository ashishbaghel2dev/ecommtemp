<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'stock',
        'in_stock',
        'manage_stock',
        'attributes',
        'image',
        'is_active',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getFinalPriceAttribute()
    {
        if ($this->sale_price) {
            return $this->sale_price;
        }

        return $this->price ?? $this->product->price;
    }
}