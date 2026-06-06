<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeCarouselImage extends Model
{
    protected $fillable = [
        'carousel_key',
        'title',
        'image',
    ];
}
