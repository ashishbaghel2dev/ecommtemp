<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
        'sort_order',
        'is_active',
        'color_code',
        'image',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Parent Attribute (Color, Size)
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}