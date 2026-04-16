<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopCartItem extends Model
{
    protected $table = 'shop_cart_items';

    protected $fillable = [
        'user_id',
        'product_id',
        'item_type',
        'item_id',
        'quantity',
        'variation_ids',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'item_id');
    }

    public function getItemProductAttribute()
    {
        if ($this->item_type === 'inventory') {
            return $this->inventoryItem;
        }
        return $this->product;
    }

    public function getVariationsAttribute()
    {
        if ($this->item_type === 'inventory') {
            return collect([]);
        }
        
        if ($this->variation_ids) {
            $ids = json_decode($this->variation_ids, true);
            if (is_array($ids) && count($ids) > 0) {
                return ProductVariation::whereIn('id', $ids)->get();
            }
        }
        return collect([]);
    }

    public function getVariationNamesAttribute()
    {
        if ($this->item_type === 'inventory') {
            return null;
        }
        
        $variations = $this->variations;
        if ($variations->isEmpty()) {
            return null;
        }
        return $variations->pluck('value')->implode(', ');
    }

    public function getUnitPriceAttribute()
    {
        $item = $this->itemProduct;
        
        if (!$item) {
            return 0;
        }
        
        if ($this->item_type === 'inventory') {
            return $item->current_price;
        }
        
        $basePrice = $item->current_price;
        $variations = $this->variations;
        if ($variations->isNotEmpty()) {
            $modifier = $variations->sum('price_modifier');
            return $basePrice + $modifier;
        }
        return $basePrice;
    }

    public function getSubtotalAttribute()
    {
        return $this->unit_price * $this->quantity;
    }
}
