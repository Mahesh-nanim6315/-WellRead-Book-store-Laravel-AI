<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
    ];

    /* ================= RELATIONS ================= */

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ================= HELPERS ================= */

    public function total()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }
}
