<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopOrderItem extends Model
{
    protected $table = 'shop_order_items';

    protected $fillable = [
        'order_id',
        'item_type',
        'item_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'variation_name',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'order_id');
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
}
