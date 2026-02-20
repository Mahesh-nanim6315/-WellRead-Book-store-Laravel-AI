<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UserLibrary extends Model
{
    protected $table = 'user_library'; 
    
    protected $fillable = [
        'user_id',
        'book_id',
        'format',       // ebook | audio
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /* ================= RELATIONS ================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /* ================= HELPERS ================= */

    public function isExpired()
    {
        return $this->expires_at && Carbon::now()->gt($this->expires_at);
    }
}
