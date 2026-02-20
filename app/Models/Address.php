<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'address_line',
        'city',
        'state',
        'pincode',
        'country',
        'is_default',
    ];

    /* ================= RELATIONS ================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /* ================= HELPERS ================= */

    public function markAsDefault()
    {
        // Remove default from other addresses
        self::where('user_id', $this->user_id)->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);
    }
}
