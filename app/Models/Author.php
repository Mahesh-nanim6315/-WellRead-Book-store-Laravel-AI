<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
  protected $fillable = ['name', 'image', 'bio'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}