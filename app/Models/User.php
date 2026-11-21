<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_FARMER = 'farmer';

    public const ROLE_BUYER = 'buyer';

    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'consent_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'consent_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function farmerProfile(): HasOne
    {
        return $this->hasOne(FarmerProfile::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'farmer_id');
    }

    public function farmerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'farmer_id');
    }

    public function buyerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function isFarmer(): bool
    {
        return $this->role === self::ROLE_FARMER;
    }

    public function isBuyer(): bool
    {
        return $this->role === self::ROLE_BUYER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isVerifiedFarmer(): bool
    {
        return $this->isFarmer() && $this->farmerProfile?->isVerified();
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_FARMER => 'farmer.dashboard',
            self::ROLE_ADMIN => 'admin.dashboard',
            default => 'buyer.dashboard',
        };
    }
}
