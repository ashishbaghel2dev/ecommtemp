<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'image',
        'eyebrow',
        'title',
        'subtitle',
        'link',
        'button_text',
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
