<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'role',
        'type',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function productReviews()
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    public function shopProducts()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function shopOrders()
    {
        return $this->hasMany(\App\Models\ShopOrder::class, 'seller_id');
    }

    public function subscription()
    {
        return $this->hasOne(\App\Models\Subscription::class)->latestOfMany();
    }

    public function activeSubscription()
    {
        return $this->hasOne(\App\Models\Subscription::class)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function isAgent()
    {
        return $this->role == 2;
    }

    public function isSuperAdmin()
    {
        return $this->role == 1;
    }

    public function isOrderManager()
    {
        return $this->role == 3;
    }

    public function getPhotoAttribute()
    {
        return $this->image ?? 'uploads/thumbnail.jpg';
    }

    public function getShopNameAttribute()
    {
        return $this->name . "'s Shop";
    }
}