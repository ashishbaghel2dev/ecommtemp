<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Fillable Fields
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'description',
        'image',
        'banner',
        'show_on_home',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'is_active'    => 'boolean',
        'show_on_home' => 'boolean',
        'sort_order'   => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Parent Category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Child Categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Products Under Category
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    // Active Categories
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Show On Homepage
    public function scopeHome($query)
    {
        return $query->where('show_on_home', true);
    }

    // Parent Categories Only
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    // Child Categories Only
    public function scopeChild($query)
    {
        return $query->whereNotNull('parent_id');
    }

    // Latest Categories
    public function scopeLatestFirst($query)
    {
        return $query->latest();
    }

    // Sort By sort_order
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    // Check If Category Has Products
    public function hasProducts()
    {
        return $this->products()->exists();
    }

    // Count Products
    public function productsCount()
    {
        return $this->products()->count();
    }

    // Check If Parent Category
    public function isParent()
    {
        return is_null($this->parent_id);
    }

    // Check If Has Child Categories
    public function hasChildren()
    {
        return $this->children()->exists();
    }
}