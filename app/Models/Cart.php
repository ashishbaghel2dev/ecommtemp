<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [

        'user_id',
        'session_id',
        'status',
        'type',
        'total_items',
        'total_quantity',
        'subtotal',
        'discount_total',
        'tax_total',
        'shipping_total',
        'grand_total',
        'coupon_code',
        'currency',
        'last_activity_at',
        'device_type',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [

        'last_activity_at' => 'datetime',
    ];

    /*
    |--------------------------------
    | Relationships
    |--------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /*
    |--------------------------------
    | Helpers
    |--------------------------------
    */

    public function isEmpty()
    {
        return $this->items()->count() === 0;
    }

    public function getTotalItems()
    {
        return $this->items()->sum('quantity');
    }

    public function refreshTotals()
    {
        $this->update([

            'total_items'     => $this->items()->count(),
            'total_quantity'  => $this->items()->sum('quantity'),
            'subtotal'        => $this->items()->sum('subtotal'),
            'discount_total'  => $this->items()->sum('discount_amount'),
            'tax_total'       => $this->items()->sum('tax_amount'),
            'grand_total'     => $this->items()->sum('total'),
            'last_activity_at'=> now()
        ]);
    }
}