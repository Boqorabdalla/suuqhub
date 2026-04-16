<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Inventory extends Model
{
    protected $fillable = [
        'listing_id',
        'type',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock_quantity',
        'sku',
        'featured_image',
        'availability',
        'is_featured',
        'track_stock',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name);
            }
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(InventoryImage::class)->orderBy('sort_order');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(InventoryVariation::class);
    }

    public function getCurrentPriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getIsInStockAttribute()
    {
        if ($this->variations()->exists()) {
            return $this->variations()->where('stock_quantity', '>', 0)->exists();
        }
        return $this->stock_quantity > 0;
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeForListing($query, $type, $listingId)
    {
        return $query->where('type', $type)->where('listing_id', $listingId);
    }
}
