<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// App\Models\Review.php
class Review extends Model
{
    protected $fillable = ['user_id', 'book_id', 'rating', 'comment', 'is_approved'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

