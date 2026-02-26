<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Genre;

class Book extends Model
{
 protected $fillable = [
    'name',
    'description',
    'language',
    'image',

    'author_id',
    'category_id',
    'genre_id',

    'has_ebook',
    'ebook_price',
    'ebook_pdf',
    'ebook_pages',

    'has_audio',
    'audio_price',
    'audio_file',
    'audio_minutes',

    'has_paperback',
    'paperback_price',
    'paperback_pages',
    'stock',
    'is_premium',

    'price',
    'embedding',
];

    protected $casts = [
        'has_ebook' => 'boolean',
        'has_audio' => 'boolean',
        'has_paperback' => 'boolean',
        'is_premium' => 'boolean',
    ];


    // keep this as-is
    public $timestamps = false;

    // existing relation
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // NEW relations
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

       public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}
