<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'user_id',
        'title',
        'rating',
        'comment',
        'admin_reply',
        'is_verified_purchase',
        'helpful_votes',
        'unhelpful_votes',
        'status',
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'rating' => 'integer',
        'helpful_votes' => 'integer',
        'unhelpful_votes' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(ReviewImage::class);
    }

    public function reports()
    {
        return $this->hasMany(ReviewReport::class);
    }

    public function votes()
    {
        return $this->hasMany(ReviewVote::class);
    }
}