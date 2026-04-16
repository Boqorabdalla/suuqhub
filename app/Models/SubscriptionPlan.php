<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_period',
        'duration_days',
        'tier',
        'max_listings',
        'max_products',
        'has_analytics',
        'has_custom_branding',
        'has_priority_support',
        'has_api_access',
        'commission_rate',
        'is_featured',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'has_analytics' => 'boolean',
        'has_custom_branding' => 'boolean',
        'has_priority_support' => 'boolean',
        'has_api_access' => 'boolean',
        'is_featured' => 'boolean',
        'status' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'plan_id');
    }

    public function activeSubscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrderBySort($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    public function getFeaturesAttribute()
    {
        $features = [];
        
        if ($this->max_listings > 0) {
            $features[] = "{$this->max_listings} Listings";
        }
        if ($this->max_products > 0) {
            $features[] = "{$this->max_products} Products";
        }
        if ($this->has_analytics) {
            $features[] = "Analytics Dashboard";
        }
        if ($this->has_custom_branding) {
            $features[] = "Custom Branding";
        }
        if ($this->has_priority_support) {
            $features[] = "Priority Support";
        }
        if ($this->has_api_access) {
            $features[] = "API Access";
        }
        $features[] = "{$this->commission_rate}% Commission";
        
        return $features;
    }

    public static function getTierBadgeClass($tier)
    {
        return match($tier) {
            'basic' => 'bg-secondary',
            'standard' => 'bg-primary',
            'premium' => 'bg-warning text-dark',
            'enterprise' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
