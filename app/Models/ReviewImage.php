<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    protected $fillable = [
        'review_id',
        'image_path',
        'alt_text',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}