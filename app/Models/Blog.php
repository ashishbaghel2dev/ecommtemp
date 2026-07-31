<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'slug',
    'category',
    'image',
    'image_alt',
    'description',
    'meta_keyword',
    'meta_description',
    'meta_tags',
    'schema_type',
    'schema_markup',
    'faq_schema',
    'publish_status',
    'status',
    'sort_order',
])]
class Blog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
