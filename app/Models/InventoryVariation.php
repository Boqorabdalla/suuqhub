<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryVariation extends Model
{
    protected $fillable = [
        'inventory_id',
        'name',
        'value',
        'price_modifier',
        'stock_quantity',
        'status',
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
