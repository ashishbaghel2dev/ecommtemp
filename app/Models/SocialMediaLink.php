<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialMediaLink extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'url',
        'icon',
        'priority',
        'is_active',
        'clicks'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: Only Active Links
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by Priority
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority');
    }

    /**
     * Auto full URL accessor (optional)
     */
    public function getUrlAttribute($value)
    {
        return $value;
    }

    /**
     * Increment click count
     */
    public function incrementClicks()
    {
        $this->increment('clicks');
    }
}
