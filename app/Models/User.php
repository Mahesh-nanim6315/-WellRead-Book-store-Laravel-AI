<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
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

public function getAvatarUrlAttribute(): string
{
    return $this->resolveImageUrl($this->avatar, 'images/default-avatar.png');
}

public function getCoverUrlAttribute(): string
{
    return $this->resolveImageUrl($this->cover, 'images/default-cover.jpg');
}

private function resolveImageUrl(?string $path, string $fallback): string
{
    if (! $path) {
        return asset($fallback);
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    $normalized = ltrim($path, '/');
    if (str_starts_with($normalized, 'uploads/')) {
        if (file_exists(public_path($normalized))) {
            return asset($normalized);
        }
        return asset($fallback);
    }

    if (str_starts_with($normalized, 'storage/')) {
        $normalized = substr($normalized, strlen('storage/'));
    }

    $publicFile = public_path('storage/' . $normalized);
    if (Storage::disk('public')->exists($normalized) && file_exists($publicFile)) {
        return asset('storage/' . $normalized);
    }

    return asset($fallback);
}




}
