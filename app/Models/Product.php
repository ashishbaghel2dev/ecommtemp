<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'subcategory_id',
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

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    // simple product attributes
    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    // variants (for configurable)
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // images (multiple)
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }


    public function labels()
{
    return $this->belongsToMany(ProductLabel::class);
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

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    // final price logic
    public function getFinalPriceAttribute()
    {
        if ($this->sale_price &&
            now()->between($this->sale_start, $this->sale_end)) {
            return $this->sale_price;
        }

        return $this->price;
    }
}