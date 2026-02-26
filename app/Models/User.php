<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'plan',
        'avatar',
        'cover',
        'billing_cycle',
        'plan_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'plan_expires_at' => 'datetime',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isAdmin()
{
    return $this->role === 'admin';
}

public function isManager()
{
    return $this->role === 'manager';
}

public function isStaff()
{
    return $this->role === 'staff';
}

public function hasActiveSubscription()
{
    return $this->subscribed('default') || (
        $this->plan !== 'free' &&
        $this->plan_expires_at &&
        now()->lt($this->plan_expires_at)
    );
}

public function canAccessBook(Book $book): bool
{
    if (! $book->is_premium) {
        return true;
    }

    return $this->hasActiveSubscription();
}

public function chatSessions()
{
    return $this->hasMany(ChatSession::class);
}




}
