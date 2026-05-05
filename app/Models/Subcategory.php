<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'banner',
        'is_active',
        'show_on_home',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Parent Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Products under subcategory
    public function products()
    {
        return $this->hasMany(Product::class);
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

    public function scopeHome($query)
    {
        return $query->where('show_on_home', true);
    }
    public function attributes()
{
    return $this->hasMany(Attribute::class);
}

}