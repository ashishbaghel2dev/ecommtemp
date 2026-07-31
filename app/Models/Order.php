<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'cart_id',
        'coupon_id',
        'order_number',
        'paid_at',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'shipping_address_snapshot',
        'estimated_delivery_date',
        'payment_method',
        'payment_status',
        'payment_meta',
        'status',
        'subtotal',
        'discount_total',
        'coupon_code',
        'coupon_discount',
        'tax_total',
        'shipping_total',
        'total_savings',
        'grand_total',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'total_savings' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'shipping_address_snapshot' => 'array',
        'payment_meta' => 'array',
        'estimated_delivery_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
