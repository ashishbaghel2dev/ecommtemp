<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

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
    | 🔗 Relationships
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

    // Recursive children (tree structure)
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    // Products in this category
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔍 Scopes
    |--------------------------------------------------------------------------
    */

    // Active categories only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Show on homepage
    public function scopeHome($query)
    {
        return $query->where('show_on_home', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ⚙️ Helpers
    |--------------------------------------------------------------------------
    */

    // Check if category is parent
    public function isParent()
    {
        return is_null($this->parent_id);
    }

    // Check if category has children
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    public function subcategories()
{
    return $this->hasMany(Subcategory::class);
}

}