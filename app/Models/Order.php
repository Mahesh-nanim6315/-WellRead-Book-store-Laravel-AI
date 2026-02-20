<?php

namespace App\Models;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Coupon;



use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'coupon_code',
        'address_id',
        'payment_method',
        'payment_id',
        'payment_status',
        'status',
    ];

     public $timestamps = true;

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }



    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }
}
