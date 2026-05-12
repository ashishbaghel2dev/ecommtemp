<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [

        'cart_id',
        'product_id',
        'product_variant_id',
        'attribute_signature',
        'product_name',
        'product_sku',
        'product_image',
        'quantity',
        'price',
        'original_price',
        'discount_amount',
        'tax_amount',
        'subtotal',
        'total',
        'is_selected',
        'is_available',
        'stock_at_time',
        'meta',
    ];

    protected $casts = [

        'is_selected'   => 'boolean',
        'is_available'  => 'boolean',
        'meta'          => 'array',
    ];

    /*
    |--------------------------------
    | Relationships
    |--------------------------------
    */

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------
    | Helpers
    |--------------------------------
    */

    public function calculate()
    {
        $this->subtotal = $this->price * $this->quantity;

        $this->total = ($this->subtotal - $this->discount_amount) + $this->tax_amount;

        return $this;
    }

    public function incrementQty($qty = 1)
    {
        $this->quantity += $qty;

        return $this->calculate();
    }

    public function setQty($qty)
    {
        $this->quantity = $qty;

        return $this->calculate();
    }

    public function decrementQty($qty = 1)
    {
        $this->quantity = max(0, $this->quantity - $qty); // Ensure quantity doesn't go below 0

        return $this->calculate();
    }

}