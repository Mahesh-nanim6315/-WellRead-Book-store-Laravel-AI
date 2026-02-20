<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'book_id',
        'format',   
        'price',
        'quantity',
    ];



    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

   

    public function subtotal()
    {
        return $this->price * $this->quantity;
    }
}
