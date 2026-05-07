<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'subcategory_id',
        'name',
        'code',
        'type',
        'is_required',
        'is_filterable',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    // Attribute Values (Red, Blue, S, M)
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
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
    
}