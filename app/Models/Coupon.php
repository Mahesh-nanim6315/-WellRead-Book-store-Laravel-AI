<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'usage_limit',
        'used_count',
        'expires_at'
    ];

    protected $dates = ['expires_at'];

    // Check if coupon is valid
    public function isValid()
    {
        return
            (!$this->expires_at || $this->expires_at->isFuture()) &&
            ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    // Calculate discount
    public function calculateDiscount($amount)
    {
        if ($this->type === 'percentage') {
            return round(($amount * $this->value) / 100);
        }

        if ($this->type === 'fixed') {
            return min($this->value, $amount);
        }

        return 0;
    }
}
