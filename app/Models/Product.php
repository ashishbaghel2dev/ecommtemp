<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'discount_price',
        'sale_price',
        'sale_start',
        'sale_end',
        'stock',
        'manage_stock',
        'in_stock',
        'image',
        'is_featured',
        'is_active',
        'meta_title',
        'meta_description',
        'type',
        'view_count',
    ];

    protected $casts = [
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sale_start' => 'datetime',
        'sale_end' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Product Attributes
    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    // Product Variants
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Multiple Images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Labels
    public function labels()
    {
        return $this->belongsToMany(ProductLabel::class, 'product_label_product');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function wishlists()
{
    return $this->hasMany(Wishlist::class);
}

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    // Final Price
    public function getFinalPriceAttribute()
    {
        if (
            $this->sale_price &&
            $this->sale_start &&
            $this->sale_end &&
            now()->between($this->sale_start, $this->sale_end)
        ) {
            return $this->sale_price;
        }

        return $this->discount_price ?: $this->price;
    }

    public function reviews()
{
    return $this->hasMany(Review::class);
}
}
