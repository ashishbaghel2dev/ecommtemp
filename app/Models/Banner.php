<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'image',
        'link',
        'is_active',
        'priority',
        'position'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: Only Active Banners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: By Position
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    
}

